<?php

namespace App\Http\Controllers;

use App\Libraries\Paygate\Paygate;
use App\Libraries\Result\Result;
use App\Models\Payment;
use App\Models\ProgrammeRequirement;
use App\Models\StudentSemesterGPA;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Models\CourseManagement;
use App\Models\Transaction;
use App\Models\TestApplicant;
use App\Models\User;
use App\Models\Partner;
use App\Models\Student;
use App\Models\CourseRegistration;
use App\Models\GradeScale;


use App\Libraries\Bandwidth\Bandwidth;
use App\Libraries\Google\Google;

use App\Http\Controllers\PaymentController;


use Mail;
use Log;


class CronController extends Controller
{
    //

    public function changeCourseManagementPasscode(Request $request)
    {
        $globalData = $request->input('global_data');

        $sessionSettings = $globalData->sessionSettings ?? collect();
        $resultProcessStatus = $globalData->examSetting->result_processing_status ?? null;

        if ($sessionSettings->isEmpty()) {
            return $this->dataResponse('No academic session settings found.', null, 'error');
        }

        $updatedCount = 0;

        foreach ($sessionSettings as $programmeCategoryId => $sessionSetting) {
            $academicSession = $sessionSetting->academic_session ?? null;

            if (!$academicSession) {
                continue;
            }

            $courseManagements = CourseManagement::where('academic_session', $academicSession)
                ->where('programme_category_id', $programmeCategoryId)
                ->get();

            if ($courseManagements->isEmpty()) {
                continue;
            }

            foreach ($courseManagements as $courseManagement) {
                $courseManagement->passcode = $this->generateRandomString();
                $courseManagement->save();
                $updatedCount++;
            }
        }

        if ($updatedCount === 0) {
            return $this->dataResponse('No passcodes updated. No course assignments found.', null, 'error');
        }

        return $this->dataResponse("Passcodes updated for {$updatedCount} courses.", null);
    }


    public function deletePendingTransactions(){

        $transactions = Transaction::where('status', null)
                                    ->where('payment_method', '!=', 'Manual/BankTransfer')
                                    ->where('payment_method', '!=', null)
                                    ->orderBy('id', 'desc')
                                    ->take(10)
                                    ->get();

        if (!$transactions) {
            return $this->dataResponse('No pending transactions found that can be deleted.', null);
        }

        // $deletedCount = $transactions->each->forceDelete();

        foreach ($transactions as $transaction) {
            
            $paymentReference = $transaction->reference;
            

            $paymentController = new PaymentController;

            $paymentController->upperlinkVerifyPayment($paymentReference);
        }

        return $this->dataResponse('Pending transactions deleted successfully.', null);
    }

    public function exportDatabase(){
        $backupDir = public_path('backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $fileName = 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $exportPath = $backupDir . '/' . $fileName;

        $databaseName = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        exec("mysqldump --user={$username} --password={$password} {$databaseName} > {$exportPath}");

        if (!file_exists($exportPath)) {
            return response()->json(['error' => 'Failed to create the backup file.'], 500);
        }

        if(env('SEND_MAIL')){
            Mail::send([], [], function ($message) use ($exportPath, $fileName) {
                $message->to(env('BACKUP_EMAIL'))
                    ->subject('Database Backup ' . date('Y-m-d H:i:s'))
                    ->attach($exportPath, ['as' => $fileName]);
            });
        }

        unlink($exportPath);

        return response()->json(['message' => 'Database exported and email sent successfully.']);
    }

    public function updateReferrers(){
        $testApplicants = TestApplicant::where('referrer', '!=', null)->get();
        foreach ($testApplicants as $testApplicant){
            $referrer = $testApplicant->referrer;
            $email = $testApplicant->email;

            $user = User::where('email', $email)->first();
            $partnerId = null;

            
            $isExistPartner = Partner::where('referral_code', $referrer)->first();
            if($isExistPartner){
                $partnerId = $isExistPartner->id;
            }

            if($user){
                $user->referrer = $referrer;
                $user->partner_id = $partnerId;
                $user->save();
            }
        }

        return response()->json(['message' => 'record updated.']);
    
    }

    public function massBandwidthCreation() {
        $students = Student::all();
        $bandwidthAmount = 32212254720;
    
        foreach($students as $student) {
            if($this->checkNewStudentStatus($student)) {
                $username = $student->bandwidth_username;
    
                if(!empty($username)) {
                    $accessCode = $student->passcode;
                    $firstName = $student->applicant->othernames;
    
                    $userData = new \stdClass();
                    $userData->username = $username;
                    $userData->password =  $accessCode;
                    $userData->firstname = $firstName;
                    $userData->lastname = $student->applicant->lastname;
                    $userData->phone = $student->applicant->phone_number;
                    $userData->address = $student->applicant->address;
    
                    $bandwidth = new Bandwidth();
                    $createStudentBandwidthRecord = $bandwidth->createUser($userData);
    
                    if (isset($createStudentBandwidthRecord->status) && $createStudentBandwidthRecord->status === "success") {
                        $creditStudent = $bandwidth->addToDataBalance($username, $bandwidthAmount);
                        Log::info("Student Bandwidth Credited: ". json_encode($creditStudent));

                    }
                }
            }
        }
    
        return response()->json(['message' => 'record updated.']);
    }

    public function updateGrades(){
        $courseId = 867;
        $academicSession = '2023/2024';

        $studentRegistrations = CourseRegistration::where([
            'course_id' => $courseId,
            'academic_session' => $academicSession
        ])->get();

        foreach($studentRegistrations as $studentRegistration){
            $totalScore = $studentRegistration->total;
            $grading = GradeScale::computeGrade($totalScore);
            $grade = $grading->grade;
            $points = $grading->point;
    
            // $courseCode = $studentRegistration->course_code;
    
            // if (strpos($courseCode, 'NSC') !== false && $student->programme_id == 15) {
            //     if($totalScore < 50){
            //         $grade = 'F';
            //         $points = 0;
            //     }
            // }
            $studentRegistration->grade = $grade;
            $studentRegistration->points = $studentRegistration->course_credit_unit * $points;

            $studentRegistration->save();
        }

        return response()->json(['message' => 'record updated.']);
    }


    public function massEmailCreation() {
        $students = Student::orderBy('id', 'DESC')->get();
    
        foreach($students as $student) {
            $accessCode = $student->applicant->passcode;
            $studentEmail
             = $student->email;

            if($student->is_active) {
                $google = new Google();
                $createStudentEmail = $google->createUser($studentEmail, $student->applicant->othernames, $student->applicant->lastname, $accessCode, env('GOOGLE_STUDENT_GROUP'));
            }
        }
    
        return response()->json(['message' => 'record updated.']);
    }
    

    public static function populateSemesterRecords($student){

        // Fetch all registrations with distinct academic session, semester, and level_id
       $registrations = CourseRegistration::where('student_id', $student->id)
        ->where('result_approval_id', 1)
        ->select('academic_session', 'semester', 'level_id')
        ->distinct()
        ->orderBy('level_id') // Order by level_id in ascending order
        ->get();
    
        // Check if the $registrations collection is empty
        if ($registrations->isEmpty()) {
            // Log the empty case if necessary
            Log::info('No registrations found for student: ' . $student->id);
            return; // Exit the function if there are no records
        }
    
        // Loop through each registration record to populate semester records
        foreach ($registrations as $record) {
            $academicSession = $record->academic_session;
            $semester = $record->semester;
            $levelName = $record->level_id * 100 . " Level";
            $semesterName = $semester == 1 ? "Harmattan Semester" : "Rain Semester";
    
            // Fetch GPA for the student for the particular academic session and semester
            $gpa = Result::getPresentGPA($student, $academicSession, $semester);

            // Create or update the StudentSemesterGPA record
            StudentSemesterGPA::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'session' => $academicSession,
                    'semester' => $semesterName,
                    'level' => $levelName,
                ],
                [
                    'gpa' => $gpa,
                ]
            );
        }
    }

    public function getSemesterGPA() {
        $students = Student::orderBy('id', 'DESC')->get();

        set_time_limit(600);

    
        foreach($students as $student) {
            $this::populateSemesterRecords($student);
        }
    
        return response()->json(['message' => 'record updated.']);
    }



    public function updateStudentGrade()
    {
        set_time_limit(600);

        $registrations = CourseRegistration::where('academic_session', '2024/2025')->whereNotNull('total')
            ->get();

        $updatedRecords = [];
        $fiftyPassAffected = [];

        foreach ($registrations as $studentRegistration) {
            $totalScore = $studentRegistration->total;

            if ($totalScore > 0) {
                $student = Student::where('id', $studentRegistration->student_id)->where('programme_id', 14)->first();
                $courseCode = $studentRegistration->course_code;

                $grading = GradeScale::computeGrade($totalScore);
                $grade = $grading->grade;
                $points = $grading->point;

                $requiredPassMark = 40; // Default
                $fiftyPassTriggered = false;

                if ($student) {
                    $studentProgrammeRequirement = ProgrammeRequirement::where('programme_id', $student->programme_id)
                        ->where('level_id', $student->level_id)
                        ->first();

                    if ($studentProgrammeRequirement) {
                        $additionalCriteria = json_decode($studentProgrammeRequirement->additional_criteria, true);

                        $fiftyPassSetting = $additionalCriteria['course_code_50_pass'] ?? null;

                        if (
                            $fiftyPassSetting &&
                            isset($fiftyPassSetting['enabled']) && $fiftyPassSetting['enabled'] &&
                            isset($fiftyPassSetting['prefixes']) && is_array($fiftyPassSetting['prefixes'])
                        ) {
                            foreach ($fiftyPassSetting['prefixes'] as $prefix) {
                                if (stripos($courseCode, $prefix) === 0) {
                                    $requiredPassMark = 50;
                                    $fiftyPassTriggered = true;

                                    $fiftyPassAffected[] = [
                                        'student_id' => $student->id,
                                        'level_id' => $student->level_id,
                                        'programme_id' => $student->programme_id,
                                        'course_code' => $courseCode,
                                        'total' => $totalScore,
                                        'new_required_pass_mark' => 50,
                                    ];
                                    break;
                                }
                            }
                        }
                    }

                    // Re-grade if student didn't meet pass mark
                    if ($totalScore < $requiredPassMark) {
                        $grade = 'F';
                        $points = 0;
                    }

                    $calculatedPoints = $studentRegistration->course_credit_unit * $points;

                    // Only update if something changed
                    if (
                        $studentRegistration->grade !== $grade ||
                        $studentRegistration->points !== $calculatedPoints
                    ) {
                        $studentRegistration->grade = $grade;
                        $studentRegistration->points = $calculatedPoints;
                        $studentRegistration->save();

                        $updatedRecords[] = [
                            'student_id' => $studentRegistration->student_id,
                            'course_code' => $courseCode,
                            'total' => $totalScore,
                            'grade' => $grade,
                            'points' => $calculatedPoints,
                            'required_pass_mark' => $requiredPassMark,
                        ];
                    }
                }
            }
        }

        if (!empty($fiftyPassAffected)) {
            Log::info('Records affected by course_code_50_pass rule:', $updatedRecords);
        }

        return response()->json([
            'message' => 'Grade update completed.',
            'updated_count' => count($updatedRecords),
            '50_pass_affected_count' => count($fiftyPassAffected),
            'sample_updates' => array_slice($updatedRecords, 0, 5),
            'sample_50_pass_affected' => array_slice($fiftyPassAffected, 0, 5),
        ]);
    }

    public function checkSummerCourseRegistration($transactionID){
        $transaction = Transaction::find($transactionID);

        if($transaction && $transaction->status == 1 && $transaction->is_used == 0){
            return $creditStudent = $this->creditStudentSummerCourseReg($transaction);
        }
    }

    public function checkApplicationRegistration($transactionID){
        $transaction = Transaction::find($transactionID);
        if($transaction && $transaction->status == 1){

            $upperLinkPayGate = new PayGate();
            $paymentDetails =$upperLinkPayGate->verifyTransaction($transaction->reference);

            $data = $paymentDetails['meta'];
            $paymentData = json_decode($data, true);

            $paymentId = $paymentData['payment_id'];
            $payment = Payment::where('id', $paymentId)->first();
            $paymentType = $payment->type;

            if($paymentType == Payment::PAYMENT_TYPE_GENERAL_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                $applicantData = $paymentDetails;
                return $this->createApplicant($applicantData);
                
            }
        }
    }

}

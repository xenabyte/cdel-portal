<?php

namespace App\Http\Controllers;

use App\Libraries\Result\Result;
use App\Models\ProgrammeRequirement;
use App\Models\StudentSemesterGPA;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

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


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class CronController extends Controller
{
    //

    public function changeCourseManagementPasscode(Request $request){

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];
        $resultProcessStatus = $globalData->examSetting['result_processing_status'];

        $courseManagements = CourseManagement::where([
            'academic_session' => $academicSession
        ])->get();

        if(!$courseManagements){
            return $this->dataResponse('courses have not been assigned to lectures', null, 'error');
        }
        
        foreach($courseManagements as $courseManagement){
            $courseManagement->passcode = $this->generateRandomString();
            $courseManagement->save();
        }

        return $this->dataResponse('Passcode Updated', null);

    }


    public function deletePendingTransactions(){

        $transactions = Transaction::where('status', null)
                                    ->where('payment_method', '!=', 'Manual/BankTransfer')
                                    ->where('payment_method', '!=', null)
                                    ->get();

        if (!$transactions) {
            return $this->dataResponse('No pending transactions found that can be deleted.', null);
        }

        // $deletedCount = $transactions->each->forceDelete();

        foreach ($transactions as $transaction) {
            
            $paymentReference = $transaction->reference;
            

            $paymentController = new PaymentController;

            return $paymentController->upperlinkVerifyPayment($paymentReference);
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

        Mail::send([], [], function ($message) use ($exportPath, $fileName) {
            $message->to(env('BACKUP_EMAIL'))
                ->subject('Database Backup ' . date('Y-m-d H:i:s'))
                ->attach($exportPath, ['as' => $fileName]);
        });

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

        $registrations = CourseRegistration::where('student_id', $student->id)
            ->select('academic_session', 'semester', 'level_id')
            ->distinct()
            ->get();

        dd($registrations);

        foreach ($registrations as $record) {
            $academicSession = $record->academic_session;
            $semester = $record->semester;
            $levelName = $record->level_id * 100 . " Level";
            $semesterName = $semester==1 ? "Harmattan Semester" : "Rain Semester";

            $gpa = Result::getPresentGPA($student, $academicSession, $semester);

            StudentSemesterGPA::updateOrCreate(
                [
                    'student_id' => $student->id,
                    'session' => $academicSession,
                    'semester' => $semesterName,
                    'level' => $levelName
                ],
                [
                    'gpa' => $gpa,
                ]
            );
        }
    }

    public function getSemesterGPA() {
        $students = Student::orderBy('id', 'DESC')->get();
    
        foreach($students as $student) {
            $this::populateSemesterRecords($student);
        }
    
        return response()->json(['message' => 'record updated.']);
    }

    public function updateStudentGrade(){
        $registrations = CourseRegistration::whereNotNull('total')
        ->where(function ($query) {
            $query->whereNull('grade')
                  ->orWhere('grade', '!=', 'F'); // Optional: only update non-Fs if needed
        })
        ->get();

        foreach ($registrations as $studentRegistration) {
            $totalScore = $studentRegistration->total;
    
            if ($totalScore > 0) {
                $student = Student::find($studentRegistration->student_id);
                $courseCode = $studentRegistration->course_code;
    
                $grading = GradeScale::computeGrade($totalScore);
                $grade = $grading->grade;
                $points = $grading->point;
    
                // Default to 40 unless otherwise specified
                $requiredPassMark = 40;
    
                if ($student) {
                    $studentProgrammeRequirement = ProgrammeRequirement::find($student->programme_id);
                    if ($studentProgrammeRequirement) {
                        $additionalCriteria = json_decode($studentProgrammeRequirement->additional_criteria, true);
    
                        if (
                            isset($additionalCriteria['course_code_50_pass']['enabled']) &&
                            $additionalCriteria['course_code_50_pass']['enabled'] &&
                            isset($additionalCriteria['course_code_50_pass']['prefixes'])
                        ) {
                            foreach ($additionalCriteria['course_code_50_pass']['prefixes'] as $prefix) {
                                if (stripos($courseCode, $prefix) === 0) {
                                    $requiredPassMark = 50;
                                    break;
                                }
                            }
                        }
                    }
                }
    
                if ($totalScore < $requiredPassMark) {
                    $grade = 'F';
                    $points = 0;
                }
    
                $studentRegistration->grade = $grade;
                $studentRegistration->points = $studentRegistration->course_credit_unit * $points;
                $studentRegistration->save();
            }
        }

        return response()->json(['message' => 'record updated.']);
    }

}

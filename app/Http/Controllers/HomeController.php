<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

use App\Models\Course;
use App\Models\CourseRegistrationSetting;
use App\Models\CourseRegistration;
use App\Models\StudentCourseRegistration;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\StudentExamCard;
use App\Models\Student;
use App\Models\Notification;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Staff;
use App\Models\StudentExit;
use App\Models\Unit;
use App\Models\ProgrammeCategory;
use App\Models\Plan;

use App\Mail\NotificationMail;



use App\Libraries\Pdf\Pdf;
use App\Libraries\Bandwidth\Bandwidth;
use App\Libraries\Google\Google;


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;

class HomeController extends Controller
{
    

    public function studentDetails(Request $request, $slug){
        $student = Student::withTrashed()->with(['applicant', 'programme', 'partner', 'academicLevel', 'department', 'faculty'])
        ->where('slug', $slug)->first();

        return view('studentProfile', [
            'student' => $student,
        ]);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getExamDocket(Request $request, $slug){

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')
            ->where('slug', $slug)
            ->first();

        if (!$student) {
            alert()->error('Oops!', 'Student not found')->persistent('Close');
            return redirect()->back();
        }

        $programmeCategoryId = $student->programme_category_id ?? null;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        if (empty($programmeCategory->academicSessionSetting)) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;
        $semester = $programmeCategory->examSetting->semester ?? null;

        $studentId = $student->id;
        $levelId = $student->level_id;
        $transactions = Transaction::where('student_id', $studentId)->orderBy('id', 'DESC')->get();

        $checkStudentPayment = $this->checkSchoolFees($student, $academicSession, $levelId);
        if($checkStudentPayment->status != 'success'){
            alert()->error('Oops!', 'Something went wrong with School fees')->persistent('Close');
            return redirect()->back();
        }

        $schoolPayment = Payment::with('structures')
            ->where('type', Payment::PAYMENT_TYPE_SCHOOL)
            ->where('programme_id', $student->programme_id)
            ->where('level_id', $student->level_id)
            ->where('academic_session', $student->academic_session)
            ->first();

        $passTuitionPayment = $checkStudentPayment->passTuitionPayment;
        $fullTuitionPayment = $checkStudentPayment->fullTuitionPayment;
        $passEightyTuition = $checkStudentPayment->passEightyTuition;


        $registeredCourses = CourseRegistration::with('course')
        ->where('student_id', $studentId)
        ->where('academic_session', $academicSession)
        ->where('total', null)
        ->whereHas('course', function ($query) use ($semester) {
            $query->where('semester', $semester);
        })
        ->get();
        
        return view('studentExamDocket', [
            'student' => $student,
            'transactions' => $transactions,
            'payment' => $schoolPayment,
            'passTuition' => $passTuitionPayment,
            'fullTuitionPayment' => $fullTuitionPayment,
            'passEightyTuition' => $passEightyTuition,
            'registeredCourses' => $registeredCourses
        ]);
    }

    public function updateNotificationStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'notificationId' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        Notification::where('id', $request->notificationId)->update(['status' => true]);

        return true;
    }

    public function addStaffRecord(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staffId' => 'required|unique:staff',
            'lastname' => 'required',
            'othernames' => 'required',
            'description' => 'required',
            'email' => 'required|unique:staff',
            'phone_number' => 'required',
            'image' => 'required',
            'password' => 'required',
            'confirm_password' => 'required',
            'title' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!strpos($request->email, env('SCHOOL_DOMAIN'))) {
            alert()->error('Error', 'Invalid email, your email must contain @'.env('SCHOOL_DOMAIN'))->persistent('Close');
            return redirect()->back();
        }

        // if(strpos($request->staffId, env('SCHOOL_CODE')) !== false) {
        //     alert()->error('Error', 'Invalid staff ID, please kindly follow the format ('.env("SCHOOL_CODE").'SSPFID)')->persistent('Close');
        //     return redirect()->back();
        // }

        if (!str_starts_with($request->staffId, 'TAU')) {
            alert()->error('Error', 'Invalid staff ID, please kindly follow the format (TAU)')->persistent('Close');
            return redirect()->back();
        }

        // if((strpos($request->staffId, '/') !== false)) {
        //     alert()->error('Error', 'Invalid staff ID, please kindly follow the format ('.env("SCHOOL_CODE").'SSPFID)')->persistent('Close');
        //     return redirect()->back();
        // }
        

        if($request->password == $request->confirm_password){
            $password = bcrypt($request->password);
        }else{
            alert()->error('Oops!', 'Password mismatch')->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->lastname.'-'.$request->othernames)));
        $imageUrl = null;
        if($request->has('image')) {
            $imageUrl = 'uploads/staff/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
            $image = $request->file('image')->move('uploads/staff', $imageUrl);
        }

        $newAddStaff = ([
            'title' => $request->title,
            'staffId' => $request->staffId,
            'lastname' => $request->lastname,
            'othernames' => $request->othernames,
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
            'category' => $request->category,
            'email' => $request->email,
            'password' => $password,
            'phone_number' => $request->phone_number,
            'description' => $request->description,
            'slug' => $slug,
            'image' => env('APP_URL').'/'.$imageUrl,
            'referral_code' => $this->generateReferralCode()
        ]);

        if($staff = Staff::create($newAddStaff)){
            $google = new Google();
            $google->addMemberToGroup($staff->email, env('GOOGLE_STAFF_GROUP'));
            if(strtolower($staff->category) == 'academic'){
                $google->addMemberToGroup($staff->email, env('GOOGLE_ACADEMIC_STAFF_GROUP'));
            }else{
                $google->addMemberToGroup($staff->email, env('GOOGLE_NON_ACADEMIC_STAFF_GROUP'));
            }

            alert()->success('Staff added', 'A staff have been added')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function staffRecord(Request $request)
    {
        $departments = Department::all();
        $faculties = Faculty::all();
        $units = Unit::all();

        return view('addStaff', [
            'departments' => $departments,
            'faculties' => $faculties,
            'units' => $units
        ]);
    }

    public function hallOfFame(Request $request)
    {
        $students = Student::where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->where('cgpa', '>', 4.49)
            ->orderBy('level_id', 'ASC')
            ->get();

        return view('hallOfFame', [
            'students' => $students,
        ]);
    }

    public function checkDataBalance(Request $request){
        $validator = Validator::make($request->all(), [
            'bandwidth_password' => 'required',
            'bandwidth_username' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->bandwidth_username)){
            $bandwidth = new Bandwidth();
            $checkBalance = $bandwidth->checkDataBalance($request->bandwidth_username, $request->bandwidth_password);
            $message = $checkBalance['message'];
            alert()->info('Bandwidth Balance!', $message)->persistent('Close');
            return redirect()->back();
        }
    }

    public function verifyStudentExits(Request $request){

        return view('student.verifyStudentExit');

    }

    public function verifyStudentExit(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$studentExit = StudentExit::find($request->exit_id)) {
            alert()->error('Oops!', 'Student exit applicattion record not found')->persistent('Close');
            return redirect()->back();
        }

        $student = Student::find($studentExit->student_id);

        return view('student.verifyStudentExit', [
            'studentExit' => $studentExit,
            'student' => $student
        ]);
    }

    public function enterSchool(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$studentExit = StudentExit::find($request->exit_id)) {
            alert()->error('Oops!', 'Student exit applicattion record not found')->persistent('Close');
            return redirect()->back();
        }

        if ($request->password != env('SECURITY_PASSWORD')) {
            alert()->error('Oops!', 'Password Mismatch')->persistent('Close');
            return redirect()->back();
        }

        $studentExit->return_at = Carbon::now();

        if($studentExit->save()){
            $student = Student::find($studentExit->student_id);

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'You are welcome back to school.';

            $mail = new NotificationMail($senderName, $message, $receiverName);

            if(env('SEND_MAIL')){
                Mail::to($student->email)->send($mail);
            }

            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'status' => 0
            ]);

            alert()->success('Success', '')->persistent('Close');
            return redirect()->back();
        }
    }

    public function leftSchool(Request $request){
        $validator = Validator::make($request->all(), [
            'exit_id' => 'required',
            'password' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$studentExit = StudentExit::find($request->exit_id)) {
            alert()->error('Oops!', 'Student exit applicattion record not found')->persistent('Close');
            return redirect()->back();
        }

        $studentExit->exited_at = Carbon::now();

        if($studentExit->save()){
            $student = Student::find($studentExit->student_id);

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'This is to notify you that you have been exited from the university campus. Safe Trip';

            $mail = new NotificationMail($senderName, $message, $receiverName);
            if(env('SEND_MAIL')){
                Mail::to($student->email)->send($mail);
            }
            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'status' => 0
            ]);

            alert()->success('Success', '')->persistent('Close');
            return redirect()->back();
        }
    }

    public function exit($id){

        if (!$studentExit = StudentExit::find($id)) {
            alert()->error('Oops!', 'Student exit applicattion record not found')->persistent('Close');
            return view('welcome');
        }
        
        return view('exit', [
            'studentExit' => $studentExit,
            'student' => $studentExit->student
        ]);
    }

    public function getDepartments($id){
        $departments = Department::where('faculty_id', $id)->get();

        return $departments;
    }

    public function getPayments(Request $request){

        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();

        $applicationSession = $programmeCategory->academicSessionSetting->application_session ?? null;
        if (!$applicationSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }        

        $commonConditions = [
            'programme_category_id' => $request->programme_category_id,
            'academic_session' => $applicationSession
        ];
        
        $applicationPayment = Payment::with('structures', 'programmeCategory')
            ->where($commonConditions)
            ->where('type', Payment::PAYMENT_TYPE_GENERAL_APPLICATION)
            ->first();
        
        $interApplicationPayment = Payment::with('structures', 'programmeCategory')
            ->where($commonConditions)
            ->where('type', Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION)
            ->first();

        $payment = new \stdClass();
        $payment->payment = $applicationPayment;
        $payment->interApplicationPayment = $interApplicationPayment;

        return $payment;

    }

    public function getProgrammeCategory(Request $request){
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $request->programme_category_id)->first();
        
        return $programmeCategory;
    }

    public function csrfErrorPage(){ 

        $studentEmail = 'test@st.tau.edu.ng';
        $othernames = "test";
        $lastname = "admin";
        $accessCode = "mypassword";

        $google = new Google();
        $createStudentEmail = $google->createUser($studentEmail, $othernames, $lastname, $accessCode, env('GOOGLE_STUDENT_GROUP'));
        dd($createStudentEmail);
        log::info($createStudentEmail);


        return view('errors.csrf');
    }


    public function addBandwidth(){

        $plans = Plan::all();

        return view('addBandwidth', [
            'plans' => $plans,
        ]);
    }

    public function bandwidthTopUp(Request $request){
        $validator = Validator::make($request->all(), [
            'file' => 'required_without:username|nullable|file|mimes:csv',
            'username' => 'required_without:file|nullable|string|max:255',
            'plan_id' => 'required|exists:plans,id',
            'password' => 'required|string|min:6',
        ], [
            'file.required_without' => 'You must provide either a file or a username.',
            'username.required_without' => 'You must provide either a username or a file.',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back()->withInput();
        }

        if ($request->password !== env('BANDWIDTH_PASSWORD')) {
            alert()->error('Error', 'Password Mismatch')->persistent('Close');
            return redirect()->back()->withInput();
        }

        $bandwidthPlan = Plan::find($request->plan_id);
        $bandwidthAmount = $bandwidthPlan->bandwidth;
        $bandwidth = new Bandwidth();

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $csv = Reader::createFromPath($file->getPathname(), 'r');
            $csv->setHeaderOffset(0);
            $records = $csv->getRecords();

            foreach ($records as $row) {
                if (!isset($row['username']) || empty(trim($row['username']))) continue;

                $username = trim($row['username']);

                $validateUsername = $bandwidth->validateUser($username);
                if($validateUsername['status'] != 'success'){
                    alert()->error('Oops!', 'Invalid Username, Kindly enter the correct username')->persistent('Close');
                    return redirect()->back();
                }

                $creditStudent = $bandwidth->addToDataBalance($username, $bandwidthAmount);

                if ($creditStudent && $creditStudent['status'] === 'success') {
                    Log::info("Credited bandwidth: {$bandwidthAmount} to {$username}");
                } else {
                    Log::warning("Failed to credit bandwidth to {$username}");
                }
            }
        } else {
            $username = trim($request->username);

            $validateUsername = $bandwidth->validateUser($username);
            if($validateUsername['status'] != 'success'){
                alert()->error('Oops!', 'Invalid Username, Kindly enter the correct username')->persistent('Close');
                return redirect()->back();
            }

            $creditStudent = $bandwidth->addToDataBalance($username, $bandwidthAmount);

            if ($creditStudent && $creditStudent['status'] === 'success') {
                Log::info("Credited bandwidth: {$bandwidthAmount} to {$username}");
            } else {
                Log::warning("Failed to credit bandwidth to {$username}");
                alert()->error('Error', 'Unable to credit bandwidth to the specified user.')->persistent('Close');
                return redirect()->back()->withInput();
            }
        }

        alert()->success('Success', 'Bandwidth top-up successful.')->persistent('Close');
        return redirect()->back();
    }

}

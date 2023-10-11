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



use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;

class HomeController extends Controller
{
    

    public function studentDetails(Request $request, $slug){
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $student = Student::with(['applicant', 'programme', 'partner', 'academicLevel', 'department', 'faculty'])
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

        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $semester  = $globalData->examSetting['semester'];

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('slug', $slug)->first();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $transactions = Transaction::where('student_id', $studentId)->orderBy('id', 'DESC')->get();

        $checkStudentPayment = $this->checkSchoolFees($student, $academicSession, $levelId);
        if($checkStudentPayment->status != 'success'){
            alert()->error('Oops!', 'Something went wrong with School fees')->persistent('Close');
            return redirect()->back();
        }

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
            'transactions' => $schoolPaymentTransaction,
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

        if(empty(strpos($request->staffId, 'AU'))) {
            alert()->error('Error', 'Invalid staff ID, please kindly follow the format(TAU/SSPF/ID)')->persistent('Close');
            return redirect()->back();
        }

        if((strpos($request->staffId, '/') !== false)) {
            alert()->error('Error', 'Invalid staff ID, please kindly follow the format ('.env("SCHOOL_CODE").'SSPFID)')->persistent('Close');
            return redirect()->back();
        }
        

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

        if(Staff::create($newAddStaff)){
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

        return view('addStaff', [
            'departments' => $departments,
            'faculties' => $faculties,
        ]);
    }
}

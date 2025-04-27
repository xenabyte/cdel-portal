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

use App\Models\Programme;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Student;
use App\Models\SessionSetting;
use App\Models\Admin;
use App\Models\Staff;
use App\Models\CourseLecture;
use App\Models\CourseManagement;
use App\Models\CourseRegistration;


use App\Libraries\Google\Google;
use App\Libraries\Pdf\Pdf;


use Paystack;
use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

use KingFlamez\Rave\Facades\Rave as Flutterwave;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
     public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
            'api_key' => 'required',
        ]);
        $appApiKey = env('APP_API_KEY');

        $email = $request->email;
        $password = $request->password;
        $role = $request->role;
        $apiKey = $request->api_key;

        if($appApiKey != $apiKey){
            return $this->dataResponse('Invalid Api Key', null, 'error');
        }

        switch ($role) {
            case 'admin':
                $user = Admin::where('email', $email)->first();
                break;
            case 'staff':
                $user = Staff::where('email', $email)->first();
                break;
            case 'student':
                $user = Student::where('email', $email)->first();
                break;
            default:
                return $this->dataResponse('Invalid role', null, 'error');
        }
    
        // Check if the user exists and if the password matches
        if ($user && \Hash::check($password, $user->password)) {
            return $this->dataResponse('Login Successful!', $user);
        } else {
            return $this->dataResponse('Invalid Credentials', null, 'error');
        }

    }
    
    public function validateUser(Request $request){
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required',
            'role' => 'required',
            'api_key' => 'required',
        ]);

        if($validator->fails()) {
            return $this->dataResponse($validator->messages()->all()[0], null, 'error');
        }

        $appApiKey = env('APP_API_KEY');

        $uniqueId = $request->uniqueId;
        $role = $request->role;
        $apiKey = $request->api_key;

        if($appApiKey != $apiKey){
            return $this->dataResponse('Invalid Api Key', null, 'error');
        }

        switch ($role) {
            case 'staff':
                $user = Staff::where('staffId', $uniqueId)->orWhere('email', $uniqueId)->first();
                break;
            case 'student':
                $user = Student::with("applicant")->where('matric_number', $uniqueId)->first();
                break;
            default:
                return $this->dataResponse('Invalid role', null, 'error');
        }
    
        if ($user) {
            $response = new \stdClass();
            $response->application_id = $role == 'student' ? ($user->applicant->id ?? null) : null;
            $response->id = $user->id;
            $response->lastname = $role == 'student' ? ($user->applicant->lastname ?? null) : ($user->lastname ?? null);
            $response->othernames = $role == 'student' ? ($user->applicant->othernames ?? null) : ($user->othernames ?? null);
            $response->email = $user->email;
            $response->matric_number = $role == 'student' ? ($user->matric_number ?? null) : null;
            $response->programme = $role == 'student' ? ($user->programme->name ?? null) : null;
            $response->department = $user->department->name ?? null;
            $response->faculty = $user->faculty->name ?? null;
            $response->staff_id = $role == 'staff' ? ($user->staffId ?? null) : null;
            $response->image = $role == 'student' ? ('https://portal.tau.edu.ng/' . ($user->image ?? '')) : ($user->image ?? null);
            $response->phone_number = $role == 'student' ? ($user->applicant->phone_number ?? null) : ($user->phone_number ?? null);
        
            return $this->dataResponse($role . ' record found!', $response);
        } else {
            return $this->dataResponse($role.' record not found', null, 'error');
        }

    }

    public function getCourseLecture(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'api_key' => 'required',
        ]);

        if($validator->fails()) {
            return $this->dataResponse($validator->messages()->all()[0], null, 'error');
        }

        $appApiKey = env('APP_API_KEY');

        $code = $request->code;
        $apiKey = $request->api_key;

        if($appApiKey != $apiKey){
            return $this->dataResponse('Invalid Api Key', null, 'error');
        }

        $courseLecture = CourseLecture::with('course', 'lectureAttendance')->where('id', $code)->first();
        
        if ($courseLecture) {
            $academicSession = $courseLecture->academic_session;
            $courseId = $courseLecture->course_id;
            $programmeCategoryId = $courseLecture->programme_category_id;
            $lecturerDetails = CourseManagement::with('staff')->where('course_id', $courseId)->where('academic_session', $academicSession)->first(); 
            $registrations = CourseRegistration::with('student', 'student.applicant')->where('course_id', $courseId)->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->get();

            $courseLecture->lecturerDetails = $lecturerDetails;
            $courseLecture->registrations = $registrations;
            $courseLecture->totalRegisteredStudent = count($registrations);
            $courseLecture->totalAttendedStudent = count($courseLecture->lectureAttendance);

            return $this->dataResponse('Lecture record found!', $courseLecture);
        } else {
            return $this->dataResponse('Lecture record not found', null, 'error');
        }

    }


    public function getStudent(Request $request){
        $validator = Validator::make($request->all(), [
            'matric_number' => 'required',
        ]);

        if($validator->fails()) {
            return $this->dataResponse($validator->messages()->all()[0], null, 'error');
        }

        $matricNumber = $request->matric_number;

        $user = Student::with("applicant")->where('matric_number', $matricNumber)->first();

    
        // Check if the user exists and if the password matches
        if ($user) {
            $response = new \stdClass();
            $response->application_id = $user->applicant->id;
            $response->lastname = $user->applicant->lastname;
            $response->othernames = $user->applicant->othernames;
            $response->email = $user->email;
            $response->image = $user->image;
            $response->phone_number =  $user->applicant->phone_number;

            return $this->dataResponse(' record found!', $response);
        } else {
            return $this->dataResponse(' record not found', null, 'error');
        }

    }
}

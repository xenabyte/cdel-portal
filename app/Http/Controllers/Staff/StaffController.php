<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Staff;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\Programme;
use App\Models\AcademicLevel;
use App\Models\Course;
use App\Models\Notification;

use App\Mail\NotificationMail;


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StaffController extends Controller
{
    //

    public function index(Request $request){

        return view('staff.home');
    }

    public function mentee(Request $request){

        return view('staff.mentee');
    }

    public function courses(Request $request){

        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $courses = Course::withCount('registrations')->with('level')->where('staff_id', $staffId)->get();

        return view('staff.courses', [
            'courses' => $courses,
        ]);
    }

    public function courseDetail(Request $request, $id){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $course = Course::with('level', 'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('staff_id', $staffId)->first();
        $registeredStudents = $course->registrations->where('academic_session', $academicSession)->pluck('student');
        $course->registeredStudents = $registeredStudents;

        return view('staff.courseDetail', [
            'course' => $course,
        ]);
    }

    public function reffs(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];
        $referalCode = $staff->referral_code;

        $applicants = Applicant::with('student')->where('referrer', $referalCode)->where('academic_session', $applicationSession)->get();


        return view('staff.reffs', [
            'applicants' => $applicants,
        ]);
    }

    public function getAllReffs(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $referalCode = $staff->referral_code;

        $applicants = Applicant::with('student')->where('referrer', $referalCode)->get();


        return view('staff.reffs', [
            'applicants' => $applicants,
        ]);
    }

    public function applicant(Request $request, $slug){
        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('slug', $slug)->first();
        
        return view('staff.applicant', [
            'applicant' => $applicant,
        ]);
    }

    public function applicantWithSession(Request $request){
        $applicants = Applicant::with('programme', 'olevels', 'guardian', 'student')->where('academic_session', $request->session)->get();
        
        return view('staff.reffs', [
            'applicants' => $applicants
        ]);
    }

    public function student(Request $request, $slug){
        $student = Student::with('applicant', 'applicant.utmes', 'programme', 'transactions')->where('slug', $slug)->first();

        return view('staff.student', [
            'student' => $student
        ]);
    }

    public function courseAllocation(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $staffDepartmentId = $staff->department_id;

        $programmes = Programme::where('department_id', $staffDepartmentId)->get();
        $levels = AcademicLevel::get();

        return view('staff.courseAllocation', [
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function getCourses(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $staffDepartmentId = $staff->department_id;

        $levelId = $request->level_id;
        $semester = $request->semester;
        $programmeId = $request->programme_id;

        $courses = Course::with('staff')->where([
            'programme_id' => $programmeId,
            'semester' => $semester,
            'level_id' => $levelId
        ])->get();

        $programme = Programme::find($programmeId);
        $level = AcademicLevel::find($levelId);

        
        $programmes = Programme::where('department_id', $staffDepartmentId)->get();
        $levels = AcademicLevel::get();

        return view('staff.courseAllocation', [
            'programmes' => $programmes,
            'levels' => $levels,
            'courses' => $courses,
            'mainProgramme' => $programme,
            'mainLevel' => $level,
        ]);
    }

    public function assignCourse(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $staffDepartmentId = $staff->department_id;
        $levelId = $request->level_id;
        $semester = $request->semester;
        $programmeId = $request->programme_id;

        $programme = Programme::find($programmeId);
        $level = AcademicLevel::find($levelId);

        $courses = Course::with('staff')->where([
            'programme_id' => $programmeId,
            'semester' => $semester,
            'level_id' => $levelId
        ])->get();
        
        $programmes = Programme::where('department_id', $staffDepartmentId)->get();
        $levels = AcademicLevel::get();

        $tauStaffId = strtoupper($request->staff_id);

        if(!$staff = Staff::where('staffId', $tauStaffId)->first()){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');

            return view('staff.courseAllocation', [
                'programmes' => $programmes,
                'levels' => $levels,
                'courses' => $courses,
                'mainProgramme' => $programme,
                'mainLevel' => $level,
            ]);
        }

        $course = Course::find($request->course_id);
        $course->staff_id = $staff->id;

        if($course->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
        }

        $courses = Course::with('staff')->where([
            'programme_id' => $programmeId,
            'semester' => $semester,
            'level_id' => $levelId
        ])->get();

        return view('staff.courseAllocation', [
            'programmes' => $programmes,
            'levels' => $levels,
            'courses' => $courses,
            'mainProgramme' => $programme,
            'mainLevel' => $level,
        ]);
    }

    public function sendMessage(Request $request){
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $validator = Validator::make($request->all(), [
            'message' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $staff = Auth::guard('staff')->user();
        $staffName = $staff->title.' '.$staff->lastname.' '.$staff->othernames;
        $staffId = $staff->id;

        $message = $request->message;
        $course = Course::with('level', 'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('staff_id', $staffId)->first();
        $registeredStudents = $course->registrations->where('academic_session', $academicSession)->pluck('student');

        foreach ($registeredStudents as $student){
            $description = $staffName ." sent you a message; ".$message;
            $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
            Notification::create([
                'student_id' => $student->id,
                'description' => $description,
                'status' => 0
            ]);

            $staffDescription = "You sent you a message to all student offering ".$course->code.", messages says; ".$message;
            Notification::create([
                'staff_id' => $staffId,
                'description' => $staffDescription,
                'status' => 0
            ]);

            //send a notification mail
            Mail::to($request->email)->send(new NotificationMail($staffName, $message, $receiverName));
        }

        alert()->success('Message sent', '')->persistent('Close');
        return redirect()->back();
    }

    public function roleAllocation(Request $request){

        return view('staff.roleAllocation');
    }

    
}

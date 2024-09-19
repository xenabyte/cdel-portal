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

use App\Models\AcademicLevel;
use App\Models\ApprovalLevel;
use App\Models\Session;
use App\Models\SessionSetting;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\CourseRegistrationSetting;
use App\Models\ExaminationSetting;
use App\Models\Programme;
use App\Models\Student;
use App\Models\StudentDemotion;
use App\Models\Course;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\LevelAdviser;
use App\Models\CoursePerProgrammePerAcademicSession;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

use App\Libraries\Pdf\Pdf;

use App\Mail\NotificationMail;

class AcademicController extends Controller
{
    //

    public function faculties(Request $request){
        $faculties = Faculty::with('departments')->get();

        return view('staff.faculties', [
            'faculties' => $faculties
        ]);
    }

    public function faculty(Request $request, $slug){
        $faculty = Faculty::with('departments', 'departments.programmes', 'students', 'students.programme', 'students.programme.department')
        ->where('slug', $slug)->first();

        return view('staff.faculty', [
            'faculty' => $faculty
        ]);
    }

    public function departments(Request $request){
        $departments = Department::with('programmes')->get();

        return view('staff.departments', [
            'departments' => $departments
        ]);
    }

    public function department(Request $request, $slug){
        $department = Department::with('programmes', 'programmes.students', 'programmes.academicAdvisers', 'programmes.academicAdvisers.staff', 'programmes.academicAdvisers.level')->where('slug', $slug)->first();
        $levels = AcademicLevel::all();

        return view('staff.department', [
            'department' => $department,
            'levels' => $levels
        ]);
    }

    public function departmentForCourses(){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $department = Department::with('courses')->where('id', $staff->department_id)->orWhere('faculty_id', 0)->orderBy('id', 'DESC')->get();
        
        return view('staff.departmentForCourses', [
            'departments' => $department
        ]);
    }

    public function departmentForCourse(Request $request, $slug){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $department = Department::with('courses', 'courses.courseManagement', 'courses.courseManagement.staff', 'programmes', 'programmes.students', 'programmes.academicAdvisers', 'programmes.academicAdvisers.staff', 'programmes.academicAdvisers.level')->where('slug', $slug)->first();
        
        return view('staff.departmentForCourse', [
            'department' => $department,
        ]);
    }

    public function addCourse(Request $request){
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|unique:courses',
            'name' => 'required|string',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newCourses = [
            'name' => $request->name,
            'code' => strtoupper($request->code),
            'department_id' => $request->department_id,
        ];
        
        if(Course::create($newCourses)){
            alert()->success('Course added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateCourse(Request $request){
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$course = Course::find($request->course_id)){
            alert()->error('Oops', 'Invalid Level ')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->name) &&  $request->name != $course->name){
            $course->name = $request->name;
        }

        if(!empty($request->code) &&  $request->code != $course->code){
            $course->code = $request->code;
        }

        if($course->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function requestCourseApproval(Request $request) {
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $levelAdviser = LevelAdviser::find($request->level_adviser_id);
        if(!$levelAdviser){
            alert()->error('Oops', 'Invalid Level Adviser ')->persistent('Close');
            return redirect()->back();
        }

        $levelAdviser->course_approval_status = 'pending';
        $programme = $levelAdviser->programme->name;
        $level = $levelAdviser->level->level .' Level ';

        if($levelAdviser->save()){
            $senderName = env('SCHOOL_NAME');
            $message = 'You have a pending courses from '. $level.$programme .' level adviser for review. Please review the application on the staff portal.';
            $unitId = env("ACADEMIC_PLANNING");

            $unit = Unit::with('unit_head')->where('id', $unitId)->first();
            $unitHead =  $unit ? $unit->unit_head : null;
            
            if($unitHead){
                $mail = new NotificationMail($senderName, $message, $unitHead->title.' '.$unitHead->lastname.' '.$unitHead->othernames);

                Mail::to($unitHead->email)->send($mail);
                Notification::create([
                    'staff_id' => $unitHead->id,
                    'description' => $message,
                    'status' => 0
                ]);
            }

            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function courseApproval(Request $request) {
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $comment = $request->comment;
        $status = $request->status == 'request changes'?null:$request->status;

        $levelAdviser = LevelAdviser::find($request->level_adviser_id);
        if(!$levelAdviser){
            alert()->error('Oops', 'Invalid Level Adviser ')->persistent('Close');
            return redirect()->back();
        }

        $programme = $levelAdviser->programme->name;
        $level = $levelAdviser->level->level .' Level ';

        $levelAdviser->comment = $comment;
        $levelAdviser->course_approval_status = $status;
        $senderName = env('SCHOOL_NAME');


        if($status == null){
            
            $message = 'You have been requested to make changes to '. $level.$programme .' courses. Please review on the staff portal.';
            
            if($levelAdviser->staff){
                $mail = new NotificationMail($senderName, $message, $levelAdviser->staff->title.' '.$levelAdviser->staff->lastname.' '.$levelAdviser->staff->othernames);

                Mail::to($levelAdviser->staff->email)->send($mail);
                Notification::create([
                    'staff_id' => $levelAdviser->staff->id,
                    'description' => $message,
                    'status' => 0
                ]);
            }
        }

        if($status == 'approved'){
            $courses = CoursePerProgrammePerAcademicSession::where('programme_id', $request->programme_id)
            ->where('level_id', $request->level_id)
            ->where('academic_session', $academicSession)
            ->update(['dap_approval_status' => 'approved']);

            $message = 'Courses for '. $level.$programme .' students have been approved by DAP. Kindly proceed to open course registration for students.';
            
            $senderName = env('SCHOOL_NAME');
            $receiverName = 'Portal Admininstrator';
            $adminEmail = env('APP_EMAIL');
            
            $mail = new NotificationMail($senderName, $message, $receiverName);
            Mail::to($adminEmail)->send($mail);
        }

        if($levelAdviser->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

}

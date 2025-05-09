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
use App\Models\Faculty;
use App\Models\Department;
use App\Models\Course;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\LevelAdviser;
use App\Models\CoursePerProgrammePerAcademicSession;
use App\Models\ProgrammeCategory;
use App\Models\Student;
use App\Models\StudentSuspension;
use App\Models\ProgrammeChangeRequest;
use App\Models\Staff;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

use App\Libraries\Pdf\Pdf;

use App\Mail\NotificationMail;

class AcademicController extends Controller
{

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
        $categories = ProgrammeCategory::all();

        return view('staff.department', [
            'department' => $department,
            'levels' => $levels,
            'categories' => $categories
        ]);
    }

    public function departmentForCourses(){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $academicPlanningUnits = Unit::UNIT_ACADEMIC_PLANNING;

        $isUnitHead = Unit::where('name', $academicPlanningUnits)
                    ->where('unit_head_id', $staff->id)
                    ->exists();


        $department = Department::with('courses')->where('hod_id', $staff->id)->orderBy('id', 'DESC')->get();

        if($isUnitHead){
            $department = Department::with('courses')->orderBy('id', 'DESC')->get();
        }
        // ->orWhere('faculty_id', 0)
        return view('staff.departmentForCourses', [
            'departments' => $department
        ]);
    }

    public function departmentForCourse(Request $request, $slug){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];
        $programmeCategories = ProgrammeCategory::all();

        $department = Department::with('courses', 'courses.courseManagement', 'courses.courseManagement.staff', 'programmes', 'programmes.students', 'programmes.academicAdvisers', 'programmes.academicAdvisers.staff', 'programmes.academicAdvisers.level')->where('slug', $slug)->first();
        
        return view('staff.departmentForCourse', [
            'department' => $department,
            'programmeCategories' => $programmeCategories
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
            'name' => ucwords(strtolower($request->name)),
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
            $course->name = ucwords(strtolower($request->name));
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
                if(env('SEND_MAIL')){
                    Mail::to($unitHead->email)->send($mail);
                }
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
        $status = $request->status == 'request changes'?'pending':$request->status;

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
                if(env('SEND_MAIL')){
                    Mail::to($levelAdviser->staff->email)->send($mail);
                }
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
            if(env('SEND_MAIL')){
                Mail::to($adminEmail)->send($mail);
            }
        }

        if($levelAdviser->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    /**
     * Get all suspended students.
     */
    public function suspendedStudents()
    {
        $suspensions = StudentSuspension::with('student')->whereNull('end_date')->get();

        return view('staff.suspendedStudents', [
            'suspensions' => $suspensions
        ]);
    }

    /**
     * Get all expelled students.
     */
    public function expelledStudents()
    {
        $expelledStudents = Student::where('academic_status', 'expelled')->get();

        return view('staff.expelledStudents', [
            'expelledStudents' => $expelledStudents
        ]);
    }

    public function programmeChangeRequests(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $programmeChangeRequests = ProgrammeChangeRequest::where('status', 'pending')
            ->where(function ($query) use ($staffId) {
                $query->where('old_programme_hod_id', $staffId)
                    ->orWhere('old_programme_dean_id', $staffId)
                    ->orWhere('new_programme_hod_id', $staffId)
                    ->orWhere('new_programme_dean_id', $staffId)
                    ->orWhere('dap_id', $staffId)
                    ->orWhere('registrar_id', $staffId);
            })
            ->get();

        return view('staff.programmeChangeRequests', [
            'programmeChangeRequests' => $programmeChangeRequests,
        ]);
    }

    public function viewProgrammeChangeRequest(Request $request, $slug){

        $programmeChangeRequest = ProgrammeChangeRequest::where('slug', $slug)->first();

        return view('staff.viewProgrammeChangeRequest', [
            'programmeChangeRequest' => $programmeChangeRequest
        ]);
    }

    public function manageProgrammeChangeRequest(Request $request)
    {
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $request->validate([
            'programme_change_request_id' => 'required|exists:programme_change_requests,id',
            'role' => 'required|in:old_hod,new_hod,old_dean,new_dean,dap,registrar',
            'status' => 'required|in:approved,declined',
        ]);

        $changeRequest = ProgrammeChangeRequest::findOrFail($request->programme_change_request_id);
        
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $now = now();

        // Set approval timestamp
        switch ($request->role) {
            case 'old_hod':
                $changeRequest->hod_old_approved_at = $now;
                break;
            case 'old_dean':
                $changeRequest->dean_old_approved_at = $now;
                break;
            case 'new_hod':
                $changeRequest->hod_new_approved_at = $now;
                break;
            case 'new_dean':
                $changeRequest->dean_new_approved_at = $now;
                break;
            case 'dap':
                $changeRequest->dap_approved_at = $now;
                break;
            case 'registrar':
                $changeRequest->registrar_approved_at = $now;
                break;
        }

        // Handle rejection
        if ($request->status == 'declined') {
            $changeRequest->status = 'declined';
            $changeRequest->rejection_reason = $request->rejection_reason;
            $changeRequest->save();

            $student = Student::find($changeRequest->student_id);
            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
            $message = "Your programme change request has been declined. Reason: {$request->rejection_reason}";

            if ($student) {
                Notification::create([
                    'student_id' => $student->id,
                    'description' => $message,
                    'status' => 0,
                ]);

                if (env('SEND_MAIL')) {
                    $mail = new NotificationMail($senderName, $message, $receiverName);
                    Mail::to($student->email)->send($mail);
                }
            }

            alert()->info('Declined', 'Programme change request was declined')->persistent('Close');
            return redirect()->back();
        }

        // If approved, move to next stage or complete
        $nextStage = match ($request->role) {
            'old_hod' => 'old_dean',
            'old_dean' => 'new_hod',
            'new_hod' => 'new_dean',
            'new_dean' => 'dap',
            'dap' => 'registrar',
            'registrar' => 'completed',
        };

        if ($nextStage === 'completed') {
            $changeRequest->status = 'approved';
            $changeRequest->current_stage = 'completed';

            $student = Student::find($changeRequest->student_id);

            $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;
            $senderName = env('SCHOOL_NAME');
            $message = "Your programme change request has been fully approved.";

            $student->programme_id = $changeRequest->new_programme_id;
            $student->department_id = $changeRequest->newProgramme->department_id;
            $student->faculty_id = $changeRequest->newProgramme->department->faculty_id;
            $student->level_id = 2;
            $student->academic_status = '';

            $student->save();

            $student->refresh();

            // Reassign school fee payment to match new programme
            $studentId = $student->id;
            $applicantId = $student->user_id;
            $applicant = User::find($applicantId);
            $applicationType = $applicant->application_type;

            $type = Payment::PAYMENT_TYPE_SCHOOL;
            if ($applicationType != 'UTME' && ($student->level_id == 2 || $student->level_id == 3)) {
                $type = Payment::PAYMENT_TYPE_SCHOOL_DE;
            }

            $schoolPayment = Payment::with('structures')
                ->where('type', $type)
                ->where('programme_id', $student->programme_id)
                ->where('level_id', $student->level_id)
                ->where('academic_session', $academicSession)
                ->where('programme_category_id', $student->programme_category_id)
                ->first();

            if (!$schoolPayment) {
                alert()->success('Programme school fees not set, check with ICT admin', '')->persistent('Close');
                return $this->getSingleStudent($student->matric_number, $request->url);
            }

            // Update previous successful transactions
            Transaction::where('student_id', $studentId)
                ->where('session', $academicSession)
                ->where('status', 1)
                ->update(['payment_id' => $schoolPayment->id]);

            if ($student) {
                Notification::create([
                    'student_id' => $student->id,
                    'description' => $message,
                    'status' => 0,
                ]);

                if (env('SEND_MAIL')) {
                    
                    $mail = new NotificationMail($senderName, $message, $receiverName);
                    Mail::to($student->email)->send($mail);

                    $adminEmail = env('APP_EMAIL');
                    if ($adminEmail) {
                        $adminMessage = "Programme change for student {$student->matric_number} ({$student->lastname} {$student->othernames}) has been approved completely by {$senderName}.";
                        $adminMail = new NotificationMail($senderName, $adminMessage, 'Support Team');
                        Mail::to($adminEmail)->send($adminMail);
                    }
                }
            }
        } else {
            $changeRequest->current_stage = $nextStage;

            $nextUserId = match ($nextStage) {
                'old_dean' => $changeRequest->old_programme_dean_id,
                'new_hod' => $changeRequest->new_programme_hod_id,
                'new_dean' => $changeRequest->new_programme_dean_id,
                'dap' => $changeRequest->dap_id,
                'registrar' => $changeRequest->registrar_id,
            };

            $staff = Staff::find($nextUserId);
            if ($staff) {
                $senderName = env('SCHOOL_NAME');
                $receiverName = $staff->title . ' ' . $staff->lastname . ' ' . $staff->othernames;
                $message = 'A programme change request awaits your review.';

                Notification::create([
                    'staff_id' => $staff->id,
                    'description' => $message,
                    'status' => 0,
                ]);

                if (env('SEND_MAIL')) {
                    $mail = new NotificationMail($senderName, $message, $receiverName);
                    Mail::to($staff->email)->send($mail);
                }
            }
        }

        $changeRequest->save();

        alert()->success('Success', 'Your decision was recorded successfully')->persistent('Close');
        return redirect()->back();
    }

}

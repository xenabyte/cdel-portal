<?php

namespace App\Http\Controllers\Admin;

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

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class AcademicController extends Controller
{
    //

    public function academicLevel(){

        $academicLevels = AcademicLevel::get();
        
        return view('admin.academicLevel', [
            'academicLevels' => $academicLevels
        ]);
    }

    public function sessionSetup(){

        $sessions = Session::orderBy('id', 'DESC')->get();
        
        return view('admin.sessionSetup', [
            'sessions' => $sessions
        ]);
    }

    public function setSession(Request $request){
        $validator = Validator::make($request->all(), [
            'admission_session' => 'required',
            'academic_session' => 'required',
            'application_session' => 'required',
        ]);


        $sessionSetting = new SessionSetting;
        if(!empty($request->sessionSetting_id) && !$sessionSetting = SessionSetting::find($request->sessionSetting_id)){
            alert()->error('Oops', 'Invalid Session Setting Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->admission_session) &&  $request->admission_session != $sessionSetting->admission_session){
            $sessionSetting->admission_session = $request->admission_session;
        }

        if(!empty($request->academic_session) &&  $request->academic_session != $sessionSetting->academic_session){
            $sessionSetting->academic_session = $request->academic_session;
        }

        if(!empty($request->application_session) &&  $request->application_session != $sessionSetting->application_session){
            $sessionSetting->application_session = $request->application_session;
        }

        if($sessionSetting->save()){
            alert()->success('Changes Saved', 'Session changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function addSession(Request $request){
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|unique:sessions',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newSession = [
            'year' => $request->year,
        ];
        
        if(Session::create($newSession)){
            alert()->success('Session added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateSession(Request $request){
        $validator = Validator::make($request->all(), [
            'session_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$session = Session::find($request->session_id)){
            alert()->error('Oops', 'Invalid Session ')->persistent('Close');
            return redirect()->back();
        }

        $session->year = $request->year;

        if($session->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteSession(Request $request){
        $validator = Validator::make($request->all(), [
            'session_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$session = Session::find($request->session_id)){
            alert()->error('Oops', 'Invalid Session ')->persistent('Close');
            return redirect()->back();
        }
        
        if($session->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function addLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level' => 'required|string|unique:academic_levels',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newLevel = [
            'level' => $request->level,
        ];
        
        if(AcademicLevel::create($newLevel)){
            alert()->success('Academic level added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$level = AcademicLevel::find($request->level_id)){
            alert()->error('Oops', 'Invalid Level ')->persistent('Close');
            return redirect()->back();
        }

        $level->level = $request->level;

        if($level->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$level = AcademicLevel::find($request->level_id)){
            alert()->error('Oops', 'Invalid Level ')->persistent('Close');
            return redirect()->back();
        }
        
        if($level->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function faculties(){
        $faculties = Faculty::with('departments')->get();

        return view('admin.faculties', [
            'faculties' => $faculties
        ]);
    }

    public function faculty($slug){
        $faculty = Faculty::with('departments', 'departments.programmes', 'students', 'students.programme', 'students.programme.department')
        ->where('slug', $slug)->first();

        return view('admin.faculty', [
            'faculty' => $faculty
        ]);
    }

    public function departments(){
        $departments = Department::with('programmes')->get();

        return view('admin.departments', [
            'departments' => $departments
        ]);
    }

    public function department($slug){
        $department = Department::with('programmes', 'programmes.students')->where('slug', $slug)->first();

        return view('admin.department', [
            'department' => $department
        ]);
    }

    public function courseRegMgt(Request $request){

        $courseRegMgt = CourseRegistrationSetting::first();

        return view('admin.courseRegMgt', [
            'courseRegMgt' => $courseRegMgt
        ]);
    }

    public function setCourseRegStatus(Request $request){
        
        $courseRegMgt = new CourseRegistrationSetting;
        if(!empty($request->courseRegMgt_id) && !$courseRegMgt = CourseRegistrationSetting::find($request->courseRegMgt_id)){
            alert()->error('Oops', 'Invalid Course Reg. Setting Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->status) &&  $request->status != $courseRegMgt->status){
            $courseRegMgt->status = $request->status;
        }

        if(!empty($request->academic_session) &&  $request->academic_session != $courseRegMgt->academic_session){
            $courseRegMgt->academic_session = $request->academic_session;
        }
        
        if($courseRegMgt->save()){
            alert()->success('Changes Saved', 'Course registration changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function examDocketMgt (){
        $examDocketMgt = ExaminationSetting::first();

        return view('admin.examDocketMgt', [
            'examDocketMgt' => $examDocketMgt
        ]);
    }

    public function setExamSetting(Request $request){
        $validator = Validator::make($request->all(), [
            'exam_docket_status' => 'required',
            'academic_session' => 'required',
            'result_processing_status' => 'required',
            'semester' => 'required',
        ]);


        $examSettting = new ExaminationSetting;
        if(!empty($request->examSetting_id) && !$examSettting = ExaminationSetting::find($request->examSetting_id)){
            alert()->error('Oops', 'Invalid Exam Setting Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->exam_docket_status) &&  $request->exam_docket_status != $examSettting->exam_docket_status){
            $examSettting->exam_docket_status = $request->exam_docket_status;
        }

        if(!empty($request->academic_session) &&  $request->academic_session != $examSettting->academic_session){
            $examSettting->academic_session = $request->academic_session;
        }

        if(!empty($request->semester) &&  $request->semester != $examSettting->semester){
            $examSettting->semester = $request->semester;
        }

        if(!empty($request->result_processing_status) &&  $request->result_processing_status != $examSettting->result_processing_status){
            $examSettting->result_processing_status = $request->result_processing_status;
        }

        if($examSettting->save()){
            alert()->success('Changes Saved', 'Exam setttings saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function approvalLevel(){

        $approvalLevels = ApprovalLevel::get();
        
        return view('admin.approvalLevel', [
            'approvalLevels' => $approvalLevels
        ]);
    }
    
    public function addApprovalLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level' => 'required|string|unique:academic_levels',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newLevel = [
            'level' => $request->level,
        ];
        
        if(ApprovalLevel::create($newLevel)){
            alert()->success('Academic level added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateApprovalLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$level = ApprovalLevel::find($request->level_id)){
            alert()->error('Oops', 'Invalid Level ')->persistent('Close');
            return redirect()->back();
        }

        $level->level = $request->level;

        if($level->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteApprovalLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$level = ApprovalLevel::find($request->level_id)){
            alert()->error('Oops', 'Invalid Level ')->persistent('Close');
            return redirect()->back();
        }
        
        if($level->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }
    

    public function campusCapacity(){

        $programmes = Programme::with(['students' => function ($query) {
            $query->where('is_active', true)
                  ->where('is_passed_out', false)
                  ->where('is_rusticated', false);
        }, 'programmeCategory'])->get();
        
        return view('admin.campusCapacity', [
            'programmes' => $programmes
        ]);
    }

    public function allStudents(){

        $student = Student::
            with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->get();
        
        return view('admin.allStudents', [
            'students' => $students
        ]);
    }

    public function studentInfo($slug){

        $student = Student::
            with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->first();
        
        return view('admin.studentInfo', [
            'student' => $student
        ]);
    }

    public function massPromotion(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $programmes = Programme::with(['students' => function ($query) {
            $query->where('is_active', true)
                  ->where('is_passed_out', false)
                  ->where('is_rusticated', false);
        }, 'programmeCategory'])
        ->where(function ($query) use ($academicSession) {
            $query->where('academic_session', '!=', $academicSession)
                  ->orWhereNull('academic_session');
        })
        ->get();

         
        return view('admin.massPromotion', [
            'programmes' => $programmes
        ]);
    }

    public function demoteStudent(){

        $demoteStudent = null;
        
        return view('admin.demoteStudent', [
            'demoteStudent' => $demoteStudent
        ]);
    }

    public function promoteStudent(Request $request){
        $programmeId = $request->programme_id;
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $programme = Programme::with(['students' => function ($query) {
            $query->where('is_active', true)
                  ->where('is_passed_out', false)
                  ->where('is_rusticated', false);
        }, 'programmeCategory'])
        ->where(function ($query) use ($academicSession) {
            $query->where('academic_session', '!=', $academicSession)
                  ->orWhereNull('academic_session');
        })
        ->where('id', $programmeId)
        ->first();

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $programme->academic_session = $academicSession;

        $students = $programme->students;
        $promotionOffset = 1;
        foreach ($students as $student) {
            $student->update([
                'level_id' => $student->level_id + $promotionOffset,
                'academic_session' => $academicSession,
            ]);
        }
        if($programme->save()){
            alert()->success('Student Promoted Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }
}

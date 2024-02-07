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
use App\Models\ResultApprovalStatus;
use App\Models\Session;
use App\Models\SessionSetting;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\CourseRegistrationSetting;
use App\Models\ExaminationSetting;
use App\Models\Programme;
use App\Models\ProgrammeCategory as Category;
use App\Models\Student;
use App\Models\StudentDemotion;
use App\Models\StudentCourseRegistration;
use App\Models\Course;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\CourseRegistration;

use App\Mail\NotificationMail;

use App\Libraries\Pdf\Pdf;

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

    public function setRegistrarSetting(Request $request){
        $validator = Validator::make($request->all(), [
            'registrar_name' => 'required',
            'registrar_signature' => 'required',
            'resumption_date' => 'required',
        ]);


        $sessionSetting = new SessionSetting;
        if(!empty($request->sessionSetting_id) && !$sessionSetting = SessionSetting::find($request->sessionSetting_id)){
            alert()->error('Oops', 'Invalid Session Setting Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->registrar_name) &&  $request->registrar_name != $sessionSetting->registrar_name){
            $sessionSetting->registrar_name = $request->registrar_name;
        }

        if(!empty($request->resumption_date) &&  $request->resumption_date != $sessionSetting->resumption_date){
            $sessionSetting->resumption_date = $request->resumption_date;
        }

        if(!empty($request->registrar_signature)){
            $slug = 'registrar_signature'.time();
            $imageUrl = 'uploads/'.$slug.'.'.$request->file('registrar_signature')->getClientOriginalExtension();
            $image = $request->file('registrar_signature')->move('uploads', $imageUrl);

            $sessionSetting->registrar_signature = $imageUrl;
        }

        if($sessionSetting->save()){
            alert()->success('Changes Saved', 'Registrar changes saved successfully')->persistent('Close');
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

    /**
     * Add a new Faculty
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addFaculty(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'description' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        $newAddFaculty = ([
            'name' => $request->name,
            'slug' => $slug,
        ]);

        if(Faculty::create($newAddFaculty)){
            alert()->success('Faculty added', 'A new faculty have been added')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    /**
     * Edit Faculty
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function updateFaculty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faculty_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$faculty = Faculty::find($request->faculty_id)){
            alert()->error('Oops', 'Invalid Faculty Information')->persistent('Close');
            return redirect()->back();
        }

        $faculty->name = $request->name;

        if($faculty->save()){
            alert()->success('Changes Saved', 'Faculty changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function deleteFaculty(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faculty_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$faculty = Faculty::find($request->faculty_id)){
            alert()->error('Oops', 'Invalid Faculty Information')->persistent('Close');
            return redirect()->back();
        }


        if($faculty->delete()){
            alert()->success('Good Job', 'Faculty deleted successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function faculty($slug){
        $faculty = Faculty::with('departments', 'departments.programmes', 'students', 'students.programme', 'students.programme.department')
        ->where('slug', $slug)->first();

        return view('admin.faculty', [
            'faculty' => $faculty
        ]);
    }

       /**
     * Add a new Department
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function addDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faculty_id' => 'required',
            'name' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        $newAddDepartment = ([
            'faculty_id' => $request->faculty_id,
            'name' => $request->name,
            'slug' => $slug,
        ]);

        if(Department::create($newAddDepartment)){
            alert()->success('Department added', 'A new department have been added')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$department = Department::find($request->department_id)){
            alert()->error('Oops', 'Invalid Department Information')->persistent('Close');
            return redirect()->back();
        }

        $department->name = $request->name;
        if(!empty($request->faculty_id) && $request->faculty_id != $department->faculty_id){
            $department->faculty_id = $request->faculty_id;
        }

        if($department->save()){
            alert()->success('Changes Saved', 'Department changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function deleteDepartment(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$department = Department::find($request->department_id)){
            alert()->error('Oops', 'Invalid Department Information')->persistent('Close');
            return redirect()->back();
        }


        if($department->delete()){
            alert()->success('Good Job', 'Department deleted successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function addProgramme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'faculty_id' => 'required',
            'department_id' => 'required',
            'duration' => 'required',
            'category' => 'required',
            'name' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        $newAddProgramme= ([
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
            'slug' => $slug,
            'duration' => $request->duration,
            'name' => $request->name,
            'category' => $request->category,
        ]);

        if(Programme::create($newAddProgramme)){
            alert()->success('Programme added', 'A new programme have been added')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateProgramme(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$programme = Programme::find($request->programme_id)){
            alert()->info('Oops!!', 'Programme not found')->persistent('Close');
            return redirect()->back();
        }

        $programme->name = $request->name;
        if(!empty($request->category) && $programme->category_id != $request->category){
            $programme->category = $request->category;
        }
        if(!empty($request->duration) && $programme->duration != $request->duration){
            $programme->duration = $request->duration;
        }

        if($programme->update()){
            alert()->success('Good job!!', 'Programme changes saved successfully')->persistent('Close');
            return redirect()->back();  
        }

        alert()->info('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function deleteProgramme(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$programme = Programme::find($request->programme_id)){
            alert()->info('Oops!!', 'Programme not found')->persistent('Close');
            return redirect()->back();
        }

        if($programme->delete()){
            alert()->success('Good job!!', 'Programme deleted successfully')->persistent('Close');
            return redirect()->back();  
        }

        alert()->info('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

    public function departments(){
        $departments = Department::with('programmes')->get();

        return view('admin.departments', [
            'departments' => $departments
        ]);
    }

    public function department($slug){
        $department = Department::with('programmes', 'programmes.students', 'programmes.academicAdvisers', 'programmes.academicAdvisers.staff', 'programmes.academicAdvisers.level')->where('slug', $slug)->first();
        $levels = AcademicLevel::all();
        $categories = Category::all();

        return view('admin.department', [
            'department' => $department,
            'levels' => $levels,
            'categories' => $categories
        ]);
    }

    public function saveDepartment(Request $request){
        $validator = Validator::make($request->all(), [
            'department_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$department = Department::find($request->programme_id)){
            alert()->error('Oops', 'Invalid Department ')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->code) &&  $request->code != $department->code){
            $department->code = strtoupper($request->code);
        }

        if($department->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function saveFaculty(Request $request){
        $validator = Validator::make($request->all(), [
            'faculty_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$faculty = Faculty::find($request->faculty_id)){
            alert()->error('Oops', 'Invalid Faculty ')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->code) &&  $request->code != $faculty->code){
            $faculty->code = strtoupper($request->code);
        }

        if($faculty->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function courseRegMgt(Request $request){

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $programmes = Programme::with(['students' => function ($query) {
            $query->where('is_active', true)
                  ->where('is_passed_out', false)
                  ->where('is_rusticated', false);
        }, 'programmeCategory'])
        ->where(function ($query) use ($academicSession) {
            $query->where('academic_session', $academicSession)
                  ->orWhereNull('academic_session');
                  
        })
        ->get();

        $courseRegMgt = CourseRegistrationSetting::first();

        return view('admin.courseRegMgt', [
            'courseRegMgt' => $courseRegMgt,
            'programmes' => $programmes
        ]);
    }

    public function manageCourseReg(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
            'course_registration' => 'required',
        ]);

        $programmeId = $request->programme_id;
        $courseRegistration = $request->course_registration;

        $programme = Programme::where('id', $programmeId)
        ->first();

        if(empty($programme)){
            alert()->error('Oops', 'Invalid Programme')->persistent('Close');
            return redirect()->back();
        }

        $programme->course_registration = $courseRegistration;

        if($programme->save()){
            alert()->success('Changes Saved', 'Course registration changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function resetCourseReg(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
        ]);

        $programmeId = $request->programme_id;

        $programme = Programme::where('id', $programmeId)
        ->first();

        if(empty($programme)){
            alert()->error('Oops', 'Invalid Programme')->persistent('Close');
            return redirect()->back();
        }

        $programmeStudents = Student::where('programme_id', $programmeId)->get();
        $programmeStudentIds = $programmeStudents->pluck('id')->toArray();
        $studentCourseRegistrations = StudentCourseRegistration::whereIn('student_id', $programmeStudentIds)->get();
        
        foreach ($studentCourseRegistrations as $studentCourseReg){
            $studentId = $studentCourseReg->student_id;
            $academicSession = $studentCourseReg->academic_session;
    
            // Delete registered courses
            CourseRegistration::where([
                'student_id' => $studentId,
                'academic_session' => $academicSession
            ])->forceDelete();
    
            $studentCourseReg->forceDelete();
        }

        alert()->success('Good Job', 'Course registration reset successfully')->persistent('Close');
        return redirect()->back();
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

        // $faculties = Faculty::with(['students' => function ($query) {
        //     $query->select('level_id', \DB::raw('count(*) as student_count'))
        //           ->groupBy('level_id');
        // }])->get();
    
        // Log::info($faculties);

        
        return view('admin.campusCapacity', [
            'programmes' => $programmes,
            // 'faculties' => $faculties
        ]);
    }

    public function allStudents(){

        $students = Student::
            with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->get();
        
        return view('admin.allStudents', [
            'students' => $students
        ]);
    }

    public function studentProfile($slug){
        $academicLevels = AcademicLevel::orderBy('id', 'desc')->get();
        $sessions = Session::orderBy('id', 'desc')->get();

        $student = Student::
            with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
            ->where('slug', $slug)
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->first();
        
        return view('admin.studentProfile', [
            'student' => $student,
            'academicLevels' => $academicLevels,
            'sessions' => $sessions
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
        return view('admin.demoteStudent');
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

            $checkStudentPayment = $this->checkSchoolFees($student, $student->academic_session, $student->level_id);
            if($checkStudentPayment->status != 'success'){
                alert()->error('Oops!', 'Something went wrong with School fees')->persistent('Close');
                return redirect()->back();
            }

            if(!$checkStudentPayment->fullTuitionPayment){
                $amountPaid = $checkStudentPayment->schoolPaymentTransaction->sum('amount_payed');
                $amountToPay = $checkStudentPayment->schoolPayment->structures->sum('amount');
                $balance = $amountToPay - $amountPaid;

                $reference = $this->generateRandomString(10);
                //Create new transaction
                Transaction::create([
                    'student_id' => $student->id,
                    'payment_id' => $checkStudentPayment->schoolPayment->id,
                    'amount_payed' => $balance,
                    'payment_method' => 'Manual/BankTransfer',
                    'reference' => $reference,
                    'session' => $student->academic_session,
                    'status' => null
                ]);
            }

            $student->update([
                'level_id' => $student->level_id + $promotionOffset,
                'academic_session' => $academicSession,
                'credit_load' => null
            ]);
        }
        
        if($programme->save()){
            alert()->success('Student Promoted Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function getStudent(Request $request){
        $validator = Validator::make($request->all(), [
            'reg_number' => 'required',
            'type' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentIdCode = $request->reg_number;
        if($request->type == 'Student'){
            return $this->getSingleStudent($studentIdCode, 'admin.chargeStudent');
        }

        if($request->type == 'Applicant'){
            return $this->getSingleApplicant($studentIdCode, 'admin.chargeStudent');
        }
    }

    public function makeDemoteStudent(Request $request){
        $studentId = $request->student_id;
        $student = Student::find($studentId);

        $validator = Validator::make($request->all(), [
            'new_level' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'admin.demoteStudent');
        }

        if (!$student) {
            alert()->error('Oops!', 'Student record not found')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'admin.demoteStudent');
        }

        $programmeId = $request->programme_id;
        if(!empty($programmeId)){
            $programme = Programme::with('department', 'department.faculty')->where('id', $programmeId)->first();
        }

        $existingDemotion = StudentDemotion::where([
            'student_id' => $studentId,
            'old_level_id' => $student->level_id,
            'new_level_id' => $request->new_level,
            'old_programme_id' => $student->programme_id,
            'new_programme_id' => !empty($programmeId)?$programme->id:$student->programme_id,
            'reason' => $request->reason,
            'academic_session' => $student->academic_session,
        ])->first();
    
        if ($existingDemotion) {
            alert()->error('Oops!', 'Student already demoted')->persistent('Close');
            return $this->getSingleStudent($student->matric_number, 'admin.demoteStudent'); 
        }

        StudentDemotion::create([
            'student_id' => $studentId,
            'old_level_id' => $student->level_id,
            'new_level_id' => $request->new_level,
            'old_programme_id' => $student->programme_id,
            'new_programme_id' => !empty($programmeId)?$programme->id:$student->programme_id,
            'reason' => $request->reason,
            'academic_session' => $student->academic_session,
        ]);

        $student->update([
            'level_id' => $request->new_level,
            'programme_id' => !empty($programmeId)?$programme->id:$student->programme_id,
            'department_id' => !empty($programmeId)?$programme->department->id:$student->department_id,
            'faculty_id' => !empty($programmeId)?$programme->department->faculty->id:$student->faculty_id,
        ]);

        alert()->success('Student Demoted Successfully', '')->persistent('Close');
        return $this->getSingleStudent($student->matric_number, 'admin.demoteStudent');

    }


    public function courseRegistrations (Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];


        $studentRegistrations = StudentCourseRegistration::with('student')->where([
            'academic_session' => $academicSession,
        ])->orderBy('level_id', 'asc')->get();
        
        return view('admin.courseRegistrations', [
            'studentRegistrations' => $studentRegistrations
        ]);

    }

    public function approveReg(Request $request){
        $validator = Validator::make($request->all(), [
            'reg_id' => 'required',
            'type' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$studentCourseReg = StudentCourseRegistration::find($request->reg_id)){
            alert()->error('Oops', 'Invalid Student Registration ')->persistent('Close');
            return redirect()->back();
        }
        
        $studentCourseReg->level_adviser_status = true;
        $studentCourseReg->hod_status = true;

        $studentId = $studentCourseReg->student_id;
        $student = Student::find($studentId);

        $department = Department::find($student->department_id);
        $staffId = $department->hod_id;

        $academicSession = $studentCourseReg->academic_session;
        $otherData = new \stdClass();
        $otherData->staffId = $staffId;
        $otherData->courseRegId = $request->reg_id;
        $otherData->type = $request->type;

        $pdf = new Pdf();
        $courseReg = $pdf->generateCourseRegistration($studentId, $academicSession, $otherData);


        if($studentCourseReg->save()){
            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'Your course registration has been successfully approved. Please proceed to print at your earliest convenience.';

            $mail = new NotificationMail($senderName, $message, $receiverName);
            Mail::to($student->email)->send($mail);
            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'attachment' => null,
                'status' => 0
            ]);

            alert()->success('Registration Approved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function undoReg(Request $request){
        $validator = Validator::make($request->all(), [
            'reg_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$studentCourseReg = StudentCourseRegistration::find($request->reg_id)){
            alert()->error('Oops', 'Invalid Student Registration ')->persistent('Close');
            return redirect()->back();
        }

        $levelId = $studentCourseReg->level_id;
        $studentId = $studentCourseReg->student_id;
        $academicSession = $studentCourseReg->academic_session;

        // Delete registered courses
        CourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession
        ])->forceDelete();

        if($studentCourseReg->forceDelete()){
            $student = Student::find($studentId);

            $fileDirectory = $studentCourseReg->file;
            if (file_exists($fileDirectory)) {
                unlink($fileDirectory);
            } 

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'Your course registration has been successfully reset. Please proceed to re-register as soon as possible.';

            $mail = new NotificationMail($senderName, $message, $receiverName);
            Mail::to($student->email)->send($mail);
            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'status' => 0
            ]);

            alert()->success('Registration reversed', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function resultApprovalStatus(){

        $resultApprovalStatuses = ResultApprovalStatus::get();
        
        return view('admin.resultApprovalStatus', [
            'resultApprovalStatuses' => $resultApprovalStatuses
        ]);
    }
    
    public function addResultApprovalStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|unique:result_approval_statuses',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newStatus = [
            'status' => $request->status,
        ];
        
        if(ResultApprovalStatus::create($newStatus)){
            alert()->success('Result Approval added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateResultApprovalStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'status_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$status = ResultApprovalStatus::find($request->level_id)){
            alert()->error('Oops', 'Invalid Status')->persistent('Close');
            return redirect()->back();
        }

        $status->status = $request->status;

        if($status->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteResultApprovalStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'status_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$status = ResultApprovalStatus::find($request->level_id)){
            alert()->error('Oops', 'Invalid Status ')->persistent('Close');
            return redirect()->back();
        }
        
        if($status->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function departmentForCourses(){

        $department = Department::with('courses')->orderBy('id', 'DESC')->get();
        
        return view('admin.departmentForCourses', [
            'departments' => $department
        ]);
    }

    public function departmentForCourse(Request $request, $slug){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $department = Department::with('courses', 'courses.courseManagement', 'courses.courseManagement.staff', 'programmes', 'programmes.students', 'programmes.academicAdvisers', 'programmes.academicAdvisers.staff', 'programmes.academicAdvisers.level')->where('slug', $slug)->first();

        return view('admin.departmentForCourse', [
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
}

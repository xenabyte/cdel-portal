<?php

namespace App\Http\Controllers\Admin;

use App\Models\ProgrammeCategory;
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
use App\Models\AcademicSessionSetting;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\CourseRegistrationSetting;
use App\Models\ExaminationSetting;
use App\Models\Programme;
use App\Models\Student;
use App\Models\StudentDemotion;
use App\Models\StudentCourseRegistration;
use App\Models\Course;
use App\Models\Transaction;
use App\Models\Notification;
use App\Models\CourseRegistration;
use App\Models\User;
use App\Models\Payment;
use App\Models\StudentExamCard;
use App\Models\User as Applicant;
use App\Models\Allocation;
use App\Models\LevelAdviser;
use App\Models\Staff;
use App\Models\StudentSuspension;
use App\Models\Center;
use App\Models\ProgrammeChangeRequest;

use App\Mail\NotificationMail;
use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use ZipArchive;


class AcademicController extends Controller
{
    //

    public function academicLevel(){

        $academicLevels = AcademicLevel::get();
        
        return view('admin.academicLevel', [
            'academicLevels' => $academicLevels
        ]);
    }

    public function sessionSetup($programmeCategory){

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $sessions = Session::orderBy('id', 'DESC')->get();

        $sessionSetting = AcademicSessionSetting::where('programme_category_id', $programmeCategoryId)->first();
        $examDocketMgt = ExaminationSetting::where('programme_category_id', $programmeCategoryId)->first();

        return view('admin.sessionSetup', [
            'sessions' => $sessions,
            'programmeCategory' => $programmeCategory,
            'sessionSetting' => $sessionSetting,
            'examDocketMgt' => $examDocketMgt
        ]);
    }

    /**
     * Updates the session settings with the provided request data.
     *
     * Validates the request to ensure that 'admission_session', 'academic_session', 
     * and 'application_session' are provided. If a valid 'sessionSetting_id' is 
     * given, the existing session setting is updated; otherwise, a new session 
     * setting is created. The method then checks if the session attributes have 
     * changed and updates them accordingly. If the session settings are saved 
     * successfully, a success alert is displayed; otherwise, an error alert is 
     * shown.
     *
     * @param \Illuminate\Http\Request $request The HTTP request containing session data.
     * @return \Illuminate\Http\RedirectResponse Redirects back with an alert message.
     */

    public function setSession(Request $request){
        $validator = Validator::make($request->all(), [
            'admission_session' => 'nullable',
            'academic_session' => 'nullable',
            'application_session' => 'nullable',
            'programme_category_id' => 'required' 
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $sessionSetting = new AcademicSessionSetting;

        if (!empty($request->sessionSetting_id)) {
            $sessionSetting = AcademicSessionSetting::where('id', $request->sessionSetting_id)
                ->where('programme_category_id', $request->programme_category_id)
                ->first();

            if (!$sessionSetting) {
                alert()->error('Oops', 'Invalid Session Setting Information')->persistent('Close');
                return redirect()->back();
            }
        }

        $sessionSetting->programme_category_id = $request->programme_category_id;

        if (!empty($request->admission_session) && $request->admission_session != $sessionSetting->admission_session) {
            $sessionSetting->admission_session = $request->admission_session;
        }

        if (!empty($request->academic_session) && $request->academic_session != $sessionSetting->academic_session) {
            $sessionSetting->academic_session = $request->academic_session;
        }

        if (!empty($request->application_session) && $request->application_session != $sessionSetting->application_session) {
            $sessionSetting->application_session = $request->application_session;
        }

        if ($sessionSetting->save()) {
            alert()->success('Changes Saved', 'Session changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function setRegistrarSetting(Request $request){
        $validator = Validator::make($request->all(), [
            'registrar_name' => 'nullable',
            'registrar_signature' => 'nullable',
        ]);


        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $sessionSetting = new SessionSetting;
        if(!empty($request->sessionSetting_id) && !$sessionSetting = SessionSetting::find($request->sessionSetting_id)){
            alert()->error('Oops', 'Invalid Session Setting Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->registrar_name) &&  $request->registrar_name != $sessionSetting->registrar_name){
            $sessionSetting->registrar_name = $request->registrar_name;
        }

        if(!empty($request->campus_wide_message) &&  $request->campus_wide_message != $sessionSetting->campus_wide_message){
            $sessionSetting->campus_wide_message = $request->campus_wide_message;
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

    public function setFeeStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'school_fee_status' => 'nullable',
            'accomondation_booking_status' => 'nullable',
            'resumption_date' => 'nullable'
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $sessionSetting = new AcademicSessionSetting;
        if(!empty($request->sessionSetting_id) && !$sessionSetting = AcademicSessionSetting::find($request->sessionSetting_id)){
            alert()->error('Oops', 'Invalid Session Setting Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->school_fee_status) &&  $request->school_fee_status != $sessionSetting->school_fee_status){
            $sessionSetting->school_fee_status = $request->school_fee_status;
        }

        if(!empty($request->accomondation_booking_status) &&  $request->accomondation_booking_status != $sessionSetting->accomondation_booking_status){
            $sessionSetting->accomondation_booking_status = $request->accomondation_booking_status;
        }

        if(!empty($request->resumption_date) &&  $request->resumption_date != $sessionSetting->resumption_date){
            $sessionSetting->resumption_date = $request->resumption_date;
        }

        if($sessionSetting->save()){
            alert()->success('Changes Saved', 'Fee(s) status saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function setCampusWideMessage(Request $request){
        $validator = Validator::make($request->all(), [
            'campus_wide_message' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $sessionSetting = new SessionSetting;
        if(!empty($request->sessionSetting_id) && !$sessionSetting = SessionSetting::find($request->sessionSetting_id)){
            alert()->error('Oops', 'Invalid Session Setting Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->campus_wide_message) &&  $request->campus_wide_message != $sessionSetting->campus_wide_message){
            $sessionSetting->campus_wide_message = $request->campus_wide_message;
        }

        if($sessionSetting->save()){
            alert()->success('Changes Saved', 'Message set successfully')->persistent('Close');
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

    public function deans(){
        $faculties = Faculty::with('staffs')->get();

        return view('admin.deans', [
            'faculties' => $faculties
        ]);
    }

    public function hods(){
        $departments = Department::with('staffs')->get();


        return view('admin.hods', [
            'departments' => $departments
        ]);
    }
    
    public function facultyOfficers(){
        $faculties = Faculty::with('departments')->get();
        $staffMembers = Staff::all();

        return view('admin.facultyOfficers', [
            'faculties' => $faculties,
            'staffMembers' => $staffMembers
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
            'description' => 'nullable',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = md5(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name))));

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
            'code' => $request->code
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

        if(!empty($request->code) &&  $request->code != $department->code){
            $department->code = strtoupper($request->code);
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

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $request->category)->first();
        $programmeCategoryId = $programmeCategory->id;
        if (empty($programmeCategory->academicSessionSetting)) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;


        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name.' '.$programmeCategory->category)));

        $newAddProgramme = [
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
            'slug' => $slug,
            'duration' => $request->duration,
            'name' => $request->name,
            'category_id' => $request->category,
            'award' => $request->award,
            'max_duration' => 0,
            'code' => $request->code,
            'academic_session' => $academicSession
        ];
        
        // Check if a record with the same 'name' and 'code' already exists
        $existingProgramme = Programme::where('name', $request->name)
            ->where('category_id', $request->category)
            ->exists();
        
        if ($existingProgramme) {
            alert()->warning('Duplicate Programme', 'A programme with this name and category already exists')->persistent('Close');
            return redirect()->back();
        }
        
        // Create a new programme if it doesn't already exist
        if (Programme::create($newAddProgramme)) {
            alert()->success('Programme Added', 'A new programme has been added')->persistent('Close');
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

        if(!empty($request->award) && $programme->award != $request->award){
            $programme->award = $request->award;
        }

        if(!empty($request->code) && $programme->code != $request->code){
            $programme->code = $request->code;
        }

        if(!empty($request->max_duration) && $programme->max_duration != $request->max_duration){
            $programme->max_duration = $request->max_duration;
        }

        if(!empty($request->duration) && $programme->duration != $request->duration){
            $programme->duration = $request->duration;
        }

        if(!empty($request->minimum_cgpa) && $programme->minimum_cgpa != $request->minimum_cgpa){
            $programme->minimum_cgpa = $request->minimum_cgpa;
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
        $categories = ProgrammeCategory::all();

        return view('admin.department', [
            'department' => $department,
            'levels' => $levels,
            'categories' => $categories,
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

        if(!$department = Department::find($request->department_id)){
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

    // obsolete function
    // public function courseRegMgt(Request $request){

    //     $globalData = $request->input('global_data');
    //     $academicSession = $globalData->sessionSetting['academic_session'];

    //     $programmes = Programme::with(['students' => function ($query) {
    //         $query->where('is_active', true)
    //               ->where('is_passed_out', false)
    //               ->where('is_rusticated', false);
    //     }, 'programmeCategory'])
    //     ->where(function ($query) use ($academicSession) {
    //         $query->where('academic_session', $academicSession)
    //               ->orWhereNull('academic_session');
                  
    //     })
    //     ->get();

    //     $courseRegMgt = CourseRegistrationSetting::first();

    //     return view('admin.courseRegMgt', [
    //         'courseRegMgt' => $courseRegMgt,
    //         'programmes' => $programmes
    //     ]);
    // }

    // rather obsolete
    // public function manageCourseReg(Request $request){
    //     $validator = Validator::make($request->all(), [
    //         'programme_id' => 'required',
    //         'course_registration' => 'required',
    //     ]);

    //     $programmeId = $request->programme_id;
    //     $courseRegistration = $request->course_registration;

    //     $programme = Programme::where('id', $programmeId)
    //     ->first();

    //     if(empty($programme)){
    //         alert()->error('Oops', 'Invalid Programme')->persistent('Close');
    //         return redirect()->back();
    //     }

    //     $programme->course_registration = $courseRegistration;

    //     if($programme->save()){
    //         alert()->success('Changes Saved', 'Course registration changes saved successfully')->persistent('Close');
    //         return redirect()->back();
    //     }

    //     alert()->error('Oops!', 'Something went wrong')->persistent('Close');
    //     return redirect()->back();
    // }

    public function setStudentCourseRegStatus(Request $request){
        $validator = Validator::make($request->all(), [
            'level_adviser_id' => 'required',
            'course_registration' => 'required',
        ]);

        $courseRegistration = $request->course_registration;

        $levelAdviser = LevelAdviser::find($request->level_adviser_id);
        if(!$levelAdviser){
            alert()->error('Oops', 'Invalid Level Adviser ')->persistent('Close');
            return redirect()->back();
        }

        $levelAdviser->course_registration = $courseRegistration;

        if($levelAdviser->save()){
            alert()->success('Changes Saved', 'Course registration changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function resetStudentCourseReg(Request $request){
        $validator = Validator::make($request->all(), [
            'level_adviser_id' => 'required',
            'programme_category_id' => 'required',
        ]);

        $levelAdviser = LevelAdviser::find($request->level_adviser_id);
        if(!$levelAdviser){
            alert()->error('Oops', 'Invalid Level Adviser ')->persistent('Close');
            return redirect()->back();
        }

        
        $programmeId = $levelAdviser->programme_id;
        $levelId = $levelAdviser->level_id;
        $academicSession = $levelAdviser->academic_session;

        $programmeStudents = Student::where('programme_id', $programmeId)
            ->where('programme_category_id', $request->programme_category_id)
            ->where('level_id', $levelId)
            ->get();

        $programmeStudentIds = $programmeStudents->pluck('id')->toArray();
        $studentCourseRegistrations = StudentCourseRegistration::whereIn('student_id', $programmeStudentIds)->get();
        
        foreach ($studentCourseRegistrations as $studentCourseReg){
            $studentId = $studentCourseReg->student_id;

            $courseRegistrations = CourseRegistration::where('student_id', $studentId)
            ->where('programme_category_id', $request->programme_category_id)
            ->where('academic_session', $academicSession)
            ->get();

            foreach ($courseRegistrations as $courseReg){
                $courseId = $courseReg->course_id;

                $carryOver = CourseRegistration::where([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'grade' => 'F',
                ])
                ->where('academic_session', '!=', $academicSession)
                ->first();

                if ($carryOver) {
                    $carryOver->re_reg = null;
                    $carryOver->save();
                }
            }

            // Delete registered courses
            CourseRegistration::where([
                'student_id' => $studentId,
                'academic_session' => $academicSession
            ])->forceDelete();
    
            $fileDirectory = $studentCourseReg->file;
            if (file_exists($fileDirectory)) {
                unlink($fileDirectory);
            }

            if($studentCourseReg->forceDelete()){
                $student = Student::find($studentId); 

                $senderName = env('SCHOOL_NAME');
                $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
                $message = 'Your course registration has been successfully reset. Please proceed to re-register as soon as possible.';

                $mail = new NotificationMail($senderName, $message, $receiverName);
                if(env('SEND_MAIL')){
                    Mail::to($student->email)->send($mail);
                }
                Notification::create([
                    'student_id' => $student->id,
                    'description' => $message,
                    'status' => 0
                ]);
            }
        }

        alert()->success('Good Job', 'Course registration reset successfully')->persistent('Close');
        return redirect()->back();
    }

    //rather obsolete
    public function resetCourseReg(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
        ]);

        $programmeId = $request->programme_id;
        $programmeCategoryId = $request->programme_category_id;

        $programme = Programme::where('id', $programmeId)
        ->first();

        if(empty($programme)){
            alert()->error('Oops', 'Invalid Programme')->persistent('Close');
            return redirect()->back();
        }

        $programmeStudents = Student::where('programme_id', $programmeId)->where('programme_category_id', $programmeCategoryId)->get();
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

    // rather obsolete
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

    public function setExamSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'exam_docket_status' => 'nullable',
            'academic_session' => 'nullable',
            'result_processing_status' => 'nullable',
            'test_processing_status' => 'nullable',
            'semester' => 'nullable',
            'programme_category_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $examSetting = new ExaminationSetting;

        // Check for existing record using both ID and programme_category_id
        if (!empty($request->examSetting_id)) {
            $examSetting = ExaminationSetting::where('id', $request->examSetting_id)
                ->where('programme_category_id', $request->programme_category_id)
                ->first();

            if (!$examSetting) {
                alert()->error('Oops', 'Invalid Exam Setting Information')->persistent('Close');
                return redirect()->back();
            }
        }

        $examSetting->programme_category_id = $request->programme_category_id;

        if (!empty($request->exam_docket_status) && $request->exam_docket_status != $examSetting->exam_docket_status) {
            $examSetting->exam_docket_status = $request->exam_docket_status;
        }

        if (!empty($request->academic_session) && $request->academic_session != $examSetting->academic_session) {
            $examSetting->academic_session = $request->academic_session;
        }

        if (!empty($request->semester) && $request->semester != $examSetting->semester) {
            $examSetting->semester = $request->semester;
        }

        if (!empty($request->result_processing_status) && $request->result_processing_status != $examSetting->result_processing_status) {
            $examSetting->result_processing_status = $request->result_processing_status;
        }

        if (!empty($request->test_processing_status) && $request->test_processing_status != $examSetting->test_processing_status) {
            $examSetting->test_processing_status = $request->test_processing_status;
        }

        if ($examSetting->save()) {
            alert()->success('Changes Saved', 'Exam settings saved successfully')->persistent('Close');
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

    public function allStudents($programmeCategory){

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $students = Student::
            with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
            ->where('programme_category_id', $programmeCategoryId)
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->get();
        
        return view('admin.allStudents', [
            'students' => $students,
            'programmeCategory' => $programmeCategory
        ]);
    }

    public function studentProfile(Request $request, $slug){
        $academicLevels = AcademicLevel::orderBy('id', 'desc')->get();
        $sessions = Session::orderBy('id', 'desc')->get();
        $studyCenters = Center::all();

         $student = Student::
            withTrashed()
            ->with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
            ->where('slug', $slug)
            ->first();
        
        $programmeCategoryId = $student->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $applicationSession = $programmeCategory->academicSessionSetting->application_session ?? null;

        if (!$applicationSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $referalCode = $student->referral_code;
        $applicants = [];
        if(!empty($referalCode)){
            $applicants = Applicant::with('student')->where('referrer', $referalCode)->where('academic_session', $applicationSession)->get();
        }
        
        return view('admin.studentProfile', [
            'student' => $student,
            'academicLevels' => $academicLevels,
            'sessions' => $sessions,
            'applicants' => $applicants,
            'studyCenters' => $studyCenters
        ]);
    }

    public function massPromotion(Request $request, $programmeCategory){
       
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;      
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;
        if (!$academicSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $programmes = Programme::with(['students' => function ($query) use ($programmeCategoryId) {
            $query->where('is_active', true)
                ->where('is_rusticated', false)
                ->where('programme_category_id', $programmeCategoryId);
        }, 'programmeCategory'])
        ->where(function ($query) use ($academicSession, $programmeCategoryId) {
            $query->where('category_id', $programmeCategoryId)
                ->where('academic_session', '!=', $academicSession)
                ->orWhereNull('academic_session');
        })
        ->get();
         
        return view('admin.massPromotion', [
            'programmes' => $programmes,
            'programmeCategory' => $programmeCategory
        ]);
    }

    public function demoteStudent(){        
        return view('admin.demoteStudent');
    }

    public function promoteStudent(Request $request){
        $programmeId = $request->programme_id;
        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;        
        if (!$academicSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }
        

        $programme = Programme::with(['students' => function ($query) use ($programmeCategoryId){
            $query->where('is_active', true)
                  ->where('is_passed_out', false)
                  ->where('is_rusticated', false)
                  ->where('programme_category_id', $programmeCategoryId);
        }, 'programmeCategory'])
        ->where(function ($query) use ($academicSession, $programmeCategoryId) {
            $query->where('category_id', $programmeCategoryId)
                ->where('academic_session', '!=', $academicSession)
                ->orWhereNull('academic_session');
        })
        ->where('id', $programmeId)
        ->first();


        $programme->academic_session = $academicSession;

        $students = $programme->students;
        $promotionOffset = 1;
        foreach ($students as $student) {

            $checkStudentPayment = $this->checkSchoolFees($student, $student->academic_session, $student->level_id);
            if($checkStudentPayment->status != 'success'){
                alert()->error('Oops!', 'Something went wrong with School fees')->persistent('Close');
                return redirect()->back();
            }

            Allocation::where('student_id', $student->id)->where('academic_session', $student->academic_session)->update(['release_date' => Carbon::now()]);

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
                    'payment_method' => 'Balance brought forward',
                    'reference' => $reference,
                    'session' => $student->academic_session,
                    'status' => null
                ]);
            }

            $promotionLevel = $student->level_id + $promotionOffset;
            $promotionCheck = $student->canPromote();

            if (!$promotionCheck['promotion']['status']) {
                $promotionLevel = $student->level_id;

                $senderName = env('SCHOOL_NAME');
                $receiverName = $student->applicant->lastname . ' ' . $student->applicant->othernames;

                // Compile detailed reason message
                $reasonMessage = implode("\n- ", $promotionCheck['promotion']['reasons']);
                $message = "We regret to inform you that you have not met the required academic criteria for promotion to the next level.\n\n"
                    . "Reason(s):\n- " . $reasonMessage . "\n\n"
                    . "Please review your academic performance and consult with your department for guidance on improving your standing. "
                    . "For further inquiries, kindly visit the academic office.";

                try {
                    $mail = new NotificationMail($senderName, $message, $receiverName);
                    if(env('SEND_MAIL')){
                        Mail::to($student->email)->send($mail);
                    }
                } catch (\Exception $e) {
                    Log::error('Email Notification Failed: ' . $e->getMessage());
                }

                // Save notification in the database
                Notification::create([
                    'student_id' => $student->id,
                    'description' => $message,
                    'attachment' => null,
                    'status' => 0
                ]);

                $student->update([
                    'batch' => 'A',
                    'status' => "Withdrawn",
                    'academic_session' => $academicSession,
                    'credit_load' => null
                ]);
            }

            if (!$promotionCheck['professional_exam']['status']) {
                $examReasonMessage = implode("\n- ", $promotionCheck['professional_exam']['reasons']);
                $examMessage = "This is to inform you that you are currently not eligible to sit for the upcoming professional examination due to the following reason(s):\n\n"
                    . "- " . $examReasonMessage . "\n\n"
                    . "Kindly ensure you resolve these issues as soon as possible. For more information or clarification, please contact your department or visit the academic office.";
            
                try {
                    $mail = new NotificationMail($senderName, $examMessage, $receiverName);
                    if(env('SEND_MAIL')){
                        Mail::to($student->email)->send($mail);
                    }
                } catch (\Exception $e) {
                    Log::error('Exam Notification Email Failed: ' . $e->getMessage());
                }
            
                Notification::create([
                    'student_id' => $student->id,
                    'description' => $examMessage,
                    'attachment' => null,
                    'status' => 0
                ]);
            }
        

            $student->update([
                'batch' => 'A',
                'level_id' => $promotionLevel,
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

        $url = empty($request->url) ? 'admin.chargeStudent' : $request->url;

        $studentIdCode = $request->reg_number;
        if($request->type == 'Student'){
            return $this->getSingleStudent($studentIdCode, $url);
        }

        if($request->type == 'Applicant'){
            return $this->getSingleApplicant($studentIdCode, $url);
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

        $programmeId = $request->new_programme;
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

        CourseRegistration::where('student_id', $studentId)
            ->where('level_id', $request->new_level)
            ->delete();

        if(!empty($request->new_programme) && ($request->new_programme != $student->programme_id)){
            $student->programme_id = $request->new_programme;
            $academicSession = $student->academic_session;
            $studentId = $student->id;
            $applicantId = $student->user_id;
            $applicant = User::find($applicantId);
            $applicationType = $applicant->application_type;

            $type = Payment::PAYMENT_TYPE_SCHOOL;

            if($applicationType != 'UTME' && ($student->level_id == 2|| $student->level_id == 3)){
                $type = Payment::PAYMENT_TYPE_SCHOOL_DE;
            }

            $schoolPayment = Payment::with('structures')
                ->where('type', $type)
                ->where('programme_id', $student->programme_id)
                ->where('level_id', $student->level_id)
                ->where('academic_session', $academicSession)
                ->first();

            if(!$schoolPayment){
                alert()->success('Programme school fees not set, check with ICT admin', '')->persistent('Close');
                return $this->getSingleStudent($student->matric_number, $request->url);
            }

            Transaction::where('student_id', $studentId)->where('session', $academicSession)->where('status', 1)->update(['payment_id' => $schoolPayment->id]);
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

    public function courseRegistrations(Request $request, $programmeCategory, $academicSession=null){
        if(!empty($academicSession)){
            $academicSession = str_replace('-', '/', $academicSession);
        }

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;
        
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;
        if (!$academicSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $studentRegistrations = StudentCourseRegistration::with('student')
            ->where('academic_session', $academicSession)
            ->where('programme_category_id', $programmeCategoryId)
            ->orderBy('level_id', 'asc')
            ->get();

        $studentIds = $studentRegistrations->pluck('student_id');
        $pendingStudents = Student::with('applicant')
            ->where('programme_category_id', $programmeCategoryId)
            ->whereNotNull('matric_number')
            ->whereNotIn('id', $studentIds)->get();

        $programmes = Programme::get();
        $academicLevels = AcademicLevel::get();

        return view('admin.courseRegistrations', [
            'studentRegistrations' => $studentRegistrations,
            'pendingStudents' => $pendingStudents,
            'academicSession' => $academicSession,
            'sessions' => Session::orderBy('id', 'DESC')->get(),
            'programmes' => $programmes,
            'academicLevels' => $academicLevels,
            'programmeCategory' => $programmeCategory
        ]);
    }

    public function downloadStudentCourseRegistrations(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
            'level_id' => 'required',
            'academic_session' => 'required',
            'programme_category_id' => 'required'
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentIds = Student::where('programme_id', $request->programme_id)
        ->where('programme_category_id', $request->programme_category_id)
        ->pluck('id');

        $studentRegistrations = StudentCourseRegistration::whereIn('student_id', $studentIds)
            ->where('academic_session', $request->academic_session)
            ->where('programme_category_id', $request->programme_category_id)
            ->where('level_id', $request->level_id)
            ->get();

        if ($studentRegistrations->isEmpty()) {
            alert()->error('Error', 'No registrations found for the selected criteria')->persistent('Close');
            return redirect()->back();
        }
        
        $programme = Programme::find($request->programme_id);
        $level = $request->level_id * 100 .' level student course registrations '.$request->academic_session;

        $folderName  = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $programme->name.' '.$level)));
        $folderPath  = public_path('uploads/files/'.$folderName);
        if (!file_exists($folderPath )) {
            mkdir($folderPath , 0755, true);
        }

        // Save each student's file into the folder
        foreach ($studentRegistrations as $registration) {
            if ($registration->file && file_exists($registration->file)) {
                // Get the file from storage
                $file = $registration->file;
                // Copy the file into the new folder (use original file name or customize it)
                File::copy($file, $folderPath . '/' . basename($registration->file));
            }
        }


      
       // Now that all files are saved in the folder, we will zip the folder
        $zipFileName = $folderName .'.zip'; // The name of the ZIP file
        $zipFilePath = public_path('uploads/files/'.$zipFileName); // Full path to the ZIP file

        $zip = new ZipArchive;
        $zipOpenResult = $zip->open($zipFilePath, ZipArchive::CREATE | ZipArchive::OVERWRITE); // Use full path

       if ($zipOpenResult === TRUE) {
            // Add all files from the folder to the ZIP
            $files = File::files($folderPath);
            foreach ($files as $file) {
                $zip->addFile($file->getRealPath(), $file->getFilename());
            }
            $zip->close();

            // Delete the folder after zipping to avoid clutter
            File::deleteDirectory($folderPath);

            // Return the ZIP file as a response for download
            return response()->download($zipFilePath)->deleteFileAfterSend(true);
        } else {
            alert()->error('Error', 'Failed to create ZIP file')->persistent('Close');
            return redirect()->back();
        }
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

        $studentCourseReg->file = $courseReg;

        if($studentCourseReg->save()){
            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'Your course registration has been successfully approved. Please proceed to print at your earliest convenience.';

            $mail = new NotificationMail($senderName, $message, $receiverName);
            if(env('SEND_MAIL')){
                Mail::to($student->email)->send($mail);
            }
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

        $registeredCourses =  CourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession
        ])->get();

        foreach ($registeredCourses as $registeredCourse) { 
            $courseId = $registeredCourse->course_id;

            $checkCarryOver = CourseRegistration::where([
                'student_id' => $studentId,
                'course_id' => $courseId,
                'grade' => 'F',
            ])->first();

            if(!empty($checkCarryOver)){
                $checkCarryOver->re_reg = null;
                $checkCarryOver->save();
            }

            // Delete the registered course
            $registeredCourse->forceDelete();
        }


        $fileDirectory = $studentCourseReg->file;
        if (file_exists($fileDirectory)) {
            unlink($fileDirectory);
        }

        if($studentCourseReg->forceDelete()){
            $student = Student::find($studentId); 

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'Your course registration has been successfully reset. Please proceed to re-register as soon as possible.';

            $mail = new NotificationMail($senderName, $message, $receiverName);
            if(env('SEND_MAIL')){
                Mail::to($student->email)->send($mail);
            }
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
            'departments' => $department,
        ]);
    }

    public function departmentForCourse(Request $request, $slug){

        $programmeCategories = ProgrammeCategory::with('academicSessionSetting')->get();

        $department = Department::with('courses', 'courses.courseManagement', 'courses.courseManagement.staff', 'programmes', 'programmes.students', 'programmes.academicAdvisers', 'programmes.academicAdvisers.staff', 'programmes.academicAdvisers.level')->where('slug', $slug)->first();

        return view('admin.departmentForCourse', [
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

    public function genExamDocket(Request $request){
        $studentId = $request->student_id;
        $student = Student::find($studentId);
        $programmeCategoryId = $student->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;
        $semester = $programmeCategory->examSetting->semester;
        
        if (!$academicSession || !$semester) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $courseRegs = CourseRegistration::with('course')
            ->where('student_id', $studentId)
            ->where('academic_session', $academicSession)
            ->where('total', null)
            ->where('semester', $semester)
            ->where('status', 'approved')
            ->get();

        if(empty($courseRegs)){
            alert()->error('Oops!', 'No approved course registration for this semester and session.')->persistent('Close');
            return redirect()->back();
        }

        try {

            $pdf = new Pdf();
            $examDocket = $pdf->generateExamDocket($studentId, $academicSession, $semester);

             //create record for file
            $studentExamCard = StudentExamCard::where([
                'student_id' => $studentId,
                'academic_session' => $academicSession,
                'semester' => $semester
            ])->first();


            if(empty($studentExamCard)){
                $studentExamCard = StudentExamCard::create([
                    'student_id' => $studentId,
                    'academic_session' => $academicSession,
                    'semester' => $semester,
                    'file' => $examDocket,
                    'level_id' => $student->level_id
                ]);
            }else{
                $fileDirectory = $studentExamCard->file;
                if (file_exists($fileDirectory)) {
                    unlink($fileDirectory);
                } 
                $studentExamCard->file = $examDocket;
                $studentExamCard->save();
            }


            return redirect(asset($examDocket));

        } catch (\Exception $e) {
            Log::info($e);
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return redirect()->back();
        }

    }

    public function expelStudent(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$student = Student::find($request->student_id)){
            alert()->error('Oops', 'Invalid Student ')->persistent('Close');
            return redirect()->back();
        }
        
        if($student->delete()){
            alert()->success('Disable Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }

    public function enableStudent(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$student = Student::withTrashed()->find($request->student_id)){
            alert()->error('Oops', 'Invalid Student ')->persistent('Close');
            return redirect()->back();
        }
        
        if($student->restore()){
            alert()->success('Enable Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }

    public function deletedStudents(){

        $deletedStudents =  Student::onlyTrashed()->with('faculty', 'department', 'programme')->get();

        return view('admin.deletedStudents', [
            'deletedStudents' => $deletedStudents
        ]);
    }

    /**
     * Get all suspended students.
     */
    public function suspendedStudents()
    {
        $suspensions = StudentSuspension::with('student')->whereNull('end_date')->get();

        return view('admin.suspendedStudents', [
            'suspensions' => $suspensions
        ]);
    }

    /**
     * Get all expelled students.
     */
    public function expelledStudents()
    {
        $expelledStudents = Student::where('academic_status', 'expelled')->get();

        return view('admin.expelledStudents', [
            'expelledStudents' => $expelledStudents
        ]);
    }


    public function programmeChangeRequests(Request $request){
    

        $programmeChangeRequests = ProgrammeChangeRequest::get();

        return view('admin.programmeChangeRequests', [
            'programmeChangeRequests' => $programmeChangeRequests,
        ]);
    }

    public function viewProgrammeChangeRequest(Request $request, $slug){

        $programmeChangeRequest = ProgrammeChangeRequest::where('slug', $slug)->first();

        return view('admin.viewProgrammeChangeRequest', [
            'programmeChangeRequest' => $programmeChangeRequest
        ]);
    }

    public function manageProgrammeChangeRequest(Request $request)
    {
        $request->validate([
            'programme_change_request_id' => 'required|exists:programme_change_requests,id',
            'role' => 'required|in:old_hod,new_hod,old_dean,new_dean,dap,registrar',
            'status' => 'required|in:approved,declined',
        ]);

        $changeRequest = ProgrammeChangeRequest::findOrFail($request->programme_change_request_id);
        
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

            $academicSession = $student->programmeCategory->academicSessionSetting->academic_session ?? null;
            if (!$academicSession) {
                alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
                return redirect()->back();
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

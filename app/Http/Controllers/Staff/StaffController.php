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
use App\Models\GradeScale;
use App\Models\CourseRegistration;
use App\Models\Role;
use App\Models\StaffRole;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\LevelAdviser;


use App\Mail\NotificationMail;

use App\Libraries\Result\Result;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StaffController extends Controller
{
    //

    public function index(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $referalCode = $staff->referral_code;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $applicants = Applicant::with('student')->where('referrer', $referalCode)->where('academic_session', $applicationSession)->get();

        return view('staff.home', [
            'applicants' => $applicants,
        ]);
    }

    public function profile(Request $request){

        return view('staff.profile');
    }

    public function saveBioData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'dob' => 'required',
            'religion' => 'required',
            'gender' => 'required',
            'marital_status' => 'required',
            'nationality' => 'required',
            'state' => 'required',
            'lga' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        
        if(!empty($request->dob) && $request->dob != $staff->dob){
            $staff->dob = $request->dob;
        }

        if(!empty($request->religion) && $request->religion != $staff->religion){
            $staff->religion = $request->religion;
        }

        if(!empty($request->gender) && $request->gender != $staff->gender){
            $staff->gender = $request->gender;
        }

        if(!empty($request->marital_status) && $request->marital_status != $staff->marital_status){
            $staff->marital_status = $request->marital_status;
        }

        if(!empty($request->nationality) && $request->nationality != $staff->nationality){
            $staff->nationality = $request->nationality;
        }

        if(!empty($request->state) && $request->state != $staff->state_of_origin){
            $staff->state = $request->state;
        }

        if(!empty($request->lga) && $request->lga != $staff->lga){
            $staff->lga = $request->lga;
        }

        if($staff->save()){
            alert()->success('Changes Saved', 'Bio data saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updatePassword (Request $request) {

        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'password' => 'required',
            'confirm_password' => 'required'
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $staff = Auth::guard('staff')->user();


        if(\Hash::check($request->old_password, Auth::guard('staff')->user()->password)){
            if($request->new_password == $request->confirm_password){
                $staff->password = bcrypt($request->new_password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }
        }else{
            alert()->error('Oops', 'Wrong old password, Try again with the right one')->persistent('Close');
            return redirect()->back();
        }

        if($staff->update()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function mentee(Request $request){

        return view('staff.mentee');
    }

    public function courses(Request $request){

        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $courses = Course::with('level')->where('staff_id', $staffId)->get();

        return view('staff.courses', [
            'courses' => $courses,
        ]);
    }

    public function studentCourses(Request $request){

        $programmes = Programme::get();
        $academicLevels = AcademicLevel::get();

        return view('staff.studentCourses',[
            'programmes' => $programmes,
            'academicLevels' => $academicLevels
        ]);
    }

    public function getStudentCourses(Request $request){

        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
            'level_id' => 'required',
            'semester' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $courses = Course::with('staff')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('semester', $request->semester)->get();
        $programme = Programme::find($request->programme_id);
        $academicLevel = AcademicLevel::find($request->level_id);

        $programmes = Programme::get();
        $academicLevels = AcademicLevel::get();

        return view('staff.studentCourses',[
            'programmes' => $programmes,
            'academicLevels' => $academicLevels,
            'courses' => $courses,
            'academiclevel' => $academicLevel,
            'programme' => $programme,
        ]);
    }

    public function courseDetail(Request $request, $id){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $course = Course::with('level', 'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('id', $id)->first();
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

    public function studentProfile(Request $request, $slug){
        $student = Student::with('applicant', 'applicant.utmes', 'programme', 'transactions')->where('slug', $slug)->first();

        return view('staff.studentProfile', [
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

    public function staffUploadResult(Request $request){
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $validator = Validator::make($request->all(), [
            'result' => 'required|file',
            'course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $file = $request->file('result');
        $fileExtension = $file->getClientOriginalExtension();
        
        if ($fileExtension != 'csv') {
            alert()->error('Invalid file format, only CSV is allowed', '')->persistent('Close');
            return redirect()->back();
        }

        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $courseId = $request->course_id;

    
        $file = $request->file('result');
        $processResult = Result::processResult($file, $courseId, $globalData);

        if($processResult){
            alert()->success('Student scores updated successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('No file uploaded. Result not processed', '')->persistent('Close');
        return redirect()->back();
    }

    public function updateStudentResult(Request $request){
        $validator = Validator::make($request->all(), [
            'test' => 'required',
            'exam' => 'required',
            'course_id' => 'required',
            'matric_number' => 'required',
        ]);

        $matricNumber = $request->matric_number;

        if(!$student = Student::where('matric_number', $matricNumber)->first()){
            alert()->error('Invalid Matric Number', '')->persistent('Close');
            return redirect()->back();
        }
        $studentId = $student->id;
        $courseId = $request->course_id;

        $studentRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'course_id' => $courseId,
        ])->first();

        if(!$studentRegistration){
            alert()->error('Student didnt enroll for this course', '')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($studentRegistration->result_approval_id)){
            alert()->error('Result already approved', 'Visit the ICT with relevant approval for modification')->persistent('Close');
            return redirect()->back();
        }

        $testScore = $request->test;
        $examScore = $request->exam;
        $totalScore = $testScore + $examScore;
        $grading = GradeScale::computeGrade($totalScore);
        $grade = $grading->grade;
        $points = $grading->point;
        
      
        $studentRegistration->ca_score = $testScore;
        $studentRegistration->exam_score = $examScore;
        $studentRegistration->total = $totalScore;
        $studentRegistration->grade = $grade;
        $studentRegistration->points = $points;
        if($studentRegistration->save()){
            alert()->success('Student scores updated successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function roleAllocation(Request $request){

        return view('staff.roleAllocation');
    }

    public function staff(Request $request){

        $staff  = Staff::withTrashed()->with('faculty', 'acad_department')->get();

        return view('staff.staff', [
            'staff' => $staff
        ]);
    }

    public function deleteRole(Request $request){
        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$role = Role::find($request->role_id)){
            alert()->error('Oops', 'Invalid Role ')->persistent('Close');
            return redirect()->back();
        }
        
        if($role->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function singleStaff(Request $request, $slug){

        $staff  = Staff::withTrashed()->with('faculty', 'acad_department', 'staffRoles', 'staffRoles.role')->where('slug', $slug)->first();
        $roles  = Role::get();
        $departments = Department::where('faculty_id', $staff->faculty_id)->get();

        return view('staff.singleStaff', [
            'singleStaff' => $staff,
            'roles' => $roles,
            'departments' => $departments
        ]);
    }

    public function assignRole(Request $request){
        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
            'staff_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$role = Role::find($request->role_id)){
            alert()->error('Oops', 'Invalid Role ')->persistent('Close');
            return redirect()->back();
        }

        $newRole = [
            'role_id' => $request->role_id,
            'staff_id' => $request->staff_id,
        ];

        $staffDescription = "Congratulations, you have been assigned as  ".$role->role;
            Notification::create([
                'staff_id' =>  $request->staff_id,
                'description' => $staffDescription,
                'status' => 0
            ]);
        
        if(StaffRole::create($newRole)){
            alert()->success('Role assigned successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function unAssignRole(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_role_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$staffRole = StaffRole::find($request->staff_role_id)){
            alert()->error('Oops', 'Invalid Staff Role ')->persistent('Close');
            return redirect()->back();
        }
        
        if($staffRole->forceDelete()){
            alert()->success('Role  unassigned successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function disableStaff(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }
        
        if($staff->delete()){
            alert()->success('Disable Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }

    public function enableStaff(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$staff = Staff::withTrashed()->find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }
        
        if($staff->restore()){
            alert()->success('Enable Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }

    public function assignDeanToFaculty(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'faculty_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }

        if(!$faculty = Faculty::find($request->faculty_id)){
            alert()->error('Oops', 'Invalid Faculty ')->persistent('Close');
            return redirect()->back();
        }
        $faculty->dean_id = $staff->id;
        if($faculty->save()){
            alert()->success('Dean assigned to Faculty', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }

    public function assignSubDeanToFaculty(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'faculty_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }

        if(!$faculty = Faculty::find($request->faculty_id)){
            alert()->error('Oops', 'Invalid Faculty ')->persistent('Close');
            return redirect()->back();
        }
        $faculty->sub_dean_id = $staff->id;
        if($faculty->save()){
            alert()->success('Sub Dean assigned to Faculty', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }
    

    public function assignHodToDepartment(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'department_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }

        if(!$department = Department::find($request->department_id)){
            alert()->error('Oops', 'Invalid Department ')->persistent('Close');
            return redirect()->back();
        }
        $department->hod_id = $staff->id;
        if($department->save()){
            alert()->success('HOD assigned to Department', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }


    public function addAdviser(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'programme_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }

        if(!$programme = Programme::find($request->programme_id)){
            alert()->error('Oops', 'Invalid Programme ')->persistent('Close');
            return redirect()->back();
        }

        $levelAdviser = LevelAdviser::where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->first();

        if ($levelAdviser) {
            $levelAdviser->update([
                $levelAdviser->staff_id => $staff->id
            ]);
        } else {
            // Level Adviser does not exist, create a new one
            LevelAdviser::create([
                'staff_id' => $staff->id,
                'programme_id' => $programme->id,
                'level_id' => $request->level_id
            ]);

            if(!$staffRole = StaffRole::where('staff_id', $staff->id)->where('role_id', 1)->first()) {
                StaffRole::create([
                    'role_id' => 1, //level adviser role id
                    'staff_id' => $request->staff_id,
                ]);
            }
    
            $staffDescription = "Congratulations, you have been assigned as Level Adviser ";
            Notification::create([
                'staff_id' =>  $request->staff_id,
                'description' => $staffDescription,
                'status' => 0
            ]);
        }

        alert()->success('Level advicer assigned to programme and level', '')->persistent('Close');
        return redirect()->back();
    }

    public function addExamOfficer(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'department_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }

        if(!$department = Department::find($request->department_id)){
            alert()->error('Oops', 'Invalid Department ')->persistent('Close');
            return redirect()->back();
        }
        $department->exam_officer_id = $staff->id;
        if($department->save()){
            if(!$staffRole = StaffRole::where('staff_id', $staff->id)->where('role_id', 1)->first()) {
                StaffRole::create([
                    'role_id' => 11, 
                    'staff_id' => $request->staff_id,
                ]);
            }
    
            $staffDescription = "Congratulations, you have been assigned as Level Adviser ";
            Notification::create([
                'staff_id' =>  $request->staff_id,
                'description' => $staffDescription,
                'status' => 0
            ]);

            alert()->success('Exam officer assigned to department', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }

    public function getStudents(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
            'level_id' => 'required',
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

        $students = Student::
            with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
            ->where([
                'is_active' => true,
                'is_passed_out' => false,
                'is_rusticated' => false,
                'level_id' => $request->level_id,
                'programme_id' => $request->programme_id,
                'department_id' => $request->department_id,
            ])
            ->get();

        $department = Department::with('programmes', 'programmes.students', 'programmes.academicAdvisers', 'programmes.academicAdvisers.staff', 'programmes.academicAdvisers.level')->where('slug', $department->slug)->first();
        $levels = AcademicLevel::all();

        return view('staff.department', [
            'department' => $department,
            'levels' => $levels,
            'students' => $students,
        ]);
    }


}

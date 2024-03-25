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
use App\Models\CourseManagement;
use App\Models\ProgrammeCategory as Category;

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

    public function staff(Request $request){

        $staff  = Staff::withTrashed()->with('faculty', 'acad_department')->get();
        $departments = Department::all();
        $faculties = Faculty::all();

        return view('admin.staff', [
            'staff' => $staff,
            'departments' => $departments,
            'faculties' => $faculties,
        ]);
    }

    public function addStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'staffId' => 'required|unique:staffs',
            'lastname' => 'required',
            'othernames' => 'required',
            'description' => 'required',
            'email' => 'required|unique:staffs',
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

        if(empty(strpos($request->email, env('SCHOOL_DOMAIN')))) {
            alert()->error('Error', 'Invalid email, your email must contain @'.env('SCHOOL_DOMAIN'))->persistent('Close');
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
        ]);

        if(Staff::create($newAddStaff)){
            alert()->success('Staff added', 'A staff have been added')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateStaff(Request $request)
    {
        if(!empty($request->staff_id) && !$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff Information')->persistent('Close');
            return redirect()->back();
        }

        $slug = $staff->slug;
        if(!empty($request->lastname) && $request->lastname != $staff->lastname){
            $staff->lastname = $request->lastname;
            $staff->othernames = $request->othernames;
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->lastname.'-', $request->othernames)));
            $staff->slug = $slug;
        }

        if(!empty($request->staffId) && $request->staffId != $staff->staffId){
            $staff->staffId = $request->staffId;
        }

        if(!empty($request->faculty_id) && $request->faculty_id != $staff->faculty_id){
            $staff->faculty_id = $request->faculty_id;
        }

        if(!empty($request->department_id) && $request->department_id != $staff->department_id){
            $staff->department_id = $request->department_id;
        }

        if(!empty($request->phone_number) && $request->phone_number != $staff->phone_number){
            $staff->phone_number = $request->phone_number;
        }

        if(!empty($request->title) && $request->title != $staff->title){
            $staff->title = $request->title;
        }

        if(!empty($request->email) && $request->email != $staff->email){
            $staff->email = $request->email;
        }

        if($request->has('password') && !empty($request->password)){
            if($request->password == $request->confirm_password){
                $password = bcrypt($request->password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }
            $staff->password = $password;
        }

        if(!empty($request->category) && $request->category != $staff->category){
            $staff->category = $request->category;
        }

        if(!empty($request->description) && $request->description != $staff->description){
            $staff->description = $request->description;
        }

        if(!empty($request->image)){
            $imageUrl = 'uploads/staff/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
            $image = $request->file('image')->move('uploads/staff', $imageUrl);

            $staff->image = env('APP_URL').'/public/'.$imageUrl;
        }


        if($staff->save()){
            alert()->success('Changes Saved', 'Changes saved successfully')->persistent('Close');
            return redirect()->back();
        }
    }

    public function roles(Request $request){

        $roles  = Role::orderBy('access_level', 'asc')->get();

        return view('admin.roles', [
            'roles' => $roles
        ]);
    }
    
    public function addRole(Request $request){
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|unique:roles',
            'access_level' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newRole = [
            'role' => $request->role,
            'access_level' => $request->access_level
        ];
        
        if(Role::create($newRole)){
            alert()->success('Role  added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateRole(Request $request){
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

        $role->role = $request->role;
        $role->access_level = $request->access_level;

        if($role->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
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

        $allDepartments = Department::all();
        $faculties = Faculty::all();

        return view('admin.singleStaff', [
            'singleStaff' => $staff,
            'roles' => $roles,
            'departments' => $departments,
            'allDepartments' => $allDepartments,
            'faculties' => $faculties,
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
                    'role_id' => 11, //Exam Officer role id
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
        $categories = Category::all();

        return view('admin.department', [
            'department' => $department,
            'levels' => $levels,
            'students' => $students,
            'categories' => $categories
        ]);
    }

    public function uploadStudentImage(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'image' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$student = Student::find($request->student_id)){
            alert()->error('Oops', 'Invalid Student ')->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-',$student->matric_number.$student->lastname.$student->othernames)));

        $imageUrl = 'uploads/student/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
        $image = $request->file('image')->move('uploads/student', $imageUrl);

        $student->image = $imageUrl;

        if($student->save()){
            alert()->success('Image Uploaded successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function changeStudentPassword (Request $request) {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'password' => 'required',
            'confirm_password' => 'required'
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$student = Student::find($request->student_id)){
            alert()->error('Oops', 'Invalid Student ')->persistent('Close');
            return redirect()->back();
        }

        if($request->password == $request->confirm_password){
            $student->password = bcrypt($request->password);
        }else{
            alert()->error('Oops!', 'Password mismatch')->persistent('Close');
            return redirect()->back();
        }

        if($student->save()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function changeStudentCreditLoad (Request $request) {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'credit_load' => 'required',
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$student = Student::find($request->student_id)){
            alert()->error('Oops', 'Invalid Student ')->persistent('Close');
            return redirect()->back();
        }

        $student->credit_load = $request->credit_load;

        if($student->save()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function changeStudentName(Request $request){
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

        $applicantId = $student->user_id;
        if(!$applicant = Applicant::find($applicantId)){
            alert()->error('Oops!', 'Student application data mismatch')->persistent('Close');
            return redirect()->back();
        }
        
        $applicant->lastname = $request->lastname;
        $applicant->othernames = $request->othernames;
        $applicant->save();

        $student->email = $request->email;
        
        if($student->save()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function changeStudentLevel(Request $request){
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

        $student->level_id = $request->level_id;
        
        if($student->save()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function courseAllocation(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $staffDepartmentId = $staff->department_id;

        $programmes = Programme::get();
        $levels = AcademicLevel::get();

        return view('admin.courseAllocation', [
            'programmes' => $programmes,
            'levels' => $levels
        ]);
    }

    public function getCourses(Request $request){
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

        
        $programmes = Programme::get();
        $levels = AcademicLevel::get();

        return view('admin.courseAllocation', [
            'programmes' => $programmes,
            'levels' => $levels,
            'courses' => $courses,
            'mainProgramme' => $programme,
            'mainLevel' => $level,
        ]);
    }

    public function assignCourse(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $exist = CourseManagement::where([
            'course_id' => $request->course_id,
            'staff_id' => $request->staff_id,
            'academic_session' => $academicSession
        ])->first();

        if($exist){
            alert()->error('Oops!', 'Course already assigned to staff')->persistent('Close');
            return redirect()->back();
        }

        $courseAssign = CourseManagement::create([
            'course_id' => $request->course_id,
            'staff_id' => $request->staff_id,
            'academic_session' => $academicSession
        ]);
        
        if($courseAssign) {
            alert()->success('Success', 'Staff assigned successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function unsetStaff(Request $request){
        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $unset = CourseManagement::where([
            'course_id' => $request->course_id,
            'academic_session' => $academicSession
        ])->delete();
        
        if($unset) {
            alert()->success('Success', 'Staff assigned successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    
}

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

        return view('admin.staff', [
            'staff' => $staff
        ]);
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

        return view('admin.singleStaff', [
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

        return view('admin.department', [
            'department' => $department,
            'levels' => $levels,
            'students' => $students,
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

        if($request->new_password == $request->confirm_password){
            $student->password = bcrypt($request->new_password);
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




    
}

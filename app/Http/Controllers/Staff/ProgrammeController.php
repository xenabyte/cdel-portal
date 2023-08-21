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

use App\Models\ProgrammeCategory;
use App\Models\Programme;
use App\Models\AcademicLevel;
use App\Models\LevelAdviser;
use App\Models\StudentCourseRegistration;
use App\Models\CourseRegistration;
use App\Models\Student;
use App\Models\Department;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class ProgrammeController extends Controller
{
    //

    public function programmeCategory(){

        $programmeCategories = ProgrammeCategory::get();
        
        return view('staff.programmeCategory', [
            'programmeCategories' => $programmeCategories
        ]);
    }

    public function addProgrammeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'category' => 'required|string|unique:programme_categories',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newLevel = [
            'category' => $request->category,
        ];
        
        if(ProgrammeCategory::create($newLevel)){
            alert()->success('Programme category added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateProgrammeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$programmeCategory = ProgrammeCategory::find($request->category_id)){
            alert()->error('Oops', 'Invalid Programme Category ')->persistent('Close');
            return redirect()->back();
        }

        $programmeCategory->category = $request->category;

        if($programmeCategory->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteProgrammeCategory(Request $request){
        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$programmeCategory = ProgrammeCategory::find($request->category_id)){
            alert()->error('Oops', 'Invalid Prograamme Category ')->persistent('Close');
            return redirect()->back();
        }
        
        if($programmeCategory->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function programmes(){
        $programmes = Programme::with('students')->get();

        return view('staff.programmes', [
            'programmes' => $programmes
        ]);
    }

    public function programme($slug){
        $programme = Programme::with('students')->where('slug', $slug)->first();
        $levels = AcademicLevel::all();

        return view('staff.programme', [
            'programme' => $programme,
            'levels' => $levels
        ]);
    }

    public function saveProgramme(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$programme = Programme::find($request->programme_id)){
            alert()->error('Oops', 'Invalid Programme ')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->code) &&  $request->code != $programme->code){
            $programme->code = strtoupper($request->code);
        }

        if(!empty($request->code_number) &&  $request->code_number != $programme->code_number){
            $programme->code_number = $request->code_number;
        }

        if(!empty($request->matric_last_number) &&  $request->matric_last_number != $programme->matric_last_number){
            $programme->matric_last_number = $request->matric_last_number;
        }

        if($programme->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function adviserProgrammes(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $adviserProgrammes = LevelAdviser::with('programme', 'level')->where('staff_id', $staffId)->get();

        return view('staff.adviserProgrammes', [
            'adviserProgrammes' => $adviserProgrammes
        ]);
    }


    Public function levelCourseReg(Request $request, $id){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        if(!$adviserProgramme = LevelAdviser::with('programme', 'level')->where('id', $id)->where('staff_id', $staffId)->first()){
            alert()->error('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }
        

        $levelId = $adviserProgramme->level_id;
        $programmeId = $adviserProgramme->programme_id;

        $studentIds = Student::where('level_id', $levelId)->where('programme_id', $programmeId)->pluck('id')->toArray();
    
        $studentRegistrations = StudentCourseRegistration::with('student', 'student.applicant')->whereIn('student_id', $studentIds)->where('level_id', $levelId)->where('academic_session', $academicSession)->get();

        return view('staff.levelCourseReg', [
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
        if($request->type == 'level_adviser' && !empty($studentCourseReg->level_adviser_status)){
            alert()->info('Oops', 'Student registration already approved')->persistent('Close');
            return redirect()->back();
        }
        
        if($request->type != 'level_adviser' && !empty($studentCourseReg->hod_status)){
            alert()->info('Oops', 'Student registration already approved')->persistent('Close');
            return redirect()->back();
        }
        
        if($request->type == 'level_adviser'){
            $studentCourseReg->level_adviser_status = true;
        }else{
            $studentCourseReg->hod_status = true;
        }

        if($studentCourseReg->save()){
            alert()->success('Registration Approved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function levelStudents(Request $request, $id){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        if(!$adviserProgramme = LevelAdviser::with('programme', 'level')->where('id', $id)->where('staff_id', $staffId)->first()){
            alert()->error('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        $levelId = $adviserProgramme->level_id;
        $programmeId = $adviserProgramme->programme_id;

        $students = Student::
        with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'partner', 'academicLevel', 'department', 'faculty'])
        ->where([
            'level_id' => $levelId,
            'programme_id' => $programmeId,
        ])
        ->get();

        return view('staff.levelStudents', [
            'students' => $students
        ]);
    }

    public function getDepartments($id){
        $departments = Department::where('faculty_id', $id)->get();

        return $departments;
    }

    public function getProgrammes($id){
        $programmes = Programme::where('department_id', $id)->get();

        return $programmes;
    }

}

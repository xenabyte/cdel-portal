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

use App\Models\ProgrammeCategory;
use App\Models\Programme;
use App\Models\AcademicLevel;
use App\Models\Department;
use App\Models\Course;
use App\Models\Notification;
use App\Models\GradeScale;
use App\Models\CourseRegistration;
use App\Models\Staff;

use App\Mail\NotificationMail;
use App\Libraries\Result\Result;

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
        
        return view('admin.programmeCategory', [
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

        return view('admin.programmes', [
            'programmes' => $programmes
        ]);
    }

    public function programme($slug){
        $programme = Programme::with('students')->where('slug', $slug)->first();
        $levels = AcademicLevel::all();

        return view('admin.programme', [
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

    public function getDepartments($id){
        $departments = Department::where('faculty_id', $id)->get();

        return $departments;
    }

    public function getProgrammes($id){
        $programmes = Programme::where('department_id', $id)->get();

        return $programmes;
    }

    public function studentCourses(Request $request){

        $programmes = Programme::get();
        $academicLevels = AcademicLevel::get();

        return view('admin.studentCourses',[
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

        return view('admin.studentCourses',[
            'programmes' => $programmes,
            'academicLevels' => $academicLevels,
            'courses' => $courses,
            'academiclevel' => $academicLevel,
            'programme' => $programme,
        ]);
    }

    public function courseDetail(Request $request, $id){
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $course = Course::with('level', 'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('id', $id)->first();
        $registeredStudents = $course->registrations->where('academic_session', $academicSession)->pluck('student');
        $course->registeredStudents = $registeredStudents;

        return view('admin.courseDetail', [
            'course' => $course,
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
        $staff = Staff::find($request->staff_id);
        $staffName = $staff->title.' '.$staff->lastname.' '.$staff->othernames;
        $staffId = $staff->id;

        $courseId = $request->course_id;

        $message = $request->message;
        $course = Course::with('level', 'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('id', $courseId)->first();
        $registeredStudents = $course->registrations->where('academic_session', $academicSession)->pluck('student');

        foreach ($registeredStudents as $student){
            $description = $staffName ."(through ICT) sent you a message; ".$message;
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

}

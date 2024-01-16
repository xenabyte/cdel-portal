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
use App\Models\Student;
use App\Models\StudentDemotion;
use App\Models\StudentCourseRegistration;
use App\Models\CourseRegistration;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\GradeScale;


use App\Libraries\Result\Result;
use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class ResultController extends Controller
{
    //
    public function getStudentResults(){
        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();

        return view('admin.getStudentResults',[
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'faculties' => $faculties
        ]);
    }

    public function generateStudentResults(Request $request){
        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();

        $programme = Programme::find($request->programme_id);
        $academicLevel = AcademicLevel::find($request->level_id);

        $students = Student::
        with(['applicant', 'programme', 'transactions', 'courseRegistrationDocument', 'registeredCourses', 'registeredCourses.course', 'partner', 'academicLevel', 'department', 'faculty'])
        ->where([
            'is_active' => true,
            'is_passed_out' => false,
            'is_rusticated' => false,
            'programme_id' => $request->programme_id,
            'department_id' => $request->department_id,
            'faculty_id' => $request->faculty_id,
        ])
        ->whereHas('registeredCourses', function ($query) use ($request) {
            $query->where('level_id', $request->level_id)
                  ->where('academic_session', $request->session);
        })
        ->get();    

        return view('admin.getStudentResults',[
            'students' => $students,
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'faculties' => $faculties,
            'semester' => $request->semester,
            'academicSession' => $request->session,
            'academiclevel' => $academicLevel,
            'programme' => $programme,
            'faculty_id' => $request->faculty_id,
            'department_id' => $request->department_id,
        ]);
    }

    public function approveResult(Request $request){

        $studentIds = $request->input('student_ids', []);
        $students = Student::whereIn('id', $studentIds)->get();

        foreach ($students as $student) {
            
            $studentRegistration = CourseRegistration::where([
                'student_id' => $student->id,
                'level_id' => $request->level_id,
                'academic_session' => $request->session,
                'semester' => $request->semester,
            ])->where('grade', '!=', null)->update(['result_approval_id' => ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED)]);

            Result::calculateCGPA($student->id);
        }


        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();

        alert()->success('Result Approved', '')->persistent('Close');
        return view('admin.getStudentResults',[
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'faculties' => $faculties
        ]);
    }

    public function generateResult(Request $request){
        $validator = Validator::make($request->all(), [
            'semester' => 'required',
            'session' => 'required',
            'level_id' => 'required',
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentId = $request->student_id;
        $student = Student::find($studentId);
        $globalData = $request->input('global_data');

        $semester = $request->semester;
        $academicSession = $request->session;
        $levelId = $request->level_id;
        $academicLevel = AcademicLevel::find($levelId);
        $level = $academicLevel->level;

        $courseRegs = CourseRegistration::with('course')
        ->where('student_id', $studentId)
        ->where('academic_session', $academicSession)
        ->where('level_id', $levelId)
        ->where('result_approval_id',  ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED))
        ->whereHas('course', function ($query) use ($semester) {
            $query->where('semester', $semester);
        })
        ->get();

        if(!$courseRegs->count() > 0) {
            alert()->info('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }


        $pdf = new Pdf();
        $examResult = $pdf->generateExamResult($studentId, $academicSession, $semester, $level);

        return redirect(asset($examResult));
    }

    public function studentResult(){
        return view('admin.studentResult');
    }

    public function getStudent(Request $request){
        $validator = Validator::make($request->all(), [
            'reg_number' => 'required',
            'type' => 'required',
            'url' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentIdCode = $request->reg_number;
        if($request->type == 'Student'){
            return $this->getSingleStudent($studentIdCode, $request->url);
        }
    }

    public function getStudentResult(Request $request){
        $validator = Validator::make($request->all(), [
            'reg_number' => 'required',
            'url' => 'required',
            'level_id' => 'required',
            'session' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $data = new \stdClass();
        $data->levelId = $request->level_id;
        $data->academicSession = $request->session;

        $studentIdCode = $request->reg_number;
        return $this->getSingleStudent($studentIdCode, $request->url, $data);
    }


   public function updateStudentResult(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'url' => 'required',
            'level_id' => 'required',
            'session' => 'required',
            'course_reg_id' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $studentId = $request->student_id;
        $student = Student::find($studentId);
        $data = new \stdClass();
        $data->levelId = $request->level_id;
        $data->academicSession = $request->session; 
        $studentIdCode = $student->matric_number;

        if(!$registeredCourse = CourseRegistration::with('course')->where('id', $request->course_reg_id)->first()){
            alert()->info('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->ca_score) && ($request->ca_score != $registeredCourse->ca_score)){
            $registeredCourse->ca_score = $request->ca_score;
        }

        if(!empty($request->exam_score) && ($request->exam_score != $registeredCourse->exam_score)){
            $registeredCourse->exam_score = $request->exam_score;
        }

        if(!empty($request->total) && ($request->total != $registeredCourse->total)){
            $registeredCourse->total = $request->total;

            $grading = GradeScale::computeGrade($request->total);
            $grade = $grading->grade;
            $points = $grading->point;

            $registeredCourse->grade = $grade;
            $registeredCourse->points = $points*$registeredCourse->course_credit_unit;
        }

        if($registeredCourse->save()){
            alert()->success('Result details updated successfully', '')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, $request->url, $data);
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return $this->getSingleStudent($studentIdCode, $request->url, $data);
   }


}

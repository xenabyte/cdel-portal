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
use App\Models\Course;
use App\Models\CoursePerProgrammePerAcademicSession;



use App\Libraries\Result\Result;
use App\Libraries\Pdf\Pdf;
use App\Exports\StudentResultBroadSheet;


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

    public function getStudentMissingResults(Request $request, $semester){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $courseRegistrations = CourseRegistration::with('student')->where('academic_session', $academicSession)->where('semester', $semester)->where('course_credit_unit', '>', 0)->where('grade', null)->get();
        $studentIds = $courseRegistrations->pluck('student_id')->unique()->values()->all();

        $students = Student::with(['applicant', 'programme', 'registeredCourses.course'])
        ->whereIn('id', $studentIds)
        ->get();

        $studentsWithMissingGrades = $students->map(function ($student) use ($courseRegistrations) {
            $student->courses_with_missing_grades = $courseRegistrations->where('student_id', $student->id)->map(function ($registration) {
                return $registration->course;
            });
            return $student;
        });

        return view('admin.getStudentMissingResults',[
            'students' => $students,
        ]);
    }

    public function getStudentResultSummary(Request $request){
        $globalData = $request->input('global_data');
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get(); 

        
        return view('admin.getStudentResultSummary',[
            'faculties' => $faculties,
            'academicSessions' => $academicSessions,
        ]);
    }

    public function generateStudentResults(Request $request){
        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();
        $semester = $request->semester;
        $academicSession = $request->session;
        $batch = $request->batch;
    
        $programme = Programme::find($request->programme_id);
        $academicLevel = AcademicLevel::find($request->level_id);
    
        $studentsQuery = Student::
        with(['applicant', 'programme', 'registeredCourses', 'registeredCourses.course'])
        ->where([
            'is_active' => true,
            'is_rusticated' => false,
            'programme_id' => $request->programme_id,
            'department_id' => $request->department_id,
            'faculty_id' => $request->faculty_id,
        ])
        ->whereHas('registeredCourses', function ($query) use ($request, $batch) {
            $query->where('level_id', $request->level_id)
                  ->where('academic_session', $request->session);
            if (!empty($batch)) {
                $query->where('batch', $batch);
            }
        });
    
        $students = $studentsQuery->get();
        if(empty($students)){
            alert()->success('No students found', '')->persistent('Close');
            return view('admin.getStudentResults',[
                'academicLevels' => $academicLevels,
                'academicSessions' => $academicSessions,
                'faculties' => $faculties
            ]);
        }
    
        $classifiedCourses = $this->classifyCourses($students, $semester, $academicLevel, $academicSession);
    
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
            'classifiedCourses' => $classifiedCourses,
        ]);
    }

    public function generateStudentResultSummary(Request $request){
        $academicLevels = AcademicLevel::get();
        $globalData = $request->input('global_data');
        $batch = $request->batch;

        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get(); 
    
        $studentsQuery = Student::
        with(['applicant', 'programme', 'registeredCourses', 'registeredCourses.course', 'academicLevel', 'department', 'faculty'])
        ->where([
            'is_active' => true,
            'is_rusticated' => false,
            'faculty_id' => $request->faculty_id,
        ])
        ->whereHas('registeredCourses', function ($query) use ($request, $batch) {
            $query->where('academic_session', $request->session);
            if (!empty($batch)) {
                $query->where('batch', $batch);
            }
        });
    
        $students = $studentsQuery->get();
    
        $faculty = Faculty::find($request->faculty_id);
    
        $classifiedStudents = [];
    
        foreach ($students as $student) {
            $level = $student->academicLevel->level;
            $program = $student->programme->name;
    
            if (!isset($classifiedStudents[$level])) {
                $classifiedStudents[$level] = [];
            }
    
            if (!isset($classifiedStudents[$level][$program])) {
                $classifiedStudents[$level][$program] = [];
            }
    
            $classifiedStudents[$level][$program][] = $student;
        }

        if(count($students) < 1){
            alert()->success('No students found', '')->persistent('Close');
            return view('admin.getStudentResultSummary',[
                'faculties' => $faculties,
                'academicSessions' => $academicSessions,
            ]);
        }
    
        return view('admin.getStudentResultSummary',[
            'classifiedStudents' => $classifiedStudents,
            'academicLevels' => $academicLevels,
            'academicSession' => $request->session,
            'semester' => $request->semester,
            'students' => $students,
            'faculty' => $faculty
        ]);
    }
    

    // public function generateResultBroadSheet(Request $request){
    //     $academicLevels = AcademicLevel::get();
    //     $academicSessions = Session::orderBy('id', 'desc')->get();
    //     $faculties = Faculty::get();
    //     $semester = $request->semester;
    //     $academicSession = $request->session;
    //     $academicLevel = AcademicLevel::find($request->level_id);
    //     $programme = Programme::find($request->programme_id);

    //     $students = Student::with(['applicant', 'programme', 'registeredCourses', 'registeredCourses.course', 'academicLevel', 'department', 'faculty'])
    //         ->where([
    //             'is_active' => true,
    //             'is_passed_out' => false,
    //             'is_rusticated' => false,
    //             'programme_id' => $request->programme_id,
    //             'department_id' => $request->department_id,
    //             'faculty_id' => $request->faculty_id,
    //         ])
    //         ->whereHas('registeredCourses', function ($query) use ($request) {
    //             $query->where('level_id', $request->level_id)
    //                 ->where('academic_session', $request->session);
    //         })
    //         ->get();

    //     $classifiedCourses = $this->classifyCourses($students, $semester, $academicLevel, $academicSession);
        
    //     // Generate a unique filename for the download
    //     $fileName = $programme->name . ' ' . $academicLevel->level . ' ' . $semester . ' ' . $academicSession . ' resultBroadSheet.xlsx';
    //     $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $fileName)));

    //     // Return the export instance, ready for download

    //     new StudentResultBroadSheet($students, $semester, $academicLevel, $academicSession, $classifiedCourses);

    //     return view('admin.getStudentResults',[
    //         'students' => $students,
    //         'academicLevels' => $academicLevels,
    //         'academicSessions' => $academicSessions,
    //         'faculties' => $faculties,
    //         'semester' => $request->semester,
    //         'academicSession' => $request->session,
    //         'academiclevel' => $academicLevel,
    //         'programme' => $programme,
    //         'faculty_id' => $request->faculty_id,
    //         'department_id' => $request->department_id,
    //         'classifiedCourses' => $classifiedCourses,
    //     ]);
    // }

    public function generateResultBroadSheet(Request $request){
        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();
        $semester = $request->semester;
        $academicSession = $request->session;
        $academicLevel = AcademicLevel::find($request->level_id);
        $programme = Programme::find($request->programme_id);
    
        $students = Student::with(['applicant', 'programme', 'registeredCourses', 'registeredCourses.course', 'academicLevel', 'department', 'faculty'])
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
    
        $classifiedCourses = $this->classifyCourses($students, $semester, $academicLevel, $academicSession);
        
        // Generate a unique filename for the download
        $fileName = $programme->name . ' ' . $academicLevel->level . ' ' . $semester . ' ' . $academicSession . ' resultBroadSheet';
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $fileName))) . '.xlsx'; // Add the extension
        
        // Create the export instance
        $export = new StudentResultBroadSheet($students, $semester, $academicLevel, $academicSession, $classifiedCourses);
              
    
        return \Excel::download($export, $slug);
    }
    
    public function approveResult(Request $request){
        $studentIds = $request->input('student_ids', []);
        $url = $request->url;
        $students = Student::whereIn('id', $studentIds)->get();
    
        foreach ($students as $student) {
            $studentRegistration = CourseRegistration::where('student_id', $student->id)
                ->where('academic_session', $request->session)
                ->where('semester', $request->semester)
                ->whereNotNull('grade')
                ->update(['result_approval_id' => ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED)]);
            
        }
    
        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'desc')->get();
        $faculties = Faculty::get();
    
        alert()->success('Result Approved', '')->persistent('Close');
        return view($url, [
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

        $total = $request->ca_score + $request->exam_score;

        $registeredCourse->total = $total;

        $grading = GradeScale::computeGrade($total);
        $grade = $grading->grade;
        $points = $grading->point;

        $courseCode = $registeredCourse->course_code;

        if (strpos($courseCode, 'NSC') !== false && $student->programme_id == 15) {
            if($total < 50){
                $grade = 'F';
                $points = 0;
            }
        }

        $registeredCourse->grade = $grade;
        $registeredCourse->points = $points*$registeredCourse->course_credit_unit;
        $registeredCourse->result_approval_id = ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED);
        $registeredCourse->status = "Completed";

        if($registeredCourse->save()){
            alert()->success('Result details updated successfully', '')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, $request->url, $data);
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return $this->getSingleStudent($studentIdCode, $request->url, $data);
    }

    public function deleteStudentResult(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'url' => 'required',
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

        if($registeredCourse->delete()){
            alert()->success('Result details deleted successfully', '')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, $request->url, $data);
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return $this->getSingleStudent($studentIdCode, $request->url, $data);
    }


   public function addStudentCourse(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'url' => 'required',
            'level_id' => 'required',
            'session' => 'required',
            'course_code' => 'required',
            'semester' => 'required'
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

        $resultApprovalId = ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED);

        $courseCode = $request->course_code;
        $semester = $request->semester;
        $levelId = $request->level_id;
        $programmeId = $student->programme_id;
        $academicSession = $request->session;
        $caScore = $request->ca_score;
        $examScore = $request->exam_score;
        $totalScore = $request->ca_score + $request->exam_score;


        if(!$course = Course::where('code', $courseCode)->first()){
            alert()->error('Oops!', 'Course not found')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, $request->url, $data);
        }

        $courseId = $course->id;

        $programmeCourse = CoursePerProgrammePerAcademicSession::where([
            'course_id' => $courseId,
            'level_id' => $levelId,
            'programme_id' => $programmeId,
            'semester' => $semester,
            'academic_session' => $academicSession,
        ])->first();

        if (!$programmeCourse && $levelId !== null) {
            $programmeCourse = CoursePerProgrammePerAcademicSession::where([
                'course_id' => $courseId,
                'programme_id' => $programmeId,
                'semester' => $semester,
                'academic_session' => $academicSession,
            ])->first();
        }

        if (!$programmeCourse) {
            alert()->error('Oops!', 'Course not registered for student in programme and session')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, $request->url, $data);
        }


        $existingRegistration = CourseRegistration::where([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'academic_session' => $academicSession,
            'level_id' => $levelId,
        ])->first();

        if(!$existingRegistration){
            $courseReg = CourseRegistration::create([
                'student_id' => $student->id,
                'course_id' => $programmeCourse->course_id,
                'course_credit_unit' => $programmeCourse->credit_unit,
                'course_code' => $courseCode,
                'course_status' => $programmeCourse->status,
                'semester' => $programmeCourse->semester,
                'academic_session' => $academicSession,
                'level_id' => $levelId,
                'programme_course_id' => $programmeCourse->id
            ]);
        }

        $studentCourseReg = CourseRegistration::where([
            'student_id' => $student->id,
            'course_code' => $courseCode,
            'academic_session' => $academicSession,
            'level_id' => $levelId,
        ])->first();

        $checkCarryOver = CourseRegistration::where([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'grade' => 'F',
        ])->first();

        if(!empty($checkCarryOver)){
            $checkCarryOver->re_reg = true;
            $checkCarryOver->save();
        }

        $studentCourseReg->status = 'approved';
        if(!empty($totalScore)){
            $grading = GradeScale::computeGrade($totalScore);
            $grade = $grading->grade;
            $points = $grading->point;

            $courseCode = $studentCourseReg->course_code;

            if (strpos($courseCode, 'NSC') !== false && $student->programme_id == 15) {
                if($totalScore < 50){
                    $grade = 'F';
                    $points = 0;
                }
            }

            $studentCourseReg->ca_score = $caScore;
            $studentCourseReg->exam_score = $examScore;
            $studentCourseReg->total = $totalScore;
            $studentCourseReg->grade = $grade;
            $studentCourseReg->points = $points*$studentCourseReg->course_credit_unit;
            $studentCourseReg->result_approval_id = $resultApprovalId;
            $studentCourseReg->status = 'Completed';
        }

        if($studentCourseReg->save()){
            alert()->success('Result added successfully', '')->persistent('Close');
            return $this->getSingleStudent($studentIdCode, $request->url, $data);
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return $this->getSingleStudent($studentIdCode, $request->url, $data);
   }

}

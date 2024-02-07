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
use App\Models\CoursePerProgrammePerAcademicSession;
use App\Models\Course;
use App\Models\CourseRegistrationSetting;
use App\Models\Notification;



use App\Libraries\Pdf\Pdf;
use App\Mail\NotificationMail;

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
        $staffDepartmentId = $staff->department_id;

        // $staffHod = false;
        // if($staff->id == $staff->acad_department->hod_id){
        //     $staffHod = true;
        // }

        $adviserProgrammes = LevelAdviser::with('programme', 'level')
        ->where(function ($query) use ($staff) {
            $query->whereHas('programme', function ($query) use ($staff) {
                $query->where('department_id', $staff->department_id);
            })->orWhere('staff_id', $staff->id);
        })
        ->get();

        // if($staffHod){
        //     $departmentId = $staff->department_id;
        //     $programmesIDs= Programme::where('department_id', $departmentId)->pluck('id')->toArray();

        //     $adviserProgrammes = LevelAdviser::with('programme', 'level')
        //     ->whereIn('programme_id', $programmesIDs)
        //     ->get();
        // }

        return view('staff.adviserProgrammes', [
            'adviserProgrammes' => $adviserProgrammes
        ]);
    }

    public function addCourseForStudent(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $courses = CoursePerProgrammePerAcademicSession::with('course')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
        $defaultData = [
            'courses' => $courses,
            'academiclevel' => AcademicLevel::find($request->level_id),
            'programme' => Programme::find($request->programme_id),
            'semester' => $request->semester,
            'allCourses' => Course::all(),
            'academicLevels' => AcademicLevel::get()
        ];

        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'level_id' => 'required',
            'programme_id' => 'required',
            'semester' => 'required',
            'credit_unit' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return view('staff.studentCourses',$defaultData);
        }


        $courseRegistrationSetting = Programme::where('id', $request->programme_id)->value('course_registration');
        if(!empty($courseRegistrationSetting) && $courseRegistrationSetting != 'stop'){
            alert()->error('Oops', 'Course Registration already started')->persistent('Close');
            return view('staff.studentCourses', $defaultData);
        }
        
        if(!$course = Course::find($request->course_id)){
            alert()->error('Oops', 'Invalid course ')->persistent('Close');
            return view('staff.studentCourses',$defaultData);
        }

        $exist = CoursePerProgrammePerAcademicSession::where([
            'course_id' => $course->id,
            'level_id' => $request->level_id,
            'programme_id' => $request->programme_id,
            'semester' => $request->semester,
            'credit_unit' => $request->credit_unit,
            'academic_session' => $academicSession,
        ])->first();

        if($exist){
            alert()->error('Oops!', 'Course already added')->persistent('Close');
            return view('staff.studentCourses', $defaultData);
        }
        
        $newCourses = [
            'course_id' => $course->id,
            'level_id' => $request->level_id,
            'programme_id' => $request->programme_id,
            'semester' => $request->semester,
            'credit_unit' => $request->credit_unit,
            'academic_session' => $academicSession,
            'status' => $request->status,
        ];
        
        if(CoursePerProgrammePerAcademicSession::create($newCourses)){
            alert()->success('Course added successfully', '')->persistent('Close');
            $courses = CoursePerProgrammePerAcademicSession::with('course', 'course.courseManagement', 'course.courseManagement.staff')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
            $defaultData = [
                'courses' => $courses,
                'academiclevel' => AcademicLevel::find($request->level_id),
                'programme' => Programme::find($request->programme_id),
                'semester' => $request->semester,
                'allCourses' => Course::all(),
                'academicLevels' => AcademicLevel::get()
            ];
            return view('staff.studentCourses',$defaultData);
        }

        alert()->error('Oops', 'Invalid course ')->persistent('Close');
        return view('staff.studentCourses', $defaultData);
    }

    public function deleteCourseForStudent(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $courses = CoursePerProgrammePerAcademicSession::with('course')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
        $defaultData = [
            'courses' => $courses,
            'academiclevel' => AcademicLevel::find($request->level_id),
            'programme' => Programme::find($request->programme_id),
            'semester' => $request->semester,
            'allCourses' => Course::all(),
            'academicLevels' => AcademicLevel::get(),
        ];

        $validator = Validator::make($request->all(), [
            'student_course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return view('staff.studentCourses',$defaultData);
        }

        $courseRegistrationSetting = Programme::where('id', $request->programme_id)->value('course_registration');
        if(!empty($courseRegistrationSetting) && $courseRegistrationSetting != 'stop'){
            alert()->error('Oops', 'Course Registration already started')->persistent('Close');
            return view('staff.studentCourses', $defaultData);
        }

        if(!$studentCourse = CoursePerProgrammePerAcademicSession::find($request->student_course_id)){
            alert()->error('Oops', 'Invalid Record ')->persistent('Close');
            return view('staff.studentCourses',$defaultData);
        }
        
        if($studentCourse->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            $courses = CoursePerProgrammePerAcademicSession::with('course', 'course.courseManagement', 'course.courseManagement.staff')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
            $defaultData = [
                'courses' => $courses,
                'academiclevel' => AcademicLevel::find($request->level_id),
                'programme' => Programme::find($request->programme_id),
                'semester' => $request->semester,
                'allCourses' => Course::all(),
                'academicLevels' => AcademicLevel::get(),
            ];
            return view('staff.studentCourses',$defaultData);
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return view('staff.studentCourses',$defaultData);

    }

    public function updateCourseForStudent(Request $request){
            $globalData = $request->input('global_data');
            $academicSession = $globalData->sessionSetting['academic_session'];

            $courses = CoursePerProgrammePerAcademicSession::with('course')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
            $defaultData = [
                'courses' => $courses,
                'academiclevel' => AcademicLevel::find($request->level_id),
                'programme' => Programme::find($request->programme_id),
                'semester' => $request->semester,
                'allCourses' => Course::all(),
                'academicLevels' => AcademicLevel::get(),
            ];

            $validator = Validator::make($request->all(), [
                'student_course_id' => 'required',
            ]);

            if($validator->fails()) {
                alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                return view('staff.studentCourses',$defaultData);
            }

            $courseRegistrationSetting = Programme::where('id', $request->programme_id)->value('course_registration');
            if(!empty($courseRegistrationSetting) && $courseRegistrationSetting != 'stop'){
                alert()->error('Oops', 'Course Registration already started')->persistent('Close');
                return view('staff.studentCourses', $defaultData);
            }

            if(!$studentCourse = CoursePerProgrammePerAcademicSession::find($request->student_course_id)){
                alert()->error('Oops', 'Invalid Record ')->persistent('Close');
                return view('staff.studentCourses',$defaultData);
            }

            if(!empty($request->status) && $request->status != $studentCourse->status) {
                $studentCourse->status = $request->status;
            }

            if($request->credit_unit != $studentCourse->credit_unit) {
                $studentCourse->credit_unit = $request->credit_unit;
            }

            if($studentCourse->update()){
                alert()->success('Record Updated Successfully', '')->persistent('Close');
                $courses = CoursePerProgrammePerAcademicSession::with('course', 'course.courseManagement', 'course.courseManagement.staff')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
                $defaultData = [
                    'courses' => $courses,
                    'academiclevel' => AcademicLevel::find($request->level_id),
                    'programme' => Programme::find($request->programme_id),
                    'semester' => $request->semester,
                    'allCourses' => Course::all(),
                    'academicLevels' => AcademicLevel::get(),
                ];
                return view('staff.studentCourses',$defaultData);
            }
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return view('staff.studentCourses',$defaultData);
    }

    Public function levelCourseReg(Request $request, $id){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        if(!$adviserProgramme = LevelAdviser::with('programme', 'level')->where('id', $id)->first()){
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
        $studentId = $request->student_id;
        $staffId = $request->staff_id;

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
            $type = 'Level Adviser';
        }else{
            $studentCourseReg->hod_status = true;
            $type = 'Hod';
        }

        $academicSession = $studentCourseReg->academic_session;
        $otherData = new \stdClass();
        $otherData->staffId = $staffId;
        $otherData->courseRegId = $request->reg_id;
        $otherData->type = $request->type;

        $pdf = new Pdf();
        $courseReg = $pdf->generateCourseRegistration($studentId, $academicSession, $otherData);
        if(!empty($courseReg)){
            $studentId = $studentCourseReg->student_id;
            $student = Student::find($studentId);

            $senderName = env('SCHOOL_NAME');
            $receiverName = $student->applicant->lastname .' ' . $student->applicant->othernames;
            $message = 'Your course registration has been successfully approved. Please proceed to print at your earliest convenience.';

            $mail = new NotificationMail($senderName, $message, $receiverName, $courseReg);
            Mail::to($student->email)->send($mail);
            Notification::create([
                'student_id' => $student->id,
                'description' => $message,
                'attachment' => $courseReg,
                'status' => 0
            ]);
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

        $adviserProgramme = LevelAdviser::with('programme', 'level')
        ->where('id', $id)
        ->first();

        if(!$adviserProgramme){
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
            'is_active' => true,
            'is_passed_out' => false,
            'is_rusticated' => false
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

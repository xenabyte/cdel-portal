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
use App\Models\Student;
use App\Models\CoursePerProgrammePerAcademicSession;
use App\Models\CourseRegistrationSetting;
use App\Models\CourseManagement;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Payment;
use App\Models\Session;
use App\Models\LevelAdviser;
use App\Models\StudentCourseRegistration;
use App\Models\CourseLecture;
use App\Models\LectureAttendance;


use App\Mail\NotificationMail;
use App\Libraries\Result\Result;
use App\Libraries\Attendance\Attendance;

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
            alert()->error('Oops', 'Invalid Programme Category ')->persistent('Close');
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
        $academicSessions = Session::orderBy('id', 'DESC')->get();
        $programmeCategories = ProgrammeCategory::get();

        return view('admin.studentCourses',[
            'programmes' => $programmes,
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'programmeCategories' => $programmeCategories
        ]);
    }

    public function getStudentCourses(Request $request){

        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
            'level_id' => 'required',
            'semester' => 'required',
            'academic_session' => 'required',
            'programme_category_id' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $academicSession = $request->academic_session;

        $courses = CoursePerProgrammePerAcademicSession::with('course')
            ->where('programme_id', $request->programme_id)
            ->where('level_id', $request->level_id)
            ->where('academic_session', $academicSession)
            ->where('semester', $request->semester)
            ->where('programme_category_id', $request->programme_category_id)
            ->get();

        $allCourses = Course::all();
        $programme = Programme::find($request->programme_id);
        $academicLevel = AcademicLevel::find($request->level_id);

        $programmes = Programme::get();
        $academicLevels = AcademicLevel::get();
        $academicSessions = Session::orderBy('id', 'DESC')->get();
        $programmeCategories = ProgrammeCategory::get();


        return view('admin.studentCourses',[
            'programmes' => $programmes,
            'academicLevels' => $academicLevels,
            'academicSessions' => $academicSessions,
            'courses' => $courses,
            'academiclevel' => $academicLevel,
            'programme' => $programme,
            'semester' => $request->semester,
            'allCourses' => $allCourses,
            'programmeCategories' => $programmeCategories,
            'academic_session' => $academicSession,
            'programme_category_id' => $request->programme_category_id,
            'programmeCategory' => ProgrammeCategory::find($request->programme_category_id)
        ]);
    }

    public function addCourseForStudent(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $courses = CoursePerProgrammePerAcademicSession::with('course')
            ->where('programme_id', $request->programme_id)
            ->where('level_id', $request->level_id)
            ->where('academic_session', $request->academic_session)
            ->where('semester', $request->semester)
            ->where('programme_category_id', $request->programme_category_id)
            ->get();

        $programmeCategories = ProgrammeCategory::get();

        $defaultData = [
            'courses' => $courses,
            'academiclevel' => AcademicLevel::find($request->level_id),
            'programme' => Programme::find($request->programme_id),
            'semester' => $request->semester,
            'allCourses' => Course::all(),
            'academicLevels' => AcademicLevel::get(),
            'programmeCategories' => $programmeCategories,
            'academic_session' => $request->academic_session,
            'programme_category_id' => $request->programme_category_id,
            'programmeCategory' => ProgrammeCategory::find($request->programme_category_id)
        ];

        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'level_id' => 'required',
            'programme_id' => 'required',
            'semester' => 'required',
            'credit_unit' => 'required',
            'programme_category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return view('admin.studentCourses',$defaultData);
        }

        $levelAdviser = LevelAdviser::where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->first();
        if(!$levelAdviser){
            alert()->error('Oops', 'Invalid Level Adviser or not set ')->persistent('Close');
            return view('admin.studentCourses',$defaultData);
        }

        $courseRegistration = $levelAdviser->course_registration;

        if(!empty($courseRegistration) && $courseRegistration != 'stop'){
            alert()->error('Oops', 'Course Registration already started')->persistent('Close');
            return view('admin.studentCourses', $defaultData);
        }
        
        if(!$course = Course::find($request->course_id)){
            alert()->error('Oops', 'Invalid course ')->persistent('Close');
            return view('admin.studentCourses',$defaultData);
        }

        $exist = CoursePerProgrammePerAcademicSession::where([
            'course_id' => $course->id,
            'level_id' => $request->level_id,
            'programme_id' => $request->programme_id,
            'semester' => $request->semester,
            'credit_unit' => $request->credit_unit,
            'academic_session' => $academicSession,
            'programme_category_id' => $request->programme_category_id,
        ])->first();

        if($exist){
            alert()->error('Oops!', 'Course already added')->persistent('Close');
            return view('admin.studentCourses', $defaultData);
        }
        
        $newCourses = [
            'course_id' => $course->id,
            'level_id' => $request->level_id,
            'programme_id' => $request->programme_id,
            'semester' => $request->semester,
            'credit_unit' => $request->credit_unit,
            'academic_session' => $academicSession,
            'programme_category_id' => $request->programme_category_id,
            'status' => $request->status,
        ];
        
        if($coursePerProgrammePerAcademicSession = CoursePerProgrammePerAcademicSession::create($newCourses)){
            if(strtolower($request->addToReg) == 'yes'){
                $allStudents = Student::where('level_id', $request->level_id)
                    ->where('programme_id', $request->programme_id)
                    ->where('programme_category_id', $request->programme_category_id)
                    ->where('is_active', 1)
                    ->get();

                foreach($allStudents as $student){
                    $newCourseRegistration = ([
                        'programme_course_id' => $coursePerProgrammePerAcademicSession->id,
                        'student_id' => $student->id,
                        'course_id' => $course->id,
                        'level_id' => $request->level_id,
                        'semester' => $request->semester,
                        'academic_session' => $academicSession,
                        'course_credit_unit' => $request->credit_unit,
                        'course_code' => $course->code,
                        'course_status' => $request->status,
                        'programme_category_id' => $request->programme_category_id,
                        'status' => 'approved',
                    ]);
                    CourseRegistration::create($newCourseRegistration);
                }
            }


            alert()->success('Course added successfully', '')->persistent('Close');
            $courses = CoursePerProgrammePerAcademicSession::with('course', 'course.courseManagement', 'course.courseManagement.staff')
                ->where('programme_id', $request->programme_id)
                ->where('level_id', $request->level_id)
                ->where('academic_session', $request->academic_session)
                ->where('semester', $request->semester)
                ->where('programme_category_id', $request->programme_category_id)
                ->get();

            $defaultData = [
                'courses' => $courses,
                'academiclevel' => AcademicLevel::find($request->level_id),
                'programme' => Programme::find($request->programme_id),
                'semester' => $request->semester,
                'allCourses' => Course::all(),
                'academicLevels' => AcademicLevel::get(),
                'programmeCategories' => $programmeCategories,
                'academic_session' => $request->academic_session,
                'programme_category_id' => $request->programme_category_id,
                'programmeCategory' => ProgrammeCategory::find($request->programme_category_id)
            ];

            return view('admin.studentCourses',$defaultData);
        }

        alert()->error('Oops', 'Invalid course ')->persistent('Close');
        return view('admin.studentCourses', $defaultData);
    }

    public function deleteCourseForStudent(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $programmeCategories = ProgrammeCategory::get();

        $courses = CoursePerProgrammePerAcademicSession::with('course')
            ->where('programme_id', $request->programme_id)
            ->where('level_id', $request->level_id)
            ->where('academic_session', $request->academic_session)
            ->where('semester', $request->semester)
            ->get();

        $defaultData = [
            'courses' => $courses,
            'academiclevel' => AcademicLevel::find($request->level_id),
            'programme' => Programme::find($request->programme_id),
            'semester' => $request->semester,
            'allCourses' => Course::all(),
            'academicLevels' => AcademicLevel::get(),
            'programmeCategories' => $programmeCategories,
            'academic_session' => $request->academic_session,
            'programme_category_id' => $request->programme_category_id,
            'programmeCategory' => ProgrammeCategory::find($request->programme_category_id)
        ];

        $validator = Validator::make($request->all(), [
            'student_course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return view('admin.studentCourses',$defaultData);
        }
        
        $levelAdviser = LevelAdviser::where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->first();
        if(!$levelAdviser){
            alert()->error('Oops', 'Invalid Level Adviser or not set')->persistent('Close');
            return view('staff.studentCourses', $defaultData);
        }

        $courseRegistration = $levelAdviser->course_registration;

        if(!empty($courseRegistrationSetting) && $courseRegistrationSetting != 'stop'){
            alert()->error('Oops', 'Course Registration already started')->persistent('Close');
            return view('admin.studentCourses', $defaultData);
        }

        if(!$studentCourse = CoursePerProgrammePerAcademicSession::find($request->student_course_id)){
            alert()->error('Oops', 'Invalid Record ')->persistent('Close');
            return view('admin.studentCourses',$defaultData);
        }
        
        if($studentCourse->delete()){
            //delete all course registration with programme_course_id
            $deleteStudentCourseReg = CourseRegistration::where('programme_course_id',$request->student_course_id)->where('academic_session', $academicSession)->delete();

            alert()->success('Delete Successfully', '')->persistent('Close');
            $courses = CoursePerProgrammePerAcademicSession::with('course', 'course.courseManagement', 'course.courseManagement.staff')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
            $defaultData = [
                'courses' => $courses,
                'academiclevel' => AcademicLevel::find($request->level_id),
                'programme' => Programme::find($request->programme_id),
                'semester' => $request->semester,
                'allCourses' => Course::all(),
                'academicLevels' => AcademicLevel::get(),
                'programmeCategories' => $programmeCategories,
                'academic_session' => $request->academic_session,
                'programme_category_id' => $request->programme_category_id,
                'programmeCategory' => ProgrammeCategory::find($request->programme_category_id)
            ];
            return view('admin.studentCourses',$defaultData);
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return view('admin.studentCourses',$defaultData);

    }

    public function updateCourseForStudent(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];
        $programmeCategories = ProgrammeCategory::get();


        $courses = CoursePerProgrammePerAcademicSession::with('course')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
        $defaultData = [
            'courses' => $courses,
            'academiclevel' => AcademicLevel::find($request->level_id),
            'programme' => Programme::find($request->programme_id),
            'semester' => $request->semester,
            'allCourses' => Course::all(),
            'academicLevels' => AcademicLevel::get(),
            'programmeCategories' => $programmeCategories,
            'academic_session' => $request->academic_session,
            'programme_category_id' => $request->programme_category_id,
            'programmeCategory' => ProgrammeCategory::find($request->programme_category_id)
        ];

        $validator = Validator::make($request->all(), [
            'student_course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return view('admin.studentCourses',$defaultData);
        }

        $levelAdviser = LevelAdviser::where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->first();
        if(!$levelAdviser){
            alert()->error('Oops', 'Invalid Level Adviser or not set')->persistent('Close');
            return view('staff.studentCourses', $defaultData);
        }

        $courseRegistration = $levelAdviser->course_registration;
        if(!empty($courseRegistrationSetting) && $courseRegistrationSetting != 'stop'){
            alert()->error('Oops', 'Course Registration already started')->persistent('Close');
            return view('admin.studentCourses', $defaultData);
        }

        if(!$studentCourse = CoursePerProgrammePerAcademicSession::find($request->student_course_id)){
            alert()->error('Oops', 'Invalid Record ')->persistent('Close');
            return view('admin.studentCourses',$defaultData);
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
                'programmeCategories' => $programmeCategories,
                'academic_session' => $request->academic_session,
                'programme_category_id' => $request->programme_category_id,
                'programmeCategory' => ProgrammeCategory::find($request->programme_category_id)
            ];
            return view('admin.studentCourses',$defaultData);
        }
        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return view('admin.studentCourses',$defaultData);
    }

    public function courseDetail(Request $request, $id, $programmeCategory, $academicSession = null){

        if(!empty($academicSession)){
            $academicSession = str_replace('-', '/', $academicSession);
        }

        if(empty($academicSession)){
            $globalData = $request->input('global_data');
            $academicSession = $globalData->sessionSetting['academic_session'];
        }

        $programmeCategory = ProgrammeCategory::where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $lecturerDetails = CourseManagement::where('course_id', $id)->where('academic_session', $academicSession)->first(); 
        $registrations = CourseRegistration::where('course_id', $id)->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->get();
        $courseLectures = CourseLecture::with('lectureAttendance')->where('course_id', $id)->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->get();
        $course = Course::find($id);

        return view('admin.courseDetail', [
            'registrations' => $registrations,
            'lecturerDetails' => $lecturerDetails,
            'courseLectures' => $courseLectures,
            'course' => $course,
            'academicSession' => $academicSession,
            'programmeCategory' => $programmeCategory,
            'sessions' => Session::orderBy('id', 'DESC')->get()
        ]);
    }

    public function createLecture(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $validator = Validator::make($request->all(), [
            'topic' => 'required',
            'duration' => 'required',
            'date' => 'required',
            'programme_category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$course = Course::find($request->course_id)){
            alert()->error('Oops', 'Course not found')->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $course->code.$request->topic)));

        $createLectureData = ([
            'course_id' => $request->course_id,
            'topic' => $request->topic,
            'duration' => $request->duration,
            'date' => $request->date,
            'slug' => $slug,
            'academic_session' => $academicSession,
            'programme_category_id' => $request->programme_category_id
        ]);

        if(CourseLecture::create($createLectureData)){
            alert()->success('Lecture created successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error while creating lecture', '')->persistent('Close');
        return redirect()->back();
       
    }

    public function updateLecture(Request $request){

        $validator = Validator::make($request->all(), [
            'lecture_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$lecture = CourseLecture::find($request->lecture_id)){
            alert()->error('Oops', 'Course lecture not found')->persistent('Close');
            return redirect()->back();
        }

        if(!$course = Course::find($lecture->course_id)){
            alert()->error('Oops', 'Course not found')->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $course->code.$request->topic)));

        $updateLectureData = ([
            'topic' => $request->topic,
            'duration' => $request->duration,
            'date' => $request->date,
            'slug' => $slug,
        ]);

        if(CourseLecture::update($updateLectureData)){
            alert()->success('Lecture updated successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error while updating lecture', '')->persistent('Close');
        return redirect()->back();
       
    }

    public function deleteLecture(Request $request){
        $validator = Validator::make($request->all(), [
            'lecture_id' => 'required|exists:course_lectures,id',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $lecture = CourseLecture::find($request->lecture_id);

        if (!$lecture) {
            alert()->error('Oops', 'Course lecture not found')->persistent('Close');
            return redirect()->back();
        }

        $deletedAttendance = LectureAttendance::where('course_lecture_id', $lecture->id)->delete();

        if ($lecture->delete()) {
            alert()->success('Lecture deleted successfully!')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error while deleting lecture')->persistent('Close');
        return redirect()->back();
    }


    public function staffUploadAttendance(Request $request){
        $globalData = $request->input('global_data');

        $validator = Validator::make($request->all(), [
            'attendance' => 'required|file',
            'course_id' => 'required',
            'lecture_id' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $file = $request->file('attendance');
        $fileExtension = $file->getClientOriginalExtension();
        
        if ($fileExtension != 'csv') {
            alert()->error('Invalid file format, only CSV is allowed', '')->persistent('Close');
            return redirect()->back();
        }

        $lectureId = $request->lecture_id;
    
        $file = $request->file('attendance');
        $processAttendance = Attendance::processLectureAttendance($file, $lectureId, $globalData);

        if($processAttendance != 'success'){
            alert()->error('oops!', $processAttendance)->persistent('Close');
            return redirect()->back();
        }

        if($processAttendance ){
            alert()->success('Student lecture attendance uploaded successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('No file uploaded. Attendance not processed', '')->persistent('Close');
        return redirect()->back();
    }

    public function deleteStudentAttendance(Request $request){
        $validator = Validator::make($request->all(), [
            'attendance_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$lectureAttendance = LectureAttendance::find($request->attendance_id)){
            alert()->error('Oops', 'Invalid Student Lecture Attendance')->persistent('Close');
            return redirect()->back();
        }
        
        if($lectureAttendance->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function markStudentAttendance(Request $request){
        $validator = Validator::make($request->all(), [
            'lecture_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$courseLecture = CourseLecture::find($request->lecture_id)){
            alert()->error('Oops', 'Invalid Lecture')->persistent('Close');
            return redirect()->back();
        }

        $studentIds = $request->student_id;
        $lectureId = $request->lecture_id;

        foreach ($studentIds as $studentId) {
            $student = Student::with('applicant')->where('id', $studentId)->first();
            if(!$student){
                continue;
            }

            if($exist = LectureAttendance::where('course_lecture_id', $lectureId)->where('student_id', $student->id)->first()){
                continue;
            }

            $attendanceData = ([
                'course_lecture_id' => $lectureId,
                'student_id' => $student->id,
                'status' => 1
            ]);

            LectureAttendance::create($attendanceData);
        }

        alert()->success('Student lecture attendance uploaded successfully!', '')->persistent('Close');
        return redirect()->back();
    }


    public function sendMessage(Request $request){
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'programme_category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $staff = Staff::find($request->staff_id);
        $staffName = $staff->title.' '.$staff->lastname.' '.$staff->othernames;
        $staffId = $staff->id;

        $courseId = $request->course_id;
        $programmeCategoryId = $request->programme_category_id;

        $message = $request->message;
        $course = Course::with('level', 'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('id', $courseId)->first();
        $registeredStudents = $course->registrations->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->pluck('student');

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
        $academicSession = $request->academic_session;

        $validator = Validator::make($request->all(), [
            'result' => 'required|file',
            'course_id' => 'required',
            'type' => 'required',
            'programme_category_id' => 'required'
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

        $courseId = $request->course_id;
        $uploadType = $request->type;
        $programmeCategoryId = $request->programme_category_id;

    
        $file = $request->file('result');
        $processResult = Result::processResult($file, $courseId, $uploadType, $programmeCategoryId,  $academicSession);

        if($processResult != 'success'){
            alert()->error('oops!', $processResult)->persistent('Close');
            return redirect()->back();
        }

        if($processResult){
            alert()->success('Student scores updated successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('No file uploaded. Result not processed', '')->persistent('Close');
        return redirect()->back();
    }

    public function updateStudentResult(Request $request){
        $academicSession = $request->academic_session;

        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'matric_number' => 'required',
            'type' => 'required',
            'programme_category_id' => 'required',
        ]);

        $matricNumber = $request->matric_number;
        $uploadType = $request->type;
        $programmeCategoryId = $request->programme_category_id;


        if(!$student = Student::where('matric_number', $matricNumber)->first()){
            alert()->error('Invalid Matric Number', '')->persistent('Close');
            return redirect()->back();
        }
        $studentId = $student->id;
        $courseId = $request->course_id;

        $studentRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'academic_session' => $academicSession,
            'programme_category_id' => $programmeCategoryId
        ])->first();

        if(!$studentRegistration){
            alert()->error('Student didnt enroll for this course', '')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($studentRegistration->result_approval_id)){
            alert()->error('Result already approved', 'Visit the ICT with relevant approval for modification')->persistent('Close');
            return redirect()->back();
        }

        $testScore = $studentRegistration->ca_score;
        $examScore = $studentRegistration->exam_score;

        if(strtolower($uploadType) != 'test'){
            $examScore = $request->exam;
        }else{
            $testScore = $request->test;
        }

        $studentRegistration->ca_score = $testScore;


        if($examScore > 0 && strtolower($uploadType) != 'test'){
            $totalScore = $testScore + $examScore;

            if($totalScore > 100){
                alert()->success('Oops', 'Total score is greater than 100.')->persistent('Close');
                return redirect()->back();
            }
    
            $grading = GradeScale::computeGrade($totalScore);
            $grade = $grading->grade;
            $points = $grading->point;
    
            $courseCode = $studentRegistration->course_code;
    
            if (strpos($courseCode, 'NSC') !== false && $student->programme_id == 15) {
                if($totalScore < 50){
                    $grade = 'F';
                    $points = 0;
                }
            }

            $studentRegistration->exam_score = $examScore;
            $studentRegistration->total = $totalScore;
            $studentRegistration->grade = $grade;
            $studentRegistration->points = $studentRegistration->course_credit_unit * $points;
        }

        if($studentRegistration->save()){
            alert()->success('Student scores updated successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function uploadVocationResult(Request $request){
        $globalData = $request->input('global_data');

        $validator = Validator::make($request->all(), [
            'result' => 'required|file',
            'programme_category_id' => 'required'
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

    
        $file = $request->file('result');
        $programmeCategoryId = $request->programme_category_id;

        $processResult = Result::processVocationResult($file, $programmeCategoryId, $globalData);

        if($processResult != 'success'){
            alert()->error('oops!', $processResult)->persistent('Close');
            return redirect()->back();
        }

        if($processResult){
            alert()->success('Student scores updated successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('No file uploaded. Result not processed', '')->persistent('Close');
        return redirect()->back();
    }

    public function changeProgramme(){
        return view('admin.changeProgramme');
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

    public function changeStudentProgramme (Request $request) {
        $validator = Validator::make($request->all(), [
            'studentId' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        
        if(!$student = Student::find($request->studentId)){
            alert()->error('Oops', 'Invalid Student ')->persistent('Close');
            return redirect()->back();
        }

        $academicSession = $student->academic_session;
        $changeMatricNumber = false;

        if(!empty($request->level_id) && ($request->level_id != $student->level_id)){
            $student->level_id = $request->level_id;
        }

        if(!empty($request->programme_id) && ($request->programme_id != $student->programme_id)){
            $student->programme_id = $request->programme_id;
        }

        if(!empty($request->department_id) && ($request->department_id != $student->department_id)){
            $student->department_id = $request->department_id;
        }

        if(!empty($request->faculty_id) && ($request->faculty_id != $student->faculty_id)){
            $student->faculty_id = $request->faculty_id;
        }

        if(empty($student->cgpa) && $student->level_id == 1){
            $changeMatricNumber = true;
            $student->matric_number = null;
        }

        if($student->save()){
            $student->refresh();
            $studentId = $student->id;
            $applicantId = $student->user_id;
            $applicant = User::find($applicantId);
            $applicationType = $applicant->application_type;

            if($changeMatricNumber){
                $this->generateMatricAndEmail($student);
            }

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
                alert()->success('Student details updated successfully', '')->persistent('Close');
                return $this->getSingleStudent($student->matric_number, $request->url);
            }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return $this->getSingleStudent($student->matric_number, $request->url);
    }

    public function getCourseResult(){
        $programme = Programme::get();
        $academicSessions = Session::get();
        $academicLevels = AcademicLevel::get();
        $allCourses = Course::all();

        return view('admin.courseResults', [
            'programmes' => $programme,
            'academicSessions' => $academicSessions,
            'academicLevels' => $academicLevels,
            'allCourses' => $allCourses
        ]);
    }

    public function getCourseResults(Request $request){
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
            'academic_session' => 'required',
            'level_id' => 'required',
            'course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$courseForReg = CoursePerProgrammePerAcademicSession::where('programme_id', $request->programme_id)
        ->where('academic_session', $request->academic_session)
        ->where('level_id', $request->level_id)
        ->where('course_id', $request->course_id)
        ->first()){
            alert()->error('Oops', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        $programme = Programme::get();
        $academicSessions = Session::get();
        $academicLevels = AcademicLevel::get();
        $allCourses = Course::all();

        return view('admin.courseResults', [
            'programmes' => $programme,
            'academicSessions' => $academicSessions,
            'academicLevels' => $academicLevels,
            'allCourses' => $allCourses,
            'courseForReg' => $courseForReg
        ]);
    }

    public function updateCourseResult(Request $request){

        $programme = Programme::get();
        $academicSessions = Session::get();
        $academicLevels = AcademicLevel::get();
        $allCourses = Course::all();
        $courseForReg = null;


        $validator = Validator::make($request->all(), [
            'course_per_prog_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return view('admin.courseResults', [
                'programmes' => $programme,
                'academicSessions' => $academicSessions,
                'academicLevels' => $academicLevels,
                'allCourses' => $allCourses,
                'courseForReg' => $courseForReg
            ]);
        }
        $courseForReg = CoursePerProgrammePerAcademicSession::find($request->course_per_prog_id);
        if (!$courseForReg) {
            alert()->error('Oops', 'Record not found')->persistent('Close');

            return view('admin.courseResults', [
                'programmes' => $programme,
                'academicSessions' => $academicSessions,
                'academicLevels' => $academicLevels,
                'allCourses' => $allCourses,
                'courseForReg' => $courseForReg
            ]);
        }

        $updatedFields = [];
        if (!empty($request->level_id) && $request->level_id != $courseForReg->level_id) {
            $courseForReg->level_id = $request->level_id;
            $updatedFields['level_id'] = $request->level_id;
        }

        if (!empty($request->session) && $request->session != $courseForReg->academic_session) {
            $courseForReg->academic_session = $request->session;
            $updatedFields['academic_session'] = $request->session;
        }

        if (!empty($request->semester) && $request->semester != $courseForReg->semester) {
            $courseForReg->semester = $request->semester;
            $updatedFields['semester'] = $request->semester;
        }

        if (!empty($request->credit_unit) && $request->credit_unit != $courseForReg->credit_unit) {
            $courseForReg->credit_unit = $request->credit_unit;
            $updatedFields['credit_unit'] = $request->credit_unit;

            CourseRegistration::where('programme_course_id', $request->course_per_prog_id)
                ->where('academic_session', $request->session)
                ->where('level_id', $request->level_id)
                ->update(['course_credit_unit' => $request->credit_unit]);


            $registrations = CourseRegistration::where('programme_course_id', $request->course_per_prog_id)
                ->where('academic_session', $request->session)
                ->where('level_id', $request->level_id)
                ->whereNotNull('total')
                ->get();

            foreach ($registrations as $registration) {
                $grading = GradeScale::computeGrade($registration->total);
                $points = $grading->point;

                $course = Course::find($registration->course_id);
                $student = Student::find($registration->student_id);

                if ($course && $student) {
                    $courseCode = $course->code;
                    if (strpos($courseCode, 'NSC') !== false && $student->programme_id == 15) {
                        if ($registration->total < 50) {
                            $grading->grade = 'F';
                            $points = 0;
                        }
                    }

                    $registration->points = $points * $request->credit_unit;
                    $registration->save();
                }
            }
        }

        if (!empty($updatedFields)) {
            $courseForReg->save();
            alert()->success('Changes saved successfully', '')->persistent('Close');
        }

        return view('admin.courseResults', [
            'programmes' => $programme,
            'academicSessions' => $academicSessions,
            'academicLevels' => $academicLevels,
            'allCourses' => $allCourses,
            'courseForReg' => $courseForReg
        ]);
        
    }

    public function adviserProgrammes(Request $request, $programmeCategory){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $programmeCategory = ProgrammeCategory::where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $adviserProgrammesQuery = LevelAdviser::with('programme', 'level')->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession);
        $adviserProgrammes = $adviserProgrammesQuery->get();


        foreach ($adviserProgrammes as $adviserProgramme) {
            $levelId = $adviserProgramme->level_id;
            $programmeId = $adviserProgramme->programme_id;
        
            $studentIds = Student::where('level_id', $levelId)
                ->where('programme_id', $programmeId)
                ->where('programme_category_id', $programmeCategoryId)
                ->pluck('id')
                ->toArray();
        
            $studentRegistrationsCount = StudentCourseRegistration::with('student', 'student.applicant')
                ->whereIn('student_id', $studentIds)
                ->where('level_id', $levelId)
                ->where('programme_category_id', $programmeCategoryId)
                ->where('academic_session', $academicSession)
                ->where(function ($query) {
                    $query->where('level_adviser_status', null)
                          ->orWhere('hod_status', null);
                })
                ->count();

            $coursesForReg = CoursePerProgrammePerAcademicSession::where('programme_id', $programmeId)
                ->where('academic_session', $academicSession)
                ->where('programme_category_id', $programmeCategoryId)
                ->where('level_id', $levelId)
                ->get();
        
            // Add studentRegistrationsCount to the object
            $adviserProgramme->studentRegistrationsCount = $studentRegistrationsCount;
            $adviserProgramme->coursesForReg = $coursesForReg;
        }

        return view('admin.adviserProgrammes', [
            'adviserProgrammes' => $adviserProgrammes,
            'programmeCategory' => $programmeCategory,
            'academicSession' => $academicSession
        ]);
    }

    Public function levelCourseReg(Request $request, $programmeCategory, $id){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        if(!$adviserProgramme = LevelAdviser::with('programme', 'level')->where('id', $id)->first()){
            alert()->error('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        $programmeCategory = ProgrammeCategory::where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $levelId = $adviserProgramme->level_id;
        $programmeId = $adviserProgramme->programme_id;

        $studentIds = Student::where('level_id', $levelId)
        ->where('programme_category_id', $programmeCategoryId)
        ->where('programme_id', $programmeId)
        ->pluck('id')
        ->toArray();
    
        $studentRegistrations = StudentCourseRegistration::with('student', 'student.applicant')
        ->whereIn('student_id', $studentIds)
        ->where('programme_category_id', $programmeCategoryId)
        ->where('level_id', $levelId)
        ->where('academic_session', $academicSession)
        ->get();

        return view('admin.levelCourseReg', [
            'studentRegistrations' => $studentRegistrations
        ]);
    }

    public function levelStudents(Request $request, $programmeCategory, $id){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $adviserProgramme = LevelAdviser::with('programme', 'level')
            ->where('id', $id)
            ->first();

        if (!$adviserProgramme) {
            alert()->error('Oops!', 'Record not found')->persistent('Close');
            return redirect()->back();
        }

        $programmeCategory = ProgrammeCategory::where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $levelId = $adviserProgramme->level_id;
        $programmeId = $adviserProgramme->programme_id;

        $students = Student::with([
                'applicant', 
                'programme', 
                'transactions', 
                'courseRegistrationDocument', 
                'registeredCourses', 
                'partner', 
                'academicLevel', 
                'department', 
                'faculty'
            ])
            ->where([
                'level_id' => $levelId,
                'programme_id' => $programmeId,
                'programme_category_id' => $programmeCategoryId,
                'is_active' => true,
                'is_passed_out' => false,
                'is_rusticated' => false
            ])
            ->get();

        foreach ($students as $student) {
            $hasRegistered = $student->courseRegistrationDocument()
                ->where('academic_session', $academicSession)
                ->exists();

            $student->courseRegistrationStatus = $hasRegistered ? true : false;
        }

        return view('admin.levelStudents', [
            'students' => $students
        ]);
    }

    public function courseApproval(Request $request) {
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $comment = $request->comment;
        $status = $request->status == 'request changes'?'pending':$request->status;

        $levelAdviser = LevelAdviser::find($request->level_adviser_id);
        if(!$levelAdviser){
            alert()->error('Oops', 'Invalid Level Adviser ')->persistent('Close');
            return redirect()->back();
        }

        $programme = $levelAdviser->programme->name;
        $level = $levelAdviser->level->level .' Level ';

        $levelAdviser->comment = $comment;
        $levelAdviser->course_approval_status = $status;
        $senderName = env('SCHOOL_NAME');


        if($status == null){
            
            $message = 'You have been requested to make changes to '. $level.$programme .' courses. Please review on the staff portal.';
            
            if($levelAdviser->staff){
                $mail = new NotificationMail($senderName, $message, $levelAdviser->staff->title.' '.$levelAdviser->staff->lastname.' '.$levelAdviser->staff->othernames);

                Mail::to($levelAdviser->staff->email)->send($mail);
                Notification::create([
                    'staff_id' => $levelAdviser->staff->id,
                    'description' => $message,
                    'status' => 0
                ]);
            }
        }

        if($status == 'approved'){
            $courses = CoursePerProgrammePerAcademicSession::where('programme_id', $request->programme_id)
            ->where('level_id', $request->level_id)
            ->where('academic_session', $academicSession)
            ->update(['dap_approval_status' => 'approved']);

            $message = 'Courses for'. $level.$programme .'students have been approved by DAP. Kindly proceed to open course registration for students.';
            
            $senderName = env('SCHOOL_NAME');
            $receiverName = 'Portal Admininstrator';
            $adminEmail = env('APP_EMAIL');
            
            $mail = new NotificationMail($senderName, $message, $receiverName);
            Mail::to($adminEmail)->send($mail);
        }

        if($levelAdviser->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

}

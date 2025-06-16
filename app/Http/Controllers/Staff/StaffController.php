<?php

namespace App\Http\Controllers\Staff;

use Alert;
use App\Http\Controllers\Controller;
use App\Http\Requests;
use App\Libraries\Attendance\Attendance as StudentAttendance;
use App\Libraries\Google\Google;
use App\Libraries\Result\Result;
use App\Mail\NotificationMail;
use App\Models\AcademicLevel;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseManagement;
use App\Models\CoursePerProgrammePerAcademicSession;
use App\Models\CourseRegistration;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\GradeScale;
use App\Models\LectureAttendance;
use App\Models\LevelAdviser;
use App\Models\Notification;
use App\Models\Programme;
use App\Models\ProgrammeCategory as Category;
use App\Models\ProgrammeCategory;
use App\Models\Role;
use App\Models\Session;
use App\Models\Staff;
use App\Models\StaffRole;
use App\Models\Student;
use App\Models\SummerCourseRegistration;
use App\Models\Unit;
use App\Models\User as Applicant;
use Carbon\Carbon;


use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Log;
use Mail;
use SweetAlert;

class StaffController extends Controller
{
    //

    public function index(Request $request){
        $staff = Auth::guard('staff')->user();
        $referalCode = $staff->referral_code;
        $globalData = $request->input('global_data');
        $units = Unit::get();

        $programmeCategories = ProgrammeCategory::with('academicSessionSetting')->get();

        $year = Carbon::parse()->format('Y');
        $month = Carbon::parse()->format('M');
        $capturedWorkingDays = $this->capturedWorkingDays();

        $startDateOfPresentMonth = Carbon::now()->startOfMonth();
        $endDateOfPresentMonth = Carbon::now()->endOfMonth();

        $monthAttendance = Attendance::where('staff_id', $staff->id)->whereBetween('date', [$startDateOfPresentMonth, $endDateOfPresentMonth])->get();

        if(empty($staff->change_password)){
            return view('staff.changePassword');
        }

        if((strtolower($staff->category) != 'academic') && empty($staff->unit_id)){
            return view('staff.updateUnit', [
                'units' => $units
            ]);
        }

        $allApplicants = collect();
        $staffCourses = collect();

        foreach ($programmeCategories as $category) {
            if ($category->academicSessionSetting) {
                $academicSession = $category->academicSessionSetting->academic_session;
                $programmeCategoryId = $category->id;

                $courses = CourseManagement::with(['course'])
                            ->where('staff_id', $staff->id)
                            ->where('programme_category_id', $programmeCategoryId)
                            ->where('academic_session', $academicSession)
                            ->get();
                

                 $staffCourses = $staffCourses->merge($courses);


                $applicationSession = $category->academicSessionSetting->application_session ?? null;
                if ($applicationSession) {
                    $applicants = Applicant::where('academic_session', $applicationSession)
                        ->where('referrer', $referalCode)
                        ->get();

                    $allApplicants = $allApplicants->merge($applicants);
                }

            }
        }

         // Remove duplicates if necessary
        $applicants = $allApplicants->unique('id')->values();

        // $google = new Google();
        // $google->addMemberToGroup($staff->email, env('GOOGLE_STAFF_GROUP'));
        // if(strtolower($staff->category) == 'academic'){
        //     $google->addMemberToGroup($staff->email, env('GOOGLE_ACADEMIC_STAFF_GROUP'));
        // }else{
        //     $google->addMemberToGroup($staff->email, env('GOOGLE_NON_ACADEMIC_STAFF_GROUP'));
        // }

        return view('staff.home', [
            'applicants' => $applicants,
            'capturedWorkingDays' => $capturedWorkingDays,
            'monthAttendance' => $monthAttendance,
            'programmeCategories' => $programmeCategories,
            'staffCourses' => $staffCourses
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
            'password' => 'required',
            'confirm_password' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $staff = Auth::guard('staff')->user();

        if($request->has('case')){
            if($request->password == $request->confirm_password){
                $staff->password = bcrypt($request->password);
            }else{
                alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                return redirect()->back();
            }
            $staff->change_password = true;
        }else{
            if(!empty($request->old_password)){
                alert()->error('Oops!', 'Old password is required')->persistent('Close');
                return redirect()->back();
            }
            if(\Hash::check($request->old_password, Auth::guard('staff')->user()->password)){
                if($request->password == $request->confirm_password){
                    $staff->password = bcrypt($request->password);
                }else{
                    alert()->error('Oops!', 'Password mismatch')->persistent('Close');
                    return redirect()->back();
                }
            }else{
                alert()->error('Oops', 'Wrong old password, Try again with the right one')->persistent('Close');
                return redirect()->back();
            }
        }

        if($staff->update()) {
            alert()->success('Success', 'Save Changes')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function uploadSignature (Request $request) {

        $validator = Validator::make($request->all(), [
            'image' => 'required',
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $staff = Auth::guard('staff')->user();

        if(\Hash::check($request->old_password, Auth::guard('staff')->user()->password)){
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-',$staff->title.$staff->lastname.$staff->othernames.time())));

            $signature = $staff->signature;
            if (file_exists($signature)) {
                unlink($signature);
            } 

            $imageUrl = 'uploads/staff/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
            $image = $request->file('image')->move('uploads/staff', $imageUrl);
            $staff->signature = $imageUrl;
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

    public function updateStaffUnit(Request $request){
        $validator = Validator::make($request->all(), [
            'unit_id' => 'required',
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $staff = Auth::guard('staff')->user();

        if(!empty($request->unit_id) && $request->unit_id != $staff->unit_id){
            $staff->unit_id = $request->unit_id;
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
        $programmeCategories = ProgrammeCategory::with('academicSessionSetting')->get();
        $staff = Auth::guard('staff')->user();

        $staffCourses = collect();

        foreach ($programmeCategories as $category) {
            if ($category->academicSessionSetting) {
                $academicSession = $category->academicSessionSetting->academic_session;
                $programmeCategoryId = $category->id;

                $courses = CourseManagement::with(['course'])
                            ->where('staff_id', $staff->id)
                            ->where('programme_category_id', $programmeCategoryId)
                            ->where('academic_session', $academicSession)
                            ->get();
                

                 $staffCourses = $staffCourses->merge($courses);
            }
        }

        // dd($staffCourses);

        return view('staff.courses', [
            'staffCourses' => $staffCourses,
            'programmeCategories' => $programmeCategories,
            'staff' => $staff,
        ]);
    }

    public function studentCourses(Request $request, $programmeCategory){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $programmeCategory = Category::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $programmes = Programme::with('department', 'department.faculty')->where('category_id', $programmeCategoryId)->where('department_id', $staff->department_id)->get();


        if(empty($staff->acad_department)){
            $programmes = Programme::with('department', 'department.faculty')->get();
        }

        $staffDean = false;
        if(!empty($staff->faculty) && $staff->id == $staff->faculty->dean_id){
            $staffDean = true;
        }

        if ($staffDean) {
            $faculty = Faculty::with('departments.programmes')
                ->where('id', $staff->faculty_id)
                ->first();
        
            $programmes = $faculty->departments->flatMap(function($department) {
                return $department->programmes;
            });
        }

        $staffHod = false;
        if(!empty($staff->acad_department) && $staff->id == $staff->acad_department->hod_id){
            $staffHod = true;
        }

        if($staffHod){
            $departmentId = $staff->department_id;
            $programmesIDs= Programme::where('department_id', $departmentId)->pluck('id')->toArray();

            $programmes = Programme::with('department', 'department.faculty')->whereIn('id', $programmesIDs)->get();
        }
        

        $academicLevels = AcademicLevel::get();
        $programmeCategories = Category::get();


        return view('staff.studentCourses',[
            'programmes' => $programmes,
            'academicLevels' => $academicLevels,
            'programmeCategories' => $programmeCategories,
            'programmeCategory' => $programmeCategory
        ]);
    }

    public function getStudentCourses(Request $request){

        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
            'level_id' => 'required',
            'semester' => 'required',
            'programme_category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = Category::find($programmeCategoryId);


        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;
        if (!$academicSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $courses = CoursePerProgrammePerAcademicSession::with('course')
            ->where('programme_id', $request->programme_id)
            ->where('level_id', $request->level_id)
            ->where('academic_session', $academicSession)
            ->where('semester', $request->semester)
            ->where('programme_category_id', $request->programme_category_id)
            ->get();

        $programme = Programme::find($request->programme_id);
        $academicLevel = AcademicLevel::find($request->level_id);
        $allCourses = Course::all();

        $programmes = Programme::get();
        $academicLevels = AcademicLevel::get();
        $programmeCategories = Category::get();

        return view('staff.studentCourses',[
            'programmes' => $programmes,
            'academicLevels' => $academicLevels,
            'courses' => $courses,
            'academiclevel' => $academicLevel,
            'programme' => $programme,
            'semester' => $request->semester,
            'allCourses' => $allCourses,
            'programmeCategories' => $programmeCategories,
            'academic_session' => $academicSession,
            'programme_category_id' => $request->programme_category_id,
            'programmeCategory' => Category::find($request->programme_category_id)
        ]);
    }

    // this is obsolete
    // public function addCourseForStudent(Request $request){
    //     $globalData = $request->input('global_data');
    //     $academicSession = $globalData->sessionSetting['academic_session'];

    //     $courses = CoursePerProgrammePerAcademicSession::with('course')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
    //     $defaultData = [
    //         'courses' => $courses,
    //         'academiclevel' => AcademicLevel::find($request->level_id),
    //         'programme' => Programme::find($request->programme_id),
    //         'semester' => $request->semester,
    //         'allCourses' => Course::all(),
    //     ];

    //     $validator = Validator::make($request->all(), [
    //         'course_id' => 'required',
    //         'level_id' => 'required',
    //         'programme_id' => 'required',
    //         'semester' => 'required',
    //         'credit_unit' => 'required',
    //     ]);

    //     if($validator->fails()) {
    //         alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
    //         return view('staff.studentCourses',$defaultData);
    //     }


    //     $courseRegistrationSetting = CourseRegistrationSetting::first();
    //     if($courseRegistrationSetting->status != 'stop'){
    //         alert()->error('Oops', 'Course Registration already started')->persistent('Close');
    //         return view('staff.studentCourses', $defaultData);
    //     }
        
    //     if(!$course = Course::find($request->course_id)){
    //         alert()->error('Oops', 'Invalid course ')->persistent('Close');
    //         return view('staff.studentCourses',$defaultData);
    //     }

    //     $exist = CoursePerProgrammePerAcademicSession::where([
    //         'course_id' => $course->id,
    //         'level_id' => $request->level_id,
    //         'programme_id' => $request->programme_id,
    //         'semester' => $request->semester,
    //         'credit_unit' => $request->credit_unit,
    //         'academic_session' => $academicSession,
    //     ])->first();

    //     if($exist){
    //         alert()->error('Oops!', 'Course already added')->persistent('Close');
    //         return view('staff.studentCourses', $defaultData);
    //     }
        
    //     $newCourses = [
    //         'course_id' => $course->id,
    //         'level_id' => $request->level_id,
    //         'programme_id' => $request->programme_id,
    //         'semester' => $request->semester,
    //         'credit_unit' => $request->credit_unit,
    //         'academic_session' => $academicSession,
    //         'status' => $request->status,
    //     ];
        
    //     if(CoursePerProgrammePerAcademicSession::create($newCourses)){
    //         alert()->success('Course added successfully', '')->persistent('Close');
    //         $courses = CoursePerProgrammePerAcademicSession::with('course', 'course.courseManagement', 'course.courseManagement.staff')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
    //         $defaultData = [
    //             'courses' => $courses,
    //             'academiclevel' => AcademicLevel::find($request->level_id),
    //             'programme' => Programme::find($request->programme_id),
    //             'semester' => $request->semester,
    //             'allCourses' => Course::all(),
    //         ];
    //         return view('staff.studentCourses',$defaultData);
    //     }

    //     alert()->error('Oops', 'Invalid course ')->persistent('Close');
    //     return view('staff.studentCourses', $defaultData);
    // }

    public function courseDetail(Request $request, $id, $programmeCategory, $academicSession = null){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        if(!empty($academicSession)){
            $academicSession = str_replace('-', '/', $academicSession);
        }

        $programmeCategory = Category::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        if(empty($academicSession)){
            $academicSession = $programmeCategory->academicSessionSetting->academic_session;
        }

        $lecturerDetails = CourseManagement::where('course_id', $id)->where('academic_session', $academicSession)->first(); 
        $registrations = CourseRegistration::where('course_id', $id)->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->get();
        $courseLectures = CourseLecture::with('lectureAttendance')->where('course_id', $id)->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->get();
        $summerCourseRegistrations = SummerCourseRegistration::with('courseRegistration')->where('course_id', $id)->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->get();

        $course = Course::find($id);

        return view('staff.courseDetail', [
            'registrations' => $registrations,
            'summerCourseRegistrations' => $summerCourseRegistrations,
            'lecturerDetails' => $lecturerDetails,
            'courseLectures' => $courseLectures,
            'course' => $course,
            'academicSession' => $academicSession,
            'programmeCategory' => $programmeCategory
        ]);
    }

    public function reffs(Request $request){
        $staff = Auth::guard('staff')->user();
        $globalData = $request->input('global_data');
        $referalCode = $staff->referral_code;

        $allApplicants = collect();

        foreach ($globalData->sessionSettings as $programmeCategoryId => $setting) {
            $applicationSession = $setting->application_session ?? null;

            if ($applicationSession) {
                $applicants = Applicant::where('academic_session', $applicationSession)
                    ->where('referrer', $referalCode)
                    ->get();

                $allApplicants = $allApplicants->merge($applicants);
            }
        }

        $applicants = $allApplicants->unique('id')->values();

        return view('staff.reffs', [
            'applicants' => $applicants,
        ]);
    }

    public function getAllReffs(Request $request){
        $staff = Auth::guard('staff')->user();
        $referalCode = $staff->referral_code;

        $globalData = $request->input('global_data');

        $allApplicants = collect();

        foreach ($globalData->sessionSettings as $programmeCategoryId => $setting) {
            $applicationSession = $setting->application_session ?? null;

            if ($applicationSession) {
                $applicants = Applicant::where('academic_session', $applicationSession)
                    ->where('referrer', $referalCode)
                    ->get();

                $allApplicants = $allApplicants->merge($applicants);
            }
        }

        $applicants = $allApplicants->unique('id')->values();


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
        $student = Student::withTrashed()->with('applicant', 'applicant.utmes', 'programme', 'transactions')->where('slug', $slug)->first();
        $academicLevels = AcademicLevel::orderBy('id', 'desc')->get();
        $sessions = Session::orderBy('id', 'desc')->get();

        return view('staff.studentProfile', [
            'student' => $student,
            'academicLevels' => $academicLevels,
            'sessions' => $sessions
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
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'course_id' => 'required',
            'programme_category_id' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting')->where('category', $request->programmeCategory)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session;

        $exist = CourseManagement::where([
            'course_id' => $request->course_id,
            'staff_id' => $request->staff_id,
            'programme_category_id' => $request->programme_category_id,
            'academic_session' => $academicSession
        ])->first();

        if($exist){
            alert()->error('Oops!', 'Course already assigned to staff')->persistent('Close');
            return redirect()->back();
        }

        $courseAssign = CourseManagement::create([
            'course_id' => $request->course_id,
            'staff_id' => $request->staff_id,
            'programme_category_id' => $request->programme_category_id,
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
            'course_id' => $request->course_id,
            'staff_id' => $request->staff_id,
            'programme_category_id' => $request->programme_category_id,
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting')->where('category', $request->programmeCategory)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session ?? null;

        if (!$academicSession) {
            alert()->error('Oops!', 'Session setting for programme category not found.')->persistent('Close');
            return redirect()->back();
        }

        $unset = CourseManagement::where([
            'course_id' => $request->course_id,
            'staff_id' => $request->staff_id,
            'programme_category_id' => $request->programme_category_id,
            'academic_session' => $academicSession
        ])->delete();
        
        if($unset) {
            alert()->success('Success', 'Staff unassigned successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'An Error Occurred')->persistent('Close');
        return redirect()->back();
    }

    public function sendMessage(Request $request){
        
        $validator = Validator::make($request->all(), [
            'message' => 'required',
            'programme_category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session;

        $staff = Auth::guard('staff')->user();
        $staffName = $staff->title.' '.$staff->lastname.' '.$staff->othernames;
        $staffId = $staff->id;

        $message = $request->message;
        $programmeCategoryId = $request->programme_category_id;
        $isSummer = $request->has('summer') && $request->summer == 1;

        $course = Course::with('level', 'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('id', $request->course_id)->first();
        $registeredStudents = $course->registrations->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->pluck('student');

        if($isSummer){
            $summerCourseRegistrations = SummerCourseRegistration::with('courseRegistration')->where('course_id', $course->id)->where('programme_category_id', $programmeCategoryId)->where('academic_session', $academicSession)->get();

            if($summerCourseRegistrations->count() > 0){
                $registeredStudents = $summerCourseRegistrations->pluck('courseRegistration.student');
            }
        }

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
            if(env('SEND_MAIL')){
                //send a notification mail
                Mail::to($request->email)->send(new NotificationMail($staffName, $message, $receiverName));
            }
        }

        alert()->success('Message sent', '')->persistent('Close');
        return redirect()->back();
    }

    public function staffUploadResult(Request $request){
        $staff = Auth::guard('staff')->user();
        $uploadType = $request->type;

        $validator = Validator::make($request->all(), [
            'result' => 'required|file',
            'course_id' => 'required',
            'programme_category_id' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session;
        $resultProcessStatus = $programmeCategory->examSetting->result_processing_status;
        $testProcessStatus = $programmeCategory->examSetting->test_processing_status;

        if(strtolower($uploadType) != 'test'){
            if(strtolower($resultProcessStatus) != 'start'){
                alert()->error('Result Processing has not started yet', 'Contact ICT')->persistent('Close');
                return redirect()->back();
            }
        }else{
            if(strtolower($testProcessStatus) != 'start'){
                alert()->error('Test Processing has not started yet', 'Contact ICT')->persistent('Close');
                return redirect()->back();
            }
        }

        // $courseManagement = CourseManagement::where([
        //     'course_id' => $request->course_id,
        //     'staff_id' => $staffId,
        //     'academic_session' => $academicSession
        // ])->first();

        // if(!$courseManagement){
        //     alert()->error('Oops!', 'Course not assigned to staff')->persistent('Close');
        //     return redirect()->back();
        // }

        // $courseManagementCourseCode = $courseManagement->passcode;
        // if(!empty($request->passcode) && $request->passcode != $courseManagementCourseCode){
        //     alert()->error('Oops!', 'Wrong Password, No changes was made')->persistent('Close');
        //     return redirect()->back();
        // }

        $file = $request->file('result');
        $fileExtension = $file->getClientOriginalExtension();
        
        if ($fileExtension != 'csv') {
            alert()->error('Invalid file format, only CSV is allowed', '')->persistent('Close');
            return redirect()->back();
        }

        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $courseId = $request->course_id;
        $programmeCategoryId = $request->programme_category_id;
        $isSummer = $request->has('is_summer') && $request->is_summer == 1;


    
        $file = $request->file('result');
        $processResult = Result::processResult(
            $file,
            $courseId,
            $uploadType,
            $programmeCategoryId,
            $academicSession,
            $isSummer
        );

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

    public function createLecture(Request $request){

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

        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session;

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $course->code.$request->topic)));

        $code = $this->generateRandomString(5);

        $createLectureData = ([
            'course_id' => $request->course_id,
            'topic' => $request->topic,
            'duration' => $request->duration,
            'date' => $request->date,
            'slug' => $slug,
            'academic_session' => $academicSession,
            'programme_category_id' => $request->programme_category_id,
            'code' => $code
        ]);

        if(CourseLecture::create($createLectureData)){
            alert()->success('Lecture created successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error while creating lecture', '')->persistent('Close');
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
        $courseLecture = CourseLecture::find($lectureId);
        $academicSession = $courseLecture;

    
        $file = $request->file('attendance');
        $processAttendance = StudentAttendance::processLectureAttendance($file, $lectureId, $academicSession);

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

        if($lecture->update($updateLectureData)){
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


    public function updateStudentResult(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $uploadType = $request->type;
        $isSummer = $request->has('summer') && $request->summer == 1;

        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'matric_number' => 'required',
            'programme_category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session;

        $resultProcessStatus = $programmeCategory->examSetting->result_processing_status;
        $testProcessStatus = $programmeCategory->examSetting->test_processing_status;

        if(strtolower($uploadType) != 'test'){
            if(strtolower($resultProcessStatus) != 'start'){
                alert()->error('Result Processing has not started yet', 'Contact ICT')->persistent('Close');
                return redirect()->back();
            }
        }else{
            if(strtolower($testProcessStatus) != 'start'){
                alert()->error('Test Processing has not started yet', 'Contact ICT')->persistent('Close');
                return redirect()->back();
            }
        }

        $courseManagement = CourseManagement::where([
            'course_id' => $request->course_id,
            'staff_id' => $staffId,
            'academic_session' => $academicSession
        ])->first();

        if(!$courseManagement){
            alert()->error('Oops!', 'Course not assigned to staff')->persistent('Close');
            return redirect()->back();
        }

        $courseManagementCourseCode = $courseManagement->passcode;
        if(!empty($request->passcode) && $request->passcode != $courseManagementCourseCode && !$isSummer){
            alert()->error('Oops!', 'Wrong Password, No changes was made')->persistent('Close');
            return redirect()->back();
        }

        $matricNumber = $request->matric_number;

        if(!$student = Student::where('matric_number', $matricNumber)->first()){
            alert()->error('Invalid Matric Number', '')->persistent('Close');
            return redirect()->back();
        }
        $studentId = $student->id;
        $courseId = $request->course_id;

        $course = Course::find($courseId);

        $studentRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'academic_session' => $academicSession,
            'programme_category_id' => $request->programme_category_id
        ])->first();

        if(!$studentRegistration){
            alert()->error('Student didnt enroll for this course', '')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($studentRegistration->result_approval_id) && !$isSummer){
            alert()->error('Result already approved', 'Visit the ICT with relevant approval for modification')->persistent('Close');
            return redirect()->back();
        }

        $testScore = $request->test;
        $examScore = $request->exam;

        if ($uploadType == 'test') {
            $examScore = $studentRegistration->exam_score;
            $studentRegistration->ca_score = $testScore;
        }

        if($uploadType == 'exam') {
            $testScore = $studentRegistration->ca_score;
            $studentRegistration->exam_score = $examScore;
        }

        if ($uploadType == 'both') {
            $studentRegistration->ca_score = $testScore;
            $studentRegistration->exam_score = $examScore;
        }


        if($examScore > 0 && strtolower($uploadType) != 'test'){
            $totalScore = $testScore + $examScore;

            if($totalScore > 100){
                alert()->success('Oops', 'Total score is greater than 100.')->persistent('Close');
                return redirect()->back();
            }
    
            $grading = GradeScale::computeGrade($totalScore);
            $grade = $grading->grade;
            $points = $grading->point;

            $studentFaculty = Faculty::find($student->faculty_id);
            if($studentFaculty->id == 3 || $studentFaculty->id == 7){
                if($student->department_id == $course->department_id){
                    if($totalScore < 50){
                        $grade = 'F';
                        $points = 0;
                    }
                }
            }
    
            // $courseCode = $studentRegistration->course_code;
            // if (strpos($courseCode, 'NSC') !== false && $student->programme_id == 15) {
            //     if($totalScore < 50){
            //         $grade = 'F';
            //         $points = 0;
            //     }
            // }

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
    
        $validator = Validator::make($request->all(), [
            'result' => 'required|file',
            'programme_category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $programmeCategoryId = $request->programme_category_id;
        $file = $request->file('result');
        $fileExtension = $file->getClientOriginalExtension();
        
        if ($fileExtension != 'csv') {
            alert()->error('Invalid file format, only CSV is allowed', '')->persistent('Close');
            return redirect()->back();
        }

    
        $file = $request->file('result');
        $processResult = Result::processVocationResult($file, $programmeCategoryId);

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

    public function roleAllocation(Request $request){

        return view('staff.roleAllocation');
    }

    public function staff(Request $request){

        $staff = Auth::guard('staff')->user();
        $facultyId = $staff->faculty_id;
        $departmentId = $staff->department_id;

        $staff  = Staff::withTrashed()->with('faculty', 'acad_department')->get();

        if(!empty($facultyId)){
            $staff  = Staff::withTrashed()->with('faculty', 'acad_department')->where('faculty_id', $facultyId)->get();
        }

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
        $programmeCategories = ProgrammeCategory::all();

        return view('staff.singleStaff', [
            'singleStaff' => $staff,
            'roles' => $roles,
            'departments' => $departments,
            'programmeCategories' => $programmeCategories
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
            'programme_category_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $programmeCategoryId = $request->programme_category_id;
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();
        $academicSession = $programmeCategory->academicSessionSetting->academic_session;

        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }

        if(!$programme = Programme::find($request->programme_id)){
            alert()->error('Oops', 'Invalid Programme ')->persistent('Close');
            return redirect()->back();
        }

        $levelAdviser = LevelAdviser::where('programme_id', $request->programme_id)
                        ->where('programme_category_id', $request->programme_category_id)
                        ->where('level_id', $request->level_id)
                        ->where('academic_session', $academicSession)
                        ->first();

        if ($levelAdviser) {
            $levelAdviser->update([
                'staff_id' => $staff->id
            ]);
        } else {
            // Level Adviser does not exist, create a new one
            LevelAdviser::create([
                'staff_id' => $staff->id,
                'programme_id' => $programme->id,
                'programme_category_id' => $request->programme_category_id,
                'level_id' => $request->level_id,
                'academic_session' => $academicSession
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

        alert()->success('Level adviser assigned to programme and level', '')->persistent('Close');
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
            'programme_category_id' => 'required',
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
                'programme_category_id' => $request->programme_category_id,
                'department_id' => $request->department_id,
            ])
            ->get();

        $department = Department::with('programmes', 'programmes.students', 'programmes.academicAdvisers', 'programmes.academicAdvisers.staff', 'programmes.academicAdvisers.level')->where('slug', $department->slug)->first();
        $levels = AcademicLevel::all();
        $categories = Category::all();

        return view('staff.department', [
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

    public function changeStudentBatch (Request $request) {

        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
            'batch' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$student = Student::find($request->student_id)){
            alert()->error('Oops', 'Invalid Student ')->persistent('Close');
            return redirect()->back();
        }

        $student->batch = $request->batch;

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

    public function chargeStudent(){
        return view('staff.chargeStudent');
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

        $studentIdCode = $request->reg_number;
        if($request->type == 'Student'){
            return $this->getSingleStudent($studentIdCode, 'staff.chargeStudent');
        }

        if($request->type == 'Applicant'){
            return $this->getSingleApplicant($studentIdCode, 'staff.chargeStudent');
        }
    }

}

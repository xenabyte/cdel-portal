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
use App\Models\Session;
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
use App\Models\CoursePerProgrammePerAcademicSession;
use App\Models\ProgrammeCategory as Category;
use App\Models\Attendance;
use App\Models\Unit;
use App\Models\CourseLecture;
use App\Models\LectureAttendance;

use App\Mail\NotificationMail;

use App\Libraries\Result\Result;
use App\Libraries\Google\Google;
use App\Libraries\Attendance\Attendance as StudentAttendance;

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
        $units = Unit::get();

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

        $applicants = Applicant::with('student')->where('referrer', $referalCode)->where('academic_session', $applicationSession)->get();

        $google = new Google();
        $google->addMemberToGroup($staff->email, env('GOOGLE_STAFF_GROUP'));
        if(strtolower($staff->category) == 'academic'){
            $google->addMemberToGroup($staff->email, env('GOOGLE_ACADEMIC_STAFF_GROUP'));
        }else{
            $google->addMemberToGroup($staff->email, env('GOOGLE_NON_ACADEMIC_STAFF_GROUP'));
        }

        return view('staff.home', [
            'applicants' => $applicants,
            'capturedWorkingDays' => $capturedWorkingDays,
            'monthAttendance' => $monthAttendance
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

        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        // $courses = CoursePerProgrammePerAcademicSession::with('course', 'course.courseManagement', 'course.courseManagement.staff',  'level',  'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('academic_session', $academicSession)->where('staff_id', $staffId)->first();

        return view('staff.courses');
    }

    public function studentCourses(Request $request){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;
        $programmes = Programme::with('department', 'department.faculty')->where('department_id', $staff->department_id)->get();


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

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $courses = CoursePerProgrammePerAcademicSession::with('course')->where('programme_id', $request->programme_id)->where('level_id', $request->level_id)->where('academic_session', $academicSession)->where('semester', $request->semester)->get();
        $programme = Programme::find($request->programme_id);
        $academicLevel = AcademicLevel::find($request->level_id);
        $allCourses = Course::all();

        $programmes = Programme::get();
        $academicLevels = AcademicLevel::get();

        return view('staff.studentCourses',[
            'programmes' => $programmes,
            'academicLevels' => $academicLevels,
            'courses' => $courses,
            'academiclevel' => $academicLevel,
            'programme' => $programme,
            'semester' => $request->semester,
            'allCourses' => $allCourses,
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

    public function courseDetail(Request $request, $id, $academicSession = null){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        if(!empty($academicSession)){
            $academicSession = str_replace('-', '/', $academicSession);
        }

        if(empty($academicSession)){
            $globalData = $request->input('global_data');
            $admissionSession = $globalData->sessionSetting['admission_session'];
            $academicSession = $globalData->sessionSetting['academic_session'];
            $applicationSession = $globalData->sessionSetting['application_session'];
        }

        $lecturerDetails = CourseManagement::with('staff')->where('course_id', $id)->where('academic_session', $academicSession)->first(); 
        $registrations = CourseRegistration::where('course_id', $id)->where('academic_session', $academicSession)->get();
        $courseLectures = CourseLecture::with('lectureAttendance')->where('course_id', $id)->where('academic_session', $academicSession)->get();
        $course = Course::find($id);

        return view('staff.courseDetail', [
            'registrations' => $registrations,
            'lecturerDetails' => $lecturerDetails,
            'courseLectures' => $courseLectures,
            'course' => $course,
            'academicSession' => $academicSession,
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
        $course = Course::with('level', 'registrations', 'registrations.student', 'registrations.student.applicant', 'registrations.student.programme')->where('id', $request->course_id)->first();
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
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];
        $resultProcessStatus = $globalData->examSetting['result_processing_status'];
        $testProcessStatus = $globalData->examSetting['test_processing_status'];

        $uploadType = $request->type;

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

        $validator = Validator::make($request->all(), [
            'result' => 'required|file',
            'course_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $courseManagement = CourseManagement::where([
            'course_id' => $request->course_id,
            'staff_id' => $staffId,
            'academic_session' => $academicSession
        ])->first();

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

    
        $file = $request->file('result');
        $processResult = Result::processResult($file, $courseId, $uploadType, $globalData);

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
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $validator = Validator::make($request->all(), [
            'topic' => 'required',
            'duration' => 'required',
            'date' => 'required'
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
            'academic_session' => $academicSession
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
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

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

        $courseId = $request->course_id;
        $lectureId = $request->lecture_id;

    
        $file = $request->file('attendance');
        $processAttendance = StudentAttendance::processLectureAttendance($file, $lectureId, $globalData);

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
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

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

        $createLectureData = ([
            'topic' => $request->topic,
            'duration' => $request->duration,
            'date' => $request->date,
            'slug' => $slug,
        ]);

        if($lecture->update($createLectureData)){
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

        $globalData = $request->input('global_data');
        $resultProcessStatus = $globalData->examSetting['result_processing_status'];
        $testProcessStatus = $globalData->examSetting['test_processing_status'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $uploadType = $request->type;

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

        $validator = Validator::make($request->all(), [
            'course_id' => 'required',
            'matric_number' => 'required',
        ]);

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
        if(!empty($request->passcode) && $request->passcode != $courseManagementCourseCode){
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

        $studentRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'course_id' => $courseId,
            'academic_session' => $academicSession
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
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        $applicationSession = $globalData->sessionSetting['application_session'];

        $validator = Validator::make($request->all(), [
            'result' => 'required|file',
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
        $processResult = Result::processVocationResult($file, $globalData);

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
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

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

        $levelAdviser = LevelAdviser::where('programme_id', $request->programme_id)
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

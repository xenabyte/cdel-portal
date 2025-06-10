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
use App\Models\Guardian;
use App\Models\FinalClearance;
use App\Models\ProgrammeCategory;

use App\Mail\NotificationMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudentController extends Controller
{
    //

    public function resendGuardianOnboarding(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$student = Student::with('applicant')->where('id', $request->student_id)->first()){
            alert()->error('Oops!', 'Student record not found')->persistent('Close');
            return redirect()->back();
        }

        if(!$applicant = Applicant::where('id', $student->user_id)->first()){
            alert()->error('Oops!', 'Applicant record not found')->persistent('Close');
            return redirect()->back();
        }

        $guardianId = $applicant->guardian_id;
        $guardian = Guardian::find($guardianId);
        if(!$guardian = Guardian::where('id', $guardianId)->first()){
            alert()->error('Oops!', 'Guardian record not found')->persistent('Close');
            return redirect()->back();
        }

        $accessCode = $this->generateAccessCode();
        $guardian->password = Hash::make($accessCode);
        $guardian->passcode = $accessCode;

        if($guardian->save()){
            $sendGuardianEmail =  $this->sendGuardianOnboardingMail($student);

            alert()->success('Success', 'Guardian onboarding email sent')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function generateStudentReferrerCode() {
        
        $students = Student::all();

        foreach ($students as $student) {
            $student->referral_code = $this->generateReferralCode(10);
            $student->save();
        }

        return $students;
    }

    public function graduatingStudents($programmeCategory) {

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', $programmeCategory)->first();
        $programmeCategoryId = $programmeCategory->id;

        $studentsQuery = Student::with(['programme', 'programme.department', 'programme.department.faculty', 'registeredCourses', 'academicLevel'])
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
            ->where('programme_category_id', $programmeCategoryId)
            ->whereHas('programme', function ($query) {
                $query->whereRaw('students.level_id >= programmes.duration');
            });

        $students = $studentsQuery->get();

        $classifiedStudents = [];

        foreach ($students as $student) {
            $facultyName = $student->programme->department->faculty->name;
            $departmentName = $student->programme->department->name;
            $programName = $student->programme->name;
            $level = $student->academicLevel->level;

            if (!isset($classifiedStudents[$facultyName])) {
                $classifiedStudents[$facultyName] = [];
            }

            if (!isset($classifiedStudents[$facultyName][$departmentName])) {
                $classifiedStudents[$facultyName][$departmentName] = [];
            }

            if (!isset($classifiedStudents[$facultyName][$departmentName][$programName])) {
                $classifiedStudents[$facultyName][$departmentName][$programName] = [];
            }

            if (!isset($classifiedStudents[$facultyName][$departmentName][$programName][$level])) {
                $classifiedStudents[$facultyName][$departmentName][$programName][$level] = [];
            }

            $classifiedStudents[$facultyName][$departmentName][$programName][$level][] = $student;
        }


        return view('admin.graduatingStudents', [
            'classifiedStudents' => $classifiedStudents,
            'programmeCategory' => $programmeCategory,

        ]);
    }

    public function graduateStudents(Request $request){
        $selectedStudents = $request->input('selected_students');

        if (empty($selectedStudents)) {
            alert()->error('Oops!', 'No selected students')->persistent('Close');
            return redirect()->back();
        }

        foreach ($selectedStudents as $studentId) {
            $student = Student::find($studentId);

            if ($student) {
                $student->update([
                    'is_passed_out' => true,  // Assuming you have a 'graduated' field in your students table
                    'graduation_date' => Carbon::now(),
                    'graduation_session' => $student->academic_session
                ]);
            }
        }

        alert()->success('Success', 'Students status set to graduated.')->persistent('Close');
        return redirect()->back();
    }

    public function manageClearanceApplication(Request $request){
        $student = Student::find($request->student_id);

        $status = $request->status == 1? $request->status:null;

        $student->clearance_status = $status;

        if($student->save()){
            alert()->success('Good Job', 'Application reviewed')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function alumni($academicSession = null) {
        $alumni = Student::where('is_passed_out', true)->get();

        $academicSessions = Student::where('is_passed_out', true)->select('academic_session')->distinct()->get();
    
        if ($academicSession) {
            $alumni = Student::where('is_passed_out', true)
                            ->where('academic_session', $academicSession)
                            ->get();
        }
    
        return view('admin.alumni', [
            'alumni' => $alumni,
            'academicSessions' => $academicSessions,
        ]);
    }

    public function refreshPasscode(Request $request){
        $student = Student::find($request->student_id);

        $passcode = $student->passcode;

        $student->password = bcrypt($passcode);

        if($student->save()){
            alert()->success('Good Job', 'Passcode refreshed successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    
    public function manageFinalYearStudentClearance(Request $request){
    
        $validator = Validator::make($request->all(), [
            'clearance_id' => 'required',
        ]);
    
        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
    
        $studentFinalClearance = FinalClearance::find($request->clearance_id);
        if(!$studentFinalClearance){
            alert()->error('Error', 'Student Clearance not found')->persistent('Close');
            return redirect()->back();
        }
        
        $studentFinalClearance->status = 'approved';
    
        if($studentFinalClearance->save()){
            alert()->success('Success', 'Clearance updated successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error', 'Failed to update clearance status')->persistent('Close');
        return redirect()->back();
    
    }
}

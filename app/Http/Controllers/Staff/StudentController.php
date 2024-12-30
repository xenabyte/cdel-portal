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
    public function graduatingStudents($programmeCategory) {

        $programmeCategory = ProgrammeCategory::where('category', $programmeCategory)->first();
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


        return view('staff.graduatingStudents', [
            'classifiedStudents' => $classifiedStudents,
            'programmeCategory' => $programmeCategory,
        ]);
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
    
        return view('staff.alumni', [
            'alumni' => $alumni,
            'academicSessions' => $academicSessions,
        ]);
    }

    public function studentFinalClearance(){
        $staff = Auth::guard('staff')->user();
        $staffId = $staff->id;

        $students = FinalClearance::with('student', 'librarian', 'hod', 'dean', 'bursary', 'registrar', 'student_care_dean')
        ->whereNull('status')
        ->where(function ($query) use ($staffId) {
            $query->where('hod_id', $staffId)
                ->orWhere('dean_id', $staffId)
                ->orWhere('student_care_dean_id', $staffId)
                ->orWhere('registrar_id', $staffId)
                ->orWhere('bursary_id', $staffId)
                ->orWhere('library_id', $staffId)
                ->orWhere('ppd_id', $staffId);
        })
        ->get();



        return view('staff.studentFinalClearance', [
            'students' => $students,
        ]);
    }

    public function manageFinalYearStudentClearance(Request $request){
        $staff = Auth::guard('staff')->user();
    
        $validator = Validator::make($request->all(), [
            'clearance_id' => 'required',
            'status' => 'required',
            'comment' => 'required',
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
    
        if ($staff->id == $studentFinalClearance->hod_id) {
            $role = 'hod';
        } elseif ($staff->id == $studentFinalClearance->dean_id) {
            $role = 'dean';
        } elseif ($staff->id == $studentFinalClearance->student_care_dean_id) {
            $role = 'student_care_dean';
        } elseif ($staff->id == $studentFinalClearance->registrar_id) {
            $role = 'registrar';
        } elseif ($staff->id == $studentFinalClearance->bursary_id) {
            $role = 'bursary';
        } elseif ($staff->id == $studentFinalClearance->library_id) {
            $role = 'library';
        } elseif ($staff->id == $studentFinalClearance->ppd_id) {
            $role = 'ppd';
        } else {
            alert()->error('Error', 'You do not have permission to manage this clearance')->persistent('Close');
            return redirect()->back();
        }
    
        switch ($role) {
            case 'hod':
                $studentFinalClearance->hod_status = $request->status;
                $studentFinalClearance->hod_comment = $request->comment;
                $studentFinalClearance->hod_approval_date = now();
                break;
            case 'dean':
                $studentFinalClearance->dean_status = $request->status;
                $studentFinalClearance->dean_comment = $request->comment;
                $studentFinalClearance->dean_approval_date = now();
                break;
            case 'student_care_dean':
                $studentFinalClearance->student_care_dean_status = $request->status;
                $studentFinalClearance->student_care_dean_comment = $request->comment;
                $studentFinalClearance->student_care_dean_approval_date = now();
                break;
            case 'registrar':
                $studentFinalClearance->registrar_status = $request->status;
                $studentFinalClearance->registrar_comment = $request->comment;
                $studentFinalClearance->registrar_approval_date = now();
                break;
            case 'bursary':
                $studentFinalClearance->bursary_status = $request->status;
                $studentFinalClearance->bursary_comment = $request->comment;
                $studentFinalClearance->bursary_approval_date = now();
                break;
            case 'library':
                $studentFinalClearance->library_status = $request->status;
                $studentFinalClearance->library_comment = $request->comment;
                $studentFinalClearance->library_approval_date = now();
                break;
            case 'ppd':
                $studentFinalClearance->ppd_status = $request->status;
                $studentFinalClearance->ppd_comment = $request->comment;
                $studentFinalClearance->ppd_approval_date = now();
                break;
        }
        $studentFinalClearance->save();

        $studentFinalClearance->refresh();

        // Check if all roles have approved the clearance
        if (
            $studentFinalClearance->hod_status === 'approved' &&
            $studentFinalClearance->dean_status === 'approved' &&
            $studentFinalClearance->student_care_dean_status === 'approved' &&
            $studentFinalClearance->registrar_status === 'approved' &&
            $studentFinalClearance->bursary_status === 'approved' &&
            $studentFinalClearance->library_status === 'approved' &&
            $studentFinalClearance->ppd_status === 'approved'
        ) {
            // All roles have approved, set the clearance status to approved
            $studentFinalClearance->status = 'approved';
        }
    
        if($studentFinalClearance->save()){
            alert()->success('Success', 'Clearance updated successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error', 'Failed to update clearance status')->persistent('Close');
        return redirect()->back();
    
    }
    
}

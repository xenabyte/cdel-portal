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

use App\Mail\NotificationMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudentController extends Controller
{
    //
    public function graduatingStudents() {

        $studentsQuery = Student::with(['programme', 'programme.department', 'programme.department.faculty', 'registeredCourses', 'academicLevel'])
            ->where('is_active', true)
            ->where('is_passed_out', false)
            ->where('is_rusticated', false)
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
}

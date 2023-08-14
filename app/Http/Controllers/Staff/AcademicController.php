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

use App\Models\AcademicLevel;
use App\Models\ApprovalLevel;
use App\Models\Session;
use App\Models\SessionSetting;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\CourseRegistrationSetting;
use App\Models\ExaminationSetting;
use App\Models\Programme;
use App\Models\Student;
use App\Models\StudentDemotion;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class AcademicController extends Controller
{
    //

    public function faculties(Request $request){
        $faculties = Faculty::with('departments')->get();

        return view('staff.faculties', [
            'faculties' => $faculties
        ]);
    }

    public function faculty(Request $request, $slug){
        $faculty = Faculty::with('departments', 'departments.programmes', 'students', 'students.programme', 'students.programme.department')
        ->where('slug', $slug)->first();

        return view('staff.faculty', [
            'faculty' => $faculty
        ]);
    }

    public function departments(Request $request){
        $departments = Department::with('programmes')->get();

        return view('staff.departments', [
            'departments' => $departments
        ]);
    }

    public function department(Request $request, $slug){
        $department = Department::with('programmes', 'programmes.students')->where('slug', $slug)->first();

        return view('staff.department', [
            'department' => $department
        ]);
    }
    
}

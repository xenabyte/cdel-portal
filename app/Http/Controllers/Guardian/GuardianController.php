<?php

namespace App\Http\Controllers\Guardian;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Guardian;
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

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class GuardianController extends Controller
{
    //

    public function index(Request $request){

        return view('guardian.home');
    }

    public function students(Request $request){

        return view('guardian.students');
    }

    public function profile(Request $request){

        return view('guardian.profile');
    }

    
    public function studentProfile(Request $request, $slug){
        $student = Student::with('applicant', 'applicant.utmes', 'programme', 'transactions')->where('slug', $slug)->first();
        $academicLevels = AcademicLevel::orderBy('id', 'desc')->get();
        $sessions = Session::orderBy('id', 'desc')->get();

        return view('guardian.studentProfile', [
            'student' => $student,
            'academicLevels' => $academicLevels,
            'sessions' => $sessions
        ]);
    }

}

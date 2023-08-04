<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Course;
use App\Models\CourseRegistrationSetting;
use App\Models\CourseRegistration;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;


class AcademicController extends Controller
{
    //

    public function courseRegistration(Request $request){
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $courses = Course::where('programme_id', $student->programme_id)->where('level_id', $student->level_id)->get();
        $existingRegistration = CourseRegistration::where([
            'student_id' => $studentId,
            'academic_session' => $academicSession
        ])->get();

        //carryover courses
        $carryOverCourses = CourseRegistration::with('course')->where('student_id', $studentId)->where('grade', 'F')->get();

        $courseRegMgt = CourseRegistrationSetting::first();

        return view('student.courseRegistration', [
            'courseRegMgt' => $courseRegMgt,
            'courses' => $courses,
            'existingRegistration' => $existingRegistration
        ]);
    }

    public function registerCourses(Request $request)
    {
        $selectedCourses = $request->input('selected_courses', []);
        if(empty($selectedCourses)){
            alert()->info('Kindly select your courses', '')->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];
        
        try {
            foreach ($selectedCourses as $courseId) {
                $course = Course::findOrFail($courseId);

                // Check if the student is already registered for this course
                $existingRegistration = CourseRegistration::where([
                    'student_id' => $studentId,
                    'course_id' => $courseId,
                    'academic_session' => $academicSession
                ])->first();

                if (!$existingRegistration) {
                    $courseReg = CourseRegistration::create([
                        'student_id' => $studentId,
                        'course_id' => $courseId,
                        'academic_session' => $academicSession
                    ]);
                }
            }
    
        
            alert()->success('Changes Saved', 'Course registration saved successfully')->persistent('Close');
            return redirect()->back();

        } catch (\Exception $e) {
            alert()->error('Oops!', 'Something went wrong')->persistent('Close');
            return redirect()->back();
        }
    }

    public function printCourseReg(Request $request)
    {
        $student = Auth::guard('student')->user();
        $studentId = $student->id;
        $globalData = $request->input('global_data');
        $admissionSession = $globalData->sessionSetting['admission_session'];
        $academicSession = $globalData->sessionSetting['academic_session'];

        $pdf = new Pdf();
        $courseReg = $pdf->generateCourseRegistration($studentId, $academicSession);

        return;
    }
}

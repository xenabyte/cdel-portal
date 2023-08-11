<?php

namespace App\Http\Controllers;

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
use App\Models\StudentCourseRegistration;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\StudentExamCard;
use App\Models\Student;


use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;

class HomeController extends Controller
{
    

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getExamDocket(Request $request, $slug){

        $student = Student::with('applicant', 'academicLevel', 'faculty', 'department', 'programme')->where('slug', $slug)->first();
        $studentId = $student->id;
        $levelId = $student->level_id;
        $transactions = Transaction::where('student_id', $studentId)->orderBy('id', 'DESC')->get();

        $schoolPayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_SCHOOL)->where('programme_id', $student->programme_id)->where('level_id', $levelId)->first();
        if(!$schoolPayment){
            alert()->info('Programme info missing, contact administrator', '')->persistent('Close');
            return redirect()->back();
        }
        $schoolPaymentId = $schoolPayment->id;
        $schoolAmount = $schoolPayment->structures->sum('amount');
        $schoolPaymentTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $schoolPaymentId)->where('session', $student->academic_session)->where('status', 1)->get();

        $passTuitionPayment = false;
        $fullTuitionPayment = false;
        $passEightyTuition = false;
        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.4){
            $passTuitionPayment = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.7){
            $passEightyTuition = true;
        }

        if($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') >= $schoolAmount){
            $passEightyTuition = true;
            $fullTuitionPayment = true;
        }
        
        return view('studentExamDocket', [
            'student' => $student,
            'transactions' => $schoolPaymentTransaction,
            'payment' => $schoolPayment,
            'passTuition' => $passTuitionPayment,
            'fullTuitionPayment' => $fullTuitionPayment,
            'passEightyTuition' => $passEightyTuition
        ]);
    }
}

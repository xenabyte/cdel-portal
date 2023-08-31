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

use App\Models\Programme;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Student;
use App\Models\SessionSetting;

use App\Mail\ApplicationPayment;
use App\Mail\StudentActivated;


use App\Libraries\Google\Google;

use Paystack;
use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class PaymentController extends Controller
{
    //

    protected $admissionSettings;
    protected $programmes;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->programmes = Programme::get();
    }

    //verify payment with card
    public function verifyPayment()
    {
        Log::info("**********************Verifying Payment**********************");
        try{
            $paymentDetails = Paystack::getPaymentData();
            $paymentId = $paymentDetails['data']['metadata']['payment_id'];
            $studentId = $paymentDetails['data']['metadata']['student_id'];
            $redirectPath = $paymentDetails['data']['metadata']['redirect_path'];
            $payment = Payment::where('id', $paymentId)->first();
            $paymentType = $payment->type;
            $student = Student::with('applicant', 'programme')->where('id', $studentId)->first();
            

            if($paymentDetails['status'] == true){
                if($this->processPayment($paymentDetails)){
                    alert()->success('Good Job', 'Payment successful')->persistent('Close');
                    if($paymentType == Payment::PAYMENT_TYPE_GENERAl_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                        return view($redirectPath, [
                            'programmes' => $this->programmes,
                            'payment' => $payment
                        ]);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                        return redirect($redirectPath);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_SCHOOL){
                        $this->generateMatricAndEmail($student);
                        return redirect($redirectPath);
                    }else{
                        return redirect($redirectPath);
                    }
                }else{
                    alert()->info('oops!!!', 'Something happpened, contact administrator')->persistent('Close');
                    if($paymentType == Payment::PAYMENT_TYPE_GENERAl_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                        return view($redirectPath, [
                            'programmes' => $this->programmes,
                            'payment' => $payment
                        ]);
                    }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                        return redirect($redirectPath);
                    }else{
                        return redirect($redirectPath);
                    }
                }

            }

            alert()->error('Error', 'Payment not successful')->persistent('Close');
            if($paymentType == Payment::PAYMENT_TYPE_GENERAl_APPLICATION || $paymentType == Payment::PAYMENT_TYPE_INTER_TRANSFER_APPLICATION){
                return view($redirectPath, [
                    'programmes' => $this->programmes,
                    'payment' => $payment
                ]);
            }elseif($paymentType == Payment::PAYMENT_TYPE_ACCEPTANCE){
                return redirect($redirectPath);
            }else{
                return redirect($redirectPath);
            }
            

        }catch(\Exception $e) {
            Log::error($e);
        }
    }

    public function paystackWebhook (Request $request) {   
        try {
            $webhookData = $request->all();
            log::info(json_encode($webhookData));
            $event = $webhookData['event'];
            sleep(300);
            return false;
            if($event == "charge.success"){
                return $this->processPayment($webhookData);
            }
          
        }
        catch (ValidationException $e) {
          Log::info(json_encode($e));
        }
    }

    private function generateMatricAndEmail($student){
        if(!$student->is_active && empty($student->matric_number)){
            $sessionSetting = SessionSetting::first();
            $admissionSession = $sessionSetting->admission_session;

            $programme = Programme::with('students', 'department', 'department.faculty')->where('id', $student->programme_id)->first();
            $codeNumber = $programme->code_number;
            $deptCode = $programme->department->code;
            $facultyCode = $programme->department->faculty->code;
            $programmeCode = $programme->code;
            $code = $deptCode.$programmeCode;

            $accessCode = $student->applicant->passcode;
            $studentPreviousEmail = $student->email;

            $name = $student->applicant->lastname.' '.$student->applicant->othernames;
            $nameParts = explode(' ', $student->applicant->othernames);
            $firstName = $nameParts[0];
            $studentEmail = strtolower($student->applicant->lastname.'.'.$firstName.'@st.tau.edu.ng');

            $newMatric = empty($programme->matric_last_number)? ($programme->students->count() + 20) + 1 : $programme->matric_last_number + 1;
            $matricNumber = substr($admissionSession, 2, 2).'/'.$facultyCode.$code.sprintf("%03d", $newMatric);

            $google = new Google();
            $createStudentEmail = $google->createUser($studentEmail, $student->applicant->othernames, $student->applicant->lastname, $accessCode);

            $student->email = $studentEmail;
            $student->matric_number = $matricNumber;
            $student->is_active = true;
            $student->save();

            $programme->matric_last_number = $newMatric;
            $programme->save();

            
            Mail::to($studentPreviousEmail)->send(new StudentActivated($student));

            return true;
        }
    }

}

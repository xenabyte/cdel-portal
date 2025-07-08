<?php

namespace App\Http\Controllers;

use Alert;
use App\Http\Requests;
use App\Libraries\Bandwidth\Bandwidth;
use App\Libraries\Google\Google;
use App\Libraries\Pdf\Pdf;
use App\Mail\ApplicationMail;
use App\Mail\ApplicationPayment;
use App\Mail\BankDetailsMail;
use App\Mail\GuardianOnboardingMail;
use App\Mail\StudentActivated;
use App\Mail\TransactionMail;
use App\Models\AcademicLevel;
use App\Models\AcademicSessionSetting;
use App\Models\Allocation;
use App\Models\Course;
use App\Models\CourseLecture;
use App\Models\CourseRegistration;
use App\Models\Department;
use App\Models\Faculty;
use App\Models\Guardian;
use App\Models\Hostel;
use App\Models\LectureAttendance;
use App\Models\Partner;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Models\Plan;
use App\Models\Programme;
use App\Models\ProgrammeCategory;
use App\Models\ProgrammeChangeRequest;
use App\Models\Room;
use App\Models\RoomBedSpace;
use App\Models\RoomType;
use App\Models\Session;
use App\Models\SessionSetting;
use App\Models\ShortUrl;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentSuspension;
use App\Models\SummerCourseRegistration;
use App\Models\TestApplicant;
use App\Models\Transaction;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Log;
use Mail;
use Paystack;
use SweetAlert;

class Controller extends BaseController
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    public function dataResponse($message, $data = null, $status = "success", $statusCode = null)
    {
        if (!$statusCode) {
            if ($status == "error") {
                $statusCode = Response::HTTP_BAD_REQUEST;
            } else {
                $statusCode = Response::HTTP_OK;
            }
        }

        return new JsonResponse([
            'status' => $status,
            'message' => $message,
            'data' => $data,
        ], $statusCode);
    }

    public function shortURL($url)
    {
        $code = $this->generateShortUrlCode();

        ShortUrl::create([
            'code' => $code,
            'url' => $url
        ]);

        return env('APP_URL')."/s/".$code;
    }

    public function processPaystackPayment($paymentDetails)
    {

        log::info("Processing paystack payment:" . json_encode($paymentDetails));
        //get active editions
        $email = $paymentDetails['data']['metadata']['email'];
        $applicationId = !empty($paymentDetails['data']['metadata']['application_id']) ? $paymentDetails['data']['metadata']['application_id'] : null;
        $studentId = !($paymentDetails['data']['metadata']['student_id']) ? $paymentDetails['data']['metadata']['student_id'] : null;
        $paymentId = $paymentDetails['data']['metadata']['payment_id'];
        $paymentGateway = $paymentDetails['data']['metadata']['payment_gateway'];
        $amount = $paymentDetails['data']['metadata']['amount'];
        $txRef = $paymentDetails['data']['metadata']['reference'];
        $planId = !empty($paymentDetails['data']['metadata']['plan_id']) ? $paymentDetails['data']['metadata']['plan_id'] : null;
        $reference = $paymentDetails['data']['reference'];
        $session = $paymentDetails['data']['metadata']['academic_session'];
        $suspensionId = !empty($paymentDetails['data']['metadata']['suspension_id']) ? $paymentDetails['data']['metadata']['suspension_id'] : null;


        if (!empty($txRef)) {
            if ($existTx = Transaction::where('reference', $txRef)->where('status', null)->first()) {
                $existTx->reference = $reference;
                $existTx->status = 1;
                $existTx->payment_method = $paymentGateway;
                $existTx->save();

                if (!empty($suspensionId)) {
                    StudentSuspension::where('id', $suspensionId)->update(['transaction_id' => $existTx->id]);
                }

                return true;
            }
        }

        //check if payment have been added
        if (Transaction::where('reference', $reference)->where('status', 1)->first()) {
            return true;
        }

        $payment = Payment::with('programme')->where('id', $paymentId)->first();

        //Create new transaction
        $transaction = Transaction::create([
             'user_id' => !empty($applicationId) ? $applicationId : null,
             'student_id' => !empty($studentId) ? $studentId : null,
             'payment_id' => $paymentId,
             'amount_payed' => $amount,
             'payment_method' => $paymentGateway,
             'reference' => $reference,
             'session' => $session,
             'status' => 1,
             'plan_id' => !empty($planId) ? $planId : null,
         ]);

        if (!empty($suspensionId)) {
            StudentSuspension::where('id', $suspensionId)->update(['transaction_id' => $transaction->id]);
        }

        return true;
    }

    public function processRavePayment($paymentDetails)
    {

        log::info("Processing flutterwave payment:" . json_encode($paymentDetails));
        //get active editions
        $email = $paymentDetails['data']['meta']['email'];
        $applicationId = !empty($paymentDetails['data']['meta']['application_id']) ? $paymentDetails['data']['meta']['application_id'] : null;
        $studentId = !empty($paymentDetails['data']['meta']['student_id']) ? $paymentDetails['data']['meta']['student_id'] : null;
        $planId = !empty($paymentDetails['data']['meta']['plan_id']) ? $paymentDetails['data']['meta']['plan_id'] : null;
        $suspensionId = !empty($paymentDetails['data']['meta']['suspension_id']) ? $paymentDetails['data']['meta']['suspension_id'] : null;
        $paymentId = $paymentDetails['data']['meta']['payment_id'];
        $paymentGateway = $paymentDetails['data']['meta']['payment_gateway'];
        $amount = $paymentDetails['data']['meta']['amount'];
        $txRef = $paymentDetails['data']['meta']['reference'];
        $reference = $paymentDetails['data']['meta']['reference'];
        $session = $paymentDetails['data']['meta']['academic_session'];

        $fwTxId = isset($_GET['transaction_id']) ? $_GET['transaction_id'] : null;
        $payment = Payment::with('programme')->where('id', $paymentId)->first();
        $redirectUrl = env('FLW_REDIRECT_URL').'?status=successful&tx_ref='.$reference.'&transaction_id='.$fwTxId;


        if (!empty($txRef)) {
            if ($existTx = Transaction::where('reference', $txRef)->where('status', null)->first()) {
                $existTx->reference = $reference;
                $existTx->status = 1;
                $existTx->payment_method = $paymentGateway;
                $existTx->redirect_url = $redirectUrl;
                $existTx->save();

                if (!empty($suspensionId)) {
                    StudentSuspension::where('id', $suspensionId)->update(['transaction_id' => $existTx->id]);
                }

                return true;
            }
        }

        //check if payment have been added
        if (Transaction::where('reference', $reference)->where('status', 1)->first()) {
            return true;
        }

        //Create new transaction
        $transaction = Transaction::create([
            'user_id' => !empty($applicationId) ? $applicationId : null,
            'student_id' => !empty($studentId) ? $studentId : null,
            'payment_id' => $paymentId,
            'amount_payed' => $amount,
            'payment_method' => $paymentGateway,
            'reference' => $reference,
            'session' => $session,
            'redirect_url' => $redirectUrl,
            'plan_id' => !empty($planId) ? $planId : null,
            'status' => 1
        ]);

        if (!empty($suspensionId)) {
            StudentSuspension::where('id', $suspensionId)->update(['transaction_id' => $transaction->id]);
        }

        return true;
    }

    public function processUpperlinkPayment($paymentDetails)
    {
        log::info("Processing upperlink payment:" . json_encode($paymentDetails));

        $data = $paymentDetails['meta'];
        $paymentData = json_decode($data, true);
        $merchantId = $paymentDetails['merchantId'];


        //get active editions
        $applicationId = !empty($paymentData['application_id']) ? $paymentData['application_id'] : null;
        $studentId = !empty($paymentData['student_id']) ? $paymentData['student_id'] : null;
        $planId = !empty($paymentData['plan_id']) ? $paymentData['plan_id'] : null;
        $suspensionId = !empty($paymentData['suspension_id']) ? $paymentData['suspension_id'] : null;
        $paymentId = $paymentData['payment_id'];
        $paymentGateway = $paymentData['payment_gateway'];
        $amount = $paymentDetails['amount'];
        $txRef = $paymentData['reference'];
        $reference = $paymentData['reference'];
        $session = $paymentData['academic_session'];

        $redirectUrl = env('UPPERLINK_REDIRECT_URL').'?reference='.$reference.'&merchantId='.$merchantId;


        if (!empty($txRef)) {
            if ($existTx = Transaction::where('reference', $txRef)->where('status', null)->first()) {
                $existTx->reference = $reference;
                $existTx->status = 1;
                $existTx->payment_method = $paymentGateway;
                $existTx->redirect_url = $redirectUrl;
                $existTx->save();

                if (!empty($suspensionId)) {
                    StudentSuspension::where('id', $suspensionId)->update(['transaction_id' => $existTx->id]);
                }

                return true;
            }
        }

        //check if payment have been added
        if (Transaction::where('reference', $reference)->where('status', 1)->first()) {
            return true;
        }

        //Create new transaction
        $transaction = Transaction::create([
            'user_id' => !empty($applicationId) ? $applicationId : null,
            'student_id' => !empty($studentId) ? $studentId : null,
            'payment_id' => $paymentId,
            'amount_payed' => $amount * 100,
            'payment_method' => $paymentGateway,
            'reference' => $reference,
            'session' => $session,
            'redirect_url' => $redirectUrl,
            'plan_id' => !empty($planId) ? $planId : null,
            'status' => 1
        ]);

        if (!empty($suspensionId)) {
            StudentSuspension::where('id', $suspensionId)->update(['transaction_id' => $transaction->id]);
        }

        return true;

    }

    public function generateAccessCode()
    {
        $applicationAccessCode = "";
        $current = $this->generateRandomString();
        $isExist = User::where('passcode', $current)->get();
        if (!($isExist->count() > 0)) {
            $applicationAccessCode = $current;
            return $applicationAccessCode;
        } else {
            return $this->generateAccessCode();
        }
    }

    public function generateShortUrlCode()
    {
        $shortUrlCode = "";
        $current = $this->generateRandomString();
        $isExist = ShortUrl::where('code', $current)->get();
        if (!($isExist->count() > 0)) {
            $shortUrlCode = $current;
            return $shortUrlCode;
        } else {
            return $this->generateShortUrlCode();
        }
    }

    public function generatePaymentReference($paymentType)
    {
        $prefix = $this->getInitials($paymentType);
        return $prefix . '-' . $this->generateRandomString(25);
    }

    private function getInitials($phrase)
    {
        return strtoupper(collect(explode(' ', $phrase))->map(function ($word) {
            return substr($word, 0, 1);
        })->implode(''));
    }

    public function getPaystackAmount($amount)
    {
        $paystackAmount =  (((1.5 / 100) * $amount) + 10500);

        if (($paystackAmount) > 200000) {
            $paymentAmount = $amount + 200000 + 5000;
        } elseif ($amount < 250000) {
            $paymentAmount = $amount + $paystackAmount + 5000;
        } else {
            $paymentAmount = $amount + $paystackAmount + 5000;
        }

        $paymentAmount = $amount + 50000;

        return $paymentAmount;
    }

    public function getRaveAmount($amount)
    {
        $paystackAmount =  (((1.4 / 100) * $amount) + 5000);

        // $paymentAmount = $amount + $paystackAmount + 5000;
        $paymentAmount = $amount + 5000;

        return $paymentAmount / 100;
    }

    public function getUpperlinkAmount($amount)
    {
        $upperLinkAmount =  (((1.5 / 100) * $amount) + 5000);

        if (($upperLinkAmount) > 200000) {
            $paymentAmount = $amount + 200000 + 5000;
        } else {
            $paymentAmount = $amount + $upperLinkAmount + 5000;
        }

        return $paymentAmount;
    }

    public function getMonnifyAmount($amount)
    {
        $monnifyAmount =  (((1.5 / 100) * $amount) + 5000);

        if (($monnifyAmount) > 200000) {
            $paymentAmount = $amount + 200000 + 5000;
        } else {
            $paymentAmount = $amount + $monnifyAmount + 5000;
        }

        return $paymentAmount;
    }

    public function generateReferralCode($length = 8)
    {
        $referralCode = "";
        $current = $this->generateRandomString($length);
        $isExist = Staff::where('referral_code', $current)->get();
        $isExistPartner = Partner::where('referral_code', $current)->get();
        $isExistStudent = Student::where('referral_code', $current)->get();
        if (!($isExist->count() > 0) && !($isExistPartner->count() > 0) && !($isExistStudent->count() > 0)) {
            $referralCode = $current;
            return $referralCode;
        } else {
            return $this->generateReferralCode($length);
        }
    }

    public function getPartnerId($referralCode)
    {
        // $isExistStaff = Staff::where('referral_code', $referralCode)->first();
        // if($isExistStaff){
        //     return $isExistStaff->id;
        // }

        // $isExistStudent = Student::where('referral_code', $referralCode)->first();
        // if($isExistStudent){
        //     return $isExistStudent->id;
        // }

        $isExistPartner = Partner::where('referral_code', $referralCode)->first();
        if ($isExistPartner) {
            return $isExistPartner->id;
        }

        return null;
    }

    public function getSingleApplicant($studentIdCode, $path)
    {
        $student = User::with('programme', 'programme.department', 'programme.department.faculty', 'transactions', 'student')->where('application_number', $studentIdCode)->first();
        if (!$student) {
            alert()->info('Record not found', '')->persistent('Close');
            return redirect()->back();
        }

        $studentId = $student->id;

        $levels = AcademicLevel::get();
        $programmes = Programme::get();
        $departments = Department::get();
        $faculties = Faculty::get();
        $sessions = Session::orderBy('id', 'DESC')->get();
        $paymentTypes = PaymentType::get();


        // Initialize $studentUserId and $studentStudentId
        $studentUserId = $student->user_id;
        $studentStudentId = $student->student ? $student->student->id : null;

        // Modify the transaction query based on the presence of $student->student
        $transactions = Transaction::where('user_id', $studentUserId)
            ->where(function ($query) use ($studentStudentId) {
                if ($studentStudentId) {
                    $query->where('student_id', $studentStudentId);
                }
            })
            ->where('payment_id', '!=', 0)
            ->orderBy('id', 'DESC')
            ->get();

        $transactions = Transaction::where('user_id', $student->id)->where('payment_id', '!=', 0)->orderBy('id', 'DESC')->get();


        $filteredTransactions = [];
        foreach ($transactions as $transaction) {
            $paymentType = !empty($transaction->paymentType) ? $transaction->paymentType->type : Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
            $session = $transaction->session;
            $totalPaid = $transaction->amount_payed;
            $paymentId = $transaction->payment_id;

            if (isset($filteredTransactions[$paymentType][$session])) {
                $filteredTransactions[$paymentType][$session]['totalPaid'] += $totalPaid;
            } else {
                $filteredTransactions[$paymentType][$session] = [
                    'id' => $paymentId,
                    'paymentType' => $paymentType,
                    'totalPaid' => $totalPaid,
                    'session' => $session,
                ];
            }
        }

        return view($path, [
            'transactions' => $filteredTransactions,
            'applicant' => $student,
            'levels' => $levels,
            'programmes' => $programmes,
            'departments' => $departments,
            'faculties' => $faculties,
            'sessions' => $sessions,
            'paymentTypes' => $paymentTypes
        ]);
    }

    public function getSingleStudent($studentIdCode, $path, $otherData = null)
    {

        $student = Student::with('programme', 'transactions', 'applicant')->where('matric_number', $studentIdCode)->first();
        if (!$student) {
            alert()->info('Record not found', '')->persistent('Close');
            return redirect()->back();
        }
        $studentId = $student->id;
        $levelId = $student->level_id;

        $levels = AcademicLevel::orderBy('id', 'DESC')->get();
        $programmes = Programme::get();
        $departments = Department::get();
        $faculties = Faculty::get();
        $sessions = Session::orderBy('id', 'DESC')->get();
        $paymentTypes = PaymentType::get();

        $transactions = Transaction::with('paymentType')->where('student_id', $studentId)->orderBy('id', 'DESC')->get();

        $filteredTransactions = [];
        foreach ($transactions as $transaction) {
            $paymentType = !empty($transaction->paymentType) ? $transaction->paymentType->type : Payment::PAYMENT_TYPE_WALLET_DEPOSIT;

            $session = $transaction->session;
            $paymentId = $transaction->payment_id;

            $totalPaid = ($transaction->status == 1) ? $transaction->amount_payed : 0;

            $totalAmount = $transaction->amount_payed;

            if (isset($filteredTransactions[$paymentType][$session])) {
                $filteredTransactions[$paymentType][$session]['totalPaid'] += $totalPaid;
                $filteredTransactions[$paymentType][$session]['totalAmount'] += $totalAmount;
            } else {
                $filteredTransactions[$paymentType][$session] = [
                    'id' => $paymentId,
                    'paymentType' => $paymentType,
                    'totalPaid' => $totalPaid,
                    'totalAmount' => $totalAmount,
                    'session' => $session,
                ];
            }
        }


        // foreach ($filteredTransactions as &$paymentType) {
        //     usort($paymentType, 'sortBySession');
        // }


        $schoolPayment = Payment::with('structures')
            ->where('type', Payment::PAYMENT_TYPE_SCHOOL)
            ->where('programme_id', $student->programme_id)
            ->where('programme_category_id', $student->programme_category_id)
            ->where('level_id', $levelId)
            ->where('academic_session', $student->academic_session)
            ->first();

        if (!$schoolPayment) {
            alert()->info('Programme info missing, contact administrator', '')->persistent('Close');
            return redirect()->back();
        }
        $schoolPaymentId = $schoolPayment->id;
        $schoolAmount = $schoolPayment->structures->sum('amount');
        $schoolPaymentTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $schoolPaymentId)->where('session', $student->academic_session)->where('status', 1)->get();

        $passTuitionPayment = false;
        $fullTuitionPayment = false;
        $passEightyTuition = false;
        if ($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.4) {
            $passTuitionPayment = true;
        }

        if ($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.7) {
            $passEightyTuition = true;
        }

        if ($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') >= $schoolAmount) {
            $passEightyTuition = true;
            $fullTuitionPayment = true;
        }

        $registeredCourses = null;

        if (!empty($otherData->levelId) && !empty($otherData->academicSession)) {
            $registeredCourses = CourseRegistration::with('course')->where('student_id', $studentId)->where('level_id', $otherData->levelId)->where('academic_session', $otherData->academicSession)->orderBy('semester', 'ASC')->get();
        }

        return view($path, [
            'path' => $path,
            'transactions' => $filteredTransactions,
            'payment' => $schoolPayment,
            'paymentTypes' => $paymentTypes,
            'passTuition' => $passTuitionPayment,
            'fullTuitionPayment' => $fullTuitionPayment,
            'passEightyTuition' => $passEightyTuition,
            'student' => $student,
            'levels' => $levels,
            'programmes' => $programmes,
            'departments' => $departments,
            'faculties' => $faculties,
            'sessions' => $sessions,
            'allTxs' => $transactions,
            'studentLevelId' => empty($otherData) ? null : $otherData->levelId,
            'studentSession' => empty($otherData) ? null : $otherData->academicSession,
            'registeredCourses' => $registeredCourses,
        ]);

    }

    //generate clean strings
    public function generateRandomString($length = 8)
    {
        $characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function getPreviousAcademicYear($session)
    {
        list($startYear, $endYear) = explode('/', $session);

        $startAcademicYear = Carbon::createFromDate($startYear, 1, 1)->subYear()->format('Y');
        $endAcademicYear = Carbon::createFromDate($endYear, 1, 1)->subYear()->format('Y');

        return $startAcademicYear . '/' . $endAcademicYear;
    }

    public function checkSchoolFees($student, $academicSession, $levelId)
    {
        $studentId = $student->id;
        $applicantId = $student->user_id;
        $applicant = User::find($applicantId);
        $applicationType = $applicant->application_type;
        $programmeCategoryId = $student->programme_category_id;

        $academicSessionSetting = AcademicSessionSetting::where('programme_category_id', $programmeCategoryId)->first();

        $type = Payment::PAYMENT_TYPE_SCHOOL;

        if ($programmeCategoryId == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::UNDERGRADUATE)) {
            if ($applicationType != 'UTME' && ($student->level_id == 2 || $student->level_id == 3)) {
                $type = Payment::PAYMENT_TYPE_SCHOOL_DE;
            }
        }

        $schoolPayment = Payment::with('structures')
            ->where('type', $type)
            ->where('programme_id', $student->programme_id)
            ->where('level_id', $levelId)
            ->where('academic_session', $academicSession)
            ->where('programme_category_id', $programmeCategoryId)
            ->first();

        if (!$schoolPayment) {
            $data = new \stdClass();
            $data->status = 'record_not_found';

            return $data;
        }

        $schoolPaymentId = $schoolPayment->id;
        $schoolAmount = $schoolPayment->structures->sum('amount');
        $schoolPaymentTransaction = Transaction::where('student_id', $studentId)->where('payment_id', $schoolPaymentId)->where('session', $academicSession)->where('status', 1)->get();

        $studentPendingTransactions = Transaction::with('paymentType')->where('student_id', $studentId)->where('session', '!=', $academicSession)->where('payment_id', $schoolPaymentId)->where('status', null)->where('payment_method', null)->get();

        $passTuitionPayment = false;
        $fullTuitionPayment = false;
        $passEightyTuition = false;
        if ($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.35) {
            $passTuitionPayment = true;
        }

        if ($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') > $schoolAmount * 0.65) {
            $passTuitionPayment = true;
            $passEightyTuition = true;
        }

        if ($schoolPaymentTransaction && $schoolPaymentTransaction->sum('amount_payed') >= $schoolAmount) {
            $passTuitionPayment = true;
            $passEightyTuition = true;
            $fullTuitionPayment = true;
        }

        if (strtolower($academicSessionSetting->school_fee_status) == 'stop') {
            $passTuitionPayment = true;
        }

        if ($student->is_passed_out) {
            $passTuitionPayment = true;
            $passEightyTuition = true;
            $fullTuitionPayment = true;
        }

        $data = new \stdClass();
        $data->status = 'success';
        $data->passTuitionPayment = $passTuitionPayment;
        $data->passEightyTuition = $passEightyTuition;
        $data->fullTuitionPayment = $fullTuitionPayment;
        $data->schoolPaymentTransaction = $schoolPaymentTransaction;
        $data->schoolPayment = $schoolPayment;
        $data->studentPendingTransactions = $studentPendingTransactions;

        return $data;
    }

    public function checkAccomondationStatus($student)
    {
        $studentId = $student->id;
        $programmeCategoryId = $student->programme_category_id;


        $academicSessionSetting = AcademicSessionSetting::where('programme_category_id', $programmeCategoryId)->first();
        $academicSession = $academicSessionSetting->academic_session;


        $type = Payment::PAYMENT_TYPE_ACCOMONDATION;

        $accommondationPayment = Payment::with('structures')
            ->where('type', $type)
            ->where('programme_category_id', $student->programme_category_id)
            ->where('academic_session', $academicSession)
            ->first();

        if (!$accommondationPayment) {
            $data = new \stdClass();
            $data->status = 'record_not_found';

            return $data;
        }

        $accommondationPaymentTransactions = Transaction::where('student_id', $studentId)->where('payment_id', $accommondationPayment->id)->where('session', $academicSession)->where('status', 1)->get();


        $data = new \stdClass();
        $data->status = 'success';
        $data->accommondationPayment = $accommondationPayment;
        $data->accommondationPaymentTransactions = $accommondationPaymentTransactions;

        return $data;

    }

    public function generateMatricAndEmail($student)
    {
        $isStudentActive = $student->is_active;
        if (empty($student->matric_number)) {
            $programmeCategoryId = $student->programme_category_id;

            $academicSessionSetting = AcademicSessionSetting::where('programme_category_id', $programmeCategoryId)->first();
            $admissionSession = $academicSessionSetting->admission_session;
            // $programmeCategorySuffix = ;

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
            $studentEmail = strtolower(str_replace(' ', '', $student->applicant->lastname.'.'.$firstName.'@st.tau.edu.ng'));


            $newMatric = empty($programme->matric_last_number) ? ($programme->students->count() + 20) + 1 : $programme->matric_last_number + 1;
            if ($programmeCategoryId == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::UNDERGRADUATE)) {
                $matricNumber = substr($admissionSession, 2, 2).'/'.$facultyCode.$code.sprintf("%03d", $newMatric);
            }

            if ($programmeCategoryId == ProgrammeCategory::getProgrammeCategory(ProgrammeCategory::TOPUP)) {
                $matricNumber = 'TP/'.substr($admissionSession, 2, 2).'/'.$facultyCode.$code.sprintf("%03d", $newMatric);
            }

            $google = new Google();
            $createStudentEmail = $google->createUser($studentEmail, $student->applicant->othernames, $student->applicant->lastname, $accessCode, env('GOOGLE_STUDENT_GROUP'));
            //log::info($createStudentEmail);

            $student->email = $studentEmail;
            $student->matric_number = $matricNumber;
            $student->referral_code = $this->generateReferralCode(10);
            $student->is_active = true;
            $student->save();

            $programme->matric_last_number = $newMatric;
            $programme->save();

            if (empty($student->bandwidth_username)) {
                $createBandwitdth = $this->createBandwidthAccount($student);
                $student = Student::find($student->id);
            }

            if (!$isStudentActive) {
                if (env('SEND_MAIL')) {
                    Mail::to($studentPreviousEmail)->send(new StudentActivated($student));
                    $this->sendGuardianOnboardingMail($student);
                }
            }

            return true;
        }
    }

    public function createBandwidthAccount($student)
    {
        $programme = Programme::with('department', 'department.faculty')->where('id', $student->programme_id)->first();
        $codeNumber = $programme->code_number;
        $deptCode = $programme->department->code;
        $facultyCode = $programme->department->faculty->code;
        $programmeCode = $programme->code;
        $code = $deptCode.$programmeCode;
        $bandwidthAmount = 32212254720;

        $accessCode = $student->applicant->passcode;

        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $nameParts = explode(' ', $student->applicant->othernames);
        $firstName = $nameParts[0];
        $username = strtolower(str_replace(' ', '', $code.'.'.$student->applicant->lastname.'.'.$firstName));

        $userData = new \stdClass();
        $userData->username = $username;
        $userData->password =  $accessCode;
        $userData->firstname = $firstName;
        $userData->lastname = $student->applicant->lastname;
        $userData->phone = $student->applicant->phone_number;
        $userData->address = $student->applicant->address;

        $bandwidth = new Bandwidth();
        $createStudentBandwidthRecord = $bandwidth->createUser($userData);
        $creditStudent = $bandwidth->addToDataBalance($username, $bandwidthAmount);
        $student->bandwidth_username = $username;
        $student->save();
    }

    public function creditBandwidth($transaction)
    {
        $student = Student::find($transaction->student_id);
        $bandwidthUsername = $student->bandwidth_username;

        $bandwidthPlan = Plan::find($transaction->plan_id);
        $bandwidthAmount = $bandwidthPlan->bandwidth + $bandwidthPlan->bonus;

        // Credit bandwidth
        $bandwidth = new Bandwidth();
        if (empty($transaction->is_used)) {
            $creditStudent = $bandwidth->addToDataBalance($bandwidthUsername, $bandwidthAmount);
            if ($creditStudent && isset($creditStudent['status']) && $creditStudent['status'] === 'success') {
                Log::info("********************** credit student bandwidth**********************: ". $bandwidthAmount .' - '.$student);
                $transaction->status = 1;
                $transaction->is_used = 1;
                $transaction->update();

                return true;
            }

            return true;
        }

        return false;
    }

    public function sortBySession($a, $b)
    {
        return strcmp($a['session'], $b['session']);
    }

    public function creditStudentWallet($studentId, $amount)
    {
        $student = Student::find($studentId);

        $studentBalance = $student->amount_balance;
        $studentNewBalance = $studentBalance + $amount;
        $student->amount_balance = $studentNewBalance;

        Log::info("********************** credit student wallet**********************: ". $amount .' - '.$student);
        if ($student->update()) {
            return true;
        }
        return false;
    }

    public function billStudent($transactionData)
    {

        $student = Student::with('applicant')->where('id', $transactionData->student_id)->first();
        $studentId = $student->id;

        if (empty($transactionData->transaction_id)) {
            if ($transaction = Transaction::where('reference', $transactionData->reference)->where('status', null)->first()) {
                $transaction->status = 1;
                $transaction->save();
            } else {
                //Create new transaction
                $transaction = Transaction::create([
                    'student_id' => $transactionData->student_id,
                    'payment_id' => $transactionData->payment_id,
                    'amount_payed' => $transactionData->amount,
                    'payment_method' => $transactionData->payment_gateway,
                    'reference' => $transactionData->reference,
                    'session' => $transactionData->academic_session,
                    'plan_id' => $transactionData->plan_id,
                    'status' => 1
                ]);
            }
        } else {
            $transaction = Transaction::find($transactionData->transaction_id);
        }

        $paymentId = $transactionData->payment_id;
        $paymentType = Payment::PAYMENT_TYPE_WALLET_DEPOSIT;
        if ($paymentId > 0) {
            $payment = Payment::where('id', $paymentId)->first();
            $paymentType = $payment->type;
        }

        $amount = $transactionData->amount;
        $studentBalance = $student->amount_balance;
        $studentNewBalance = $studentBalance - $transactionData->amount;
        $student->amount_balance = $studentNewBalance;
        $student->save();

        if ($student && !empty($transactionData->student_id)) {
            $pdf = new Pdf();
            $invoice = $pdf->generateTransactionInvoice($transactionData->academic_session, $transactionData->student_id, $transactionData->payment_id, 'single');

            $data = new \stdClass();
            $data->lastname = $student->applicant->lastname;
            $data->othernames = $student->applicant->othernames;
            $data->amount = $transactionData->amount;
            $data->invoice = $invoice;

            $transaction->status = 1;
            $transaction->save();
            if (env('SEND_MAIL')) {
                Mail::to($student->email)->send(new TransactionMail($data));
            }
        }

        // if($paymentType == Payment::PAYMENT_TYPE_WALLET_DEPOSIT){
        //     $creditStudent = $this->creditStudentWallet($studentId, $amount);
        //     if(!$creditStudent){
        //         Log::info("**********************Unable to credit student wallet**********************: ". $amount .' - '.$student);
        //     }
        // }

        if ($paymentType == Payment::PAYMENT_TYPE_BANDWIDTH) {
            $transaction = Transaction::where('reference', $transactionData->reference)->first();
            $creditStudent = $this->creditBandwidth($transaction, $amount);

            if (!$creditStudent) {
                Log::info("**********************Unable to credit student bandwidth**********************: ". $amount .' - '.$student);
            }
        }


        if ($paymentType == Payment::PAYMENT_TYPE_ACCOMONDATION) {
            $transaction = Transaction::where('reference', $transactionData->reference)->first();
            $creditStudent = $this->creditAccommodation($transaction);
            if (is_string($creditStudent)) {
                alert()->error('Oops', $creditStudent)->persistent('Close');
            }
        }

        if ($paymentType == Payment::PAYMENT_TYPE_SCHOOL || $paymentType == Payment::PAYMENT_TYPE_SCHOOL_DE) {
            $this->generateMatricAndEmail($student);
        }

        if ($paymentType == Payment::PAYMENT_TYPE_SUMMER_COURSE_REGISTRATION) {
            $transaction = Transaction::where('reference', $transactionData->reference)->first();
            $creditStudent = $this->creditStudentSummerCourseReg($transaction);
            if (!$creditStudent) {
                Log::info("**********************Unable to credit student summer course reg**********************: ". $amount .' - '.$student .' - '.$transaction->additional_data);
            }
        }

        alert()->success('Good Job', 'Payment successful')->persistent('Close');
        return redirect($transactionData->redirect_path);
    }

    public function getPreviousAcademicSession($currentAcademicSession)
    {
        [$year, $nextYear] = explode('/', $currentAcademicSession);
        $year = intval($year);

        // If we are in the first term, the previous session starts 2 years earlier
        if ($nextYear == $year + 1) {
            $prevYear = $year - 1;
            $prevNextYear = $nextYear - 1;
        } else {
            $prevYear = $year - 2;
            $prevNextYear = $nextYear - 2;
        }

        $prevAcademicSession = $prevYear . '/' . $prevNextYear;

        return $prevAcademicSession;
    }


    // public function classifyCourses($students, $semester, $academicLevel, $academicSession) {
    //     $classifiedCourses = [];

    //     foreach ($students as $student) {
    //         foreach ($student->registeredCourses->where('semester', $semester)->where('level_id', $academicLevel->id)->where('academic_session', $academicSession) as $registeredCourse) {
    //             $courseName = $registeredCourse->course_code;

    //             if (!isset($classifiedCourses[$courseName])) {
    //                 $classifiedCourses[$courseName] = [];
    //             }

    //             $classifiedCourses[$courseName][] = $student;
    //         }
    //     }

    //     return $classifiedCourses;
    // }

    public function classifyCourses($students, $semester, $academicLevel, $academicSession)
    {
        $classifiedCourses = [];

        foreach ($students as $student) {
            $courses = $student->registeredCourses
                ->where('semester', $semester)
                ->where('level_id', $academicLevel->id)
                ->where('academic_session', $academicSession);

            foreach ($courses as $registeredCourse) {
                $courseCode = $registeredCourse->course_code;

                // If not yet set, initialize with metadata and an empty student list
                if (!isset($classifiedCourses[$courseCode])) {
                    $classifiedCourses[$courseCode] = [
                        'course' => $registeredCourse, // one sample instance
                        'students' => [],
                    ];
                }

                $classifiedCourses[$courseCode]['students'][] = $student;
            }
        }

        return $classifiedCourses;
    }

    public function createApplicant($applicantData)
    {
        $slug = isset($applicantData['data']['metadata']['slug']) ? $applicantData['data']['metadata']['slug'] : (isset($applicantData['data']['meta']['slug']) ? $applicantData['data']['meta']['slug'] : null);
        $email = isset($applicantData['data']['metadata']['email']) ? $applicantData['data']['metadata']['email'] : (isset($applicantData['data']['meta']['email']) ? $applicantData['data']['meta']['email'] : null);
        $lastname = isset($applicantData['data']['metadata']['lastname']) ? $applicantData['data']['metadata']['lastname'] : (isset($applicantData['data']['meta']['lastname']) ? $applicantData['data']['meta']['lastname'] : null);
        $phoneNumber = isset($applicantData['data']['metadata']['phone_number']) ? $applicantData['data']['metadata']['phone_number'] : (isset($applicantData['data']['meta']['phone_number']) ? $applicantData['data']['meta']['phone_number'] : null);
        $otherNames = isset($applicantData['data']['metadata']['othernames']) ? $applicantData['data']['metadata']['othernames'] : (isset($applicantData['data']['meta']['othernames']) ? $applicantData['data']['meta']['othernames'] : null);
        $password = isset($applicantData['data']['metadata']['password']) ? $applicantData['data']['metadata']['password'] : (isset($applicantData['data']['meta']['password']) ? $applicantData['data']['meta']['password'] : null);
        $applicationSession = isset($applicantData['data']['metadata']['academic_session']) ? $applicantData['data']['metadata']['academic_session'] : (isset($applicantData['data']['meta']['academic_session']) ? $applicantData['data']['meta']['academic_session'] : null);
        $partnerId = isset($applicantData['data']['metadata']['partner_id']) ? $applicantData['data']['metadata']['partner_id'] : (isset($applicantData['data']['meta']['partner_id']) ? $applicantData['data']['meta']['partner_id'] : null);
        $referralCode = isset($applicantData['data']['metadata']['referrer']) ? $applicantData['data']['metadata']['referrer'] : (isset($applicantData['data']['meta']['referrer']) ? $applicantData['data']['meta']['referrer'] : null);
        $applicationType = isset($applicantData['data']['metadata']['application_type']) ? $applicantData['data']['metadata']['application_type'] : (isset($applicantData['data']['meta']['application_type']) ? $applicantData['data']['meta']['application_type'] : null);
        $txRef = isset($applicantData['data']['reference']) ? $applicantData['data']['reference'] : (isset($applicantData['data']['meta']['reference']) ? $applicantData['data']['meta']['reference'] : null);
        $programmeCategoryId = isset($applicantData['data']['metadata']['programme_category_id']) ? $applicantData['data']['metadata']['programme_category_id'] : (isset($applicantData['data']['meta']['programme_category_id']) ? $applicantData['data']['meta']['programme_category_id'] : null);
        $applicant = null;


        if (isset($applicantData['test_applicant_id'])) {
            $testApplicant = TestApplicant::find($applicantData['test_applicant_id']);

            $slug = $testApplicant->slug;
            $email = $testApplicant->email;
            $lastname = $testApplicant->lastname;
            $phoneNumber = $testApplicant->phone_number;
            $otherNames = $testApplicant->othernames;
            $password = $testApplicant->passcode;
            $applicationSession = $testApplicant->academic_session;
            $partnerId = $testApplicant->partner_id;
            $referralCode = $testApplicant->referrer;
            $applicationType = $testApplicant->application_type;
            $txRef = $testApplicant->reference;
            $programmeCategoryId = $testApplicant->programme_category_id;
        }


        $newApplicant = ([
            'slug' => $slug,
            'email' => strtolower($email),
            'lastname' => ucwords($lastname),
            'phone_number' => $phoneNumber,
            'othernames' => ucwords($otherNames),
            'password' => Hash::make($password),
            'passcode' => $password,
            'academic_session' => $applicationSession,
            'partner_id' => !empty($partnerId) ? $partnerId : null,
            'referrer' => $referralCode,
            'application_type' => $applicationType,
            'programme_category_id' => $programmeCategoryId
        ]);

        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('id', $programmeCategoryId)->first();

        if (!$checkApplicant = User::where('email', strtolower($email))->where('academic_session', $applicationSession)->first()) {
            Log::info("********************** Creating Applicant **********************: ".' - '.$lastname.' - '.$otherNames);
            $applicant = User::create($newApplicant);
            $applicationNumber = env('SCHOOL_CODE').'/'.$programmeCategory->code.'/'.substr($applicationSession, 0, 4).sprintf("%03d", ($applicant->id + env('APPLICATION_STARTING_NUMBER')));
            $applicant->application_number = $applicationNumber;
            $applicant->save();
            Log::info("********************** Applicant Created **********************: ".' - '.$lastname.' - '.$otherNames .' - '.$applicationNumber);

            if (!empty($txRef)) {
                if ($existTx = Transaction::where('reference', $txRef)->where('status', 1)->first()) {
                    $existTx->user_id = $applicant->id;
                    $existTx->save();
                }
            }
        }

        if (empty($applicant)) {
            $applicant = User::find($checkApplicant->id);
            $tx = Transaction::where('reference', $txRef)->where('status', 1)->first();
            $tx->user_id = $checkApplicant->id;
            $tx->save();
        }
        if (env('SEND_MAIL')) {
            Mail::to($applicant->email)->send(new ApplicationMail($applicant));
        }
        return true;
    }

    public function sendGuardianOnboardingMail($student)
    {
        if (!empty($student->applicant) && !empty($student->applicant->guardian_id)) {
            $guardianId = $student->applicant->guardian_id;
            $guardian = Guardian::find($guardianId);

            if ($guardian) {
                $guardianEmail = $guardian->email;
                $guardianPasscode = $guardian->passcode;
                if (env('SEND_MAIL')) {
                    Mail::to($guardianEmail)->send(new GuardianOnboardingMail($guardian));
                }

                return true;
            }

            return false;
        }

        return false;
    }

    public function capturedWorkingDays()
    {

        $startDateOfPresentMonth = Carbon::now()->startOfMonth();
        $today = Carbon::now();
        $diff = $startDateOfPresentMonth->diffInDays($today);
        $weekendDays = $this->countWeekendDays($startDateOfPresentMonth, $today);
        $capturedWorkingDays = $diff - $weekendDays;

        return $capturedWorkingDays;

    }

    public function workingDays()
    {
        $startDateOfPresentMonth = Carbon::now()->startOfMonth();
        $endDateOfPresentMonth = Carbon::now()->endOfMonth();
        $daysOfPresentMonth = Carbon::now()->daysInMonth;
        $weekendDays = $this->countWeekendDays($startDateOfPresentMonth, $endDateOfPresentMonth);

        $workingDays = $daysOfPresentMonth - $weekendDays;

        return $workingDays;
    }

    public function countWeekendDays($startDate, $endDate)
    {
        $weekendDays = $startDate->diffInDaysFiltered(function (Carbon $date) {
            return $date->isWeekend();
        }, $endDate);

        return $weekendDays;
    }

    public function creditAccommodation(Transaction $transaction)
    {
        if (empty($transaction->additional_data)) {
            return true;
        }

        $allocationData = json_decode($transaction->additional_data, true);

        if (!$allocationData || !isset($allocationData['room_id'], $allocationData['hostel_id'], $allocationData['campus'], $allocationData['type_id'])) {
            return 'Invalid allocation data.';
        }

        $roomId = $allocationData['room_id'];
        $hostelId = $allocationData['hostel_id'];
        $campus = $allocationData['campus'];
        $typeId = $allocationData['type_id'];

        $room = $this->findRoomWithVacancy($roomId, $typeId);

        if (!$room) {
            return 'No available bed spaces in the selected room type. kindly pick another room';
        }

        $availableBedSpace = RoomBedSpace::where('room_id', $room->id)
            ->whereDoesntHave('currentAllocation')
            ->first();

        if (!$availableBedSpace) {
            return 'No available bed spaces found. kindly pick another room';
        }

        $checkStudentAllocationCount = Allocation::where('student_id', $transaction->student_id)->where('academic_session', $transaction->session)->count();
        if ($checkStudentAllocationCount > 0) {
            return 'Student already assigned to a room for this academic session';
        }

        // Create the allocation record
        DB::beginTransaction();

        try {
            Allocation::create([
                'student_id' => $transaction->student_id,
                'room_id' => $room->id,
                'bed_id' => $availableBedSpace->id,
                'academic_session' => $transaction->session,
                'allocation_date' => Carbon::now(),
            ]);

            DB::commit();

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            return 'Failed to allocate accommodation: ' . $e->getMessage();
        }
    }

    public function initChangeProgramme(Transaction $transaction, $studentId)
    {
        $existing = ProgrammeChangeRequest::where('transaction_id', $transaction->id)->first();

        if (!$existing) {
            ProgrammeChangeRequest::create([
                'transaction_id' => $transaction->id,
                'student_id' => $studentId,
                'slug' => md5($studentId . time()),
            ]);

            return true;
        }
    }

    public function creditStudentSummerCourseReg(Transaction $transaction)
    {
        if (empty($transaction->additional_data)) {
            return false;
        }

        if (!empty($transaction->is_used)) {
            return false;
        }

        $selectedCourses = json_decode($transaction->additional_data, true);

        if (empty($selectedCourses)) {
            alert()->info('Kindly select your courses', '')->persistent('Close');
            return redirect()->back();
        }

        $studentId = $transaction->student_id;
        $student = Student::find($studentId);
        $levelId = $student->level_id;

        foreach ($selectedCourses as $failedCourseId) {
            $courseReg = CourseRegistration::with('course')->findOrFail($failedCourseId);
            $course = $courseReg->course;

            // Register the summer course
            SummerCourseRegistration::create([
                'student_id' => $studentId,
                'course_id' => $course->id,
                'programme_category_id' => $student->programme_category_id,
                'course_registration_id' => $failedCourseId,
                'academic_session' => $courseReg->academic_session,
            ]);
        }

        // Generate the summer course registration PDF
        $pdf = new Pdf();
        $summerCourseReg = $pdf->generateSummerCourseRegistration($studentId, $courseReg->academic_session); // Assume this returns a file path or name

        // Store file name/path in `additional_file` field
        $transaction->additional_file = json_encode(['summerCourseReg' => $summerCourseReg]);
        $transaction->is_used = 1;
        $transaction->save();

        alert()->success('Changes Saved', 'Course registration saved successfully')->persistent('Close');
        return redirect()->back();
    }

    protected function findRoomWithVacancy($initialRoomId, $typeId)
    {
        // Check the initial room first
        $room = Room::with('allocations')->find($initialRoomId);

        if ($room) {
            $totalBedSpaces = RoomBedSpace::where('room_id', $room->id)->count();
            $occupiedSpaces = Allocation::where('room_id', $room->id)
                ->whereNull('release_date')
                ->count();

            if ($occupiedSpaces < $totalBedSpaces) {
                return $room;
            }
        }

        // If the initial room is full, find another room of the same type with vacancies
        $roomWithVacancy = Room::where('type_id', $typeId)
        ->whereHas('bedSpaces', function ($query) {
            $query->whereDoesntHave('currentAllocation');  // Only consider bed spaces without a current allocation
        })
        ->first();


        return $roomWithVacancy;
    }

    public static function checkLateRegistration()
    {
        $resumptionDate = env('RESUMPTION_DATE');
        $gracePeriod = env('LATE_REGISTRATION_GRACE_PERIOD');

        $resumptionDate = Carbon::parse($resumptionDate);

        $lateRegStartDate = $resumptionDate->addWeeks($gracePeriod);

        $now = Carbon::now();

        if ($now->greaterThan($lateRegStartDate)) {
            $daysPast = $now->diffInWeekdays($lateRegStartDate);
            $weeksPast = floor($daysPast / 5);

            // Return true with the number of days and weeks past
            $data = new \stdClass();
            $data->isLate = true;
            $data->daysPast = $daysPast;
            $data->weeksPast = $weeksPast;

            return $data;
        }

        $data = new \stdClass();
        $data->isLate = false;
        $data->daysPast = 0;
        $data->weeksPast = 0;

        return $data;
    }

    public static function checkNewStudentStatus($student)
    {
        $levelId = $student->level_id;
        $applicationType = $student->application_type;


        if ((($levelId == 1 && strtolower($applicationType) == 'utme') ||
            ($levelId == 2 && strtolower($applicationType) != 'utme'))) {

            return true;
        }

        return false;
    }

    public static function sortCourses($courses)
    {
        return $courses->sort(function ($course) {
            $code = $course->course->code;

            // Rule 1: Check if the code includes "GST"
            if (strpos($code, 'GST') !== false) {
                // Get last digit (assuming last character after space)
                $lastDigit = intval(substr(strrchr($code, ' '), -1));
                return ['priority' => 1, 'last_digit' => $lastDigit, 'length' => strlen($code), 'code' => $code];
            }

            // Rule 4: Check if the code includes "DSA"
            if (strpos($code, 'DSA') !== false) {
                return ['priority' => 5, 'length' => strlen($code), 'code' => $code]; // Last rank
            }

            // Rule 5: Check if the code includes "TAU" and length is less than 9
            if (strpos($code, 'TAU') !== false && strlen($code) < 9) {
                return ['priority' => 4, 'length' => strlen($code), 'code' => $code]; // Just above DSA
            }

            // Rule 2: Sort by alphabetical order and last digit
            $lastDigit = intval(substr(strrchr($code, ' '), -1)); // Assuming last digit is after a space
            return ['priority' => 3, 'last_digit' => $lastDigit, 'length' => strlen($code), 'code' => $code];
        })->sortBy(function ($course) {
            return [
                $course['priority'], // First by priority
                $course['last_digit'], // Then by last digit
                strlen($course['code']), // Then by length of code
                $course['code'] // Finally by code itself
            ];
        })->values(); // Reset keys
    }







    public function getAuthorizedStudents($courseId, $programmeCategoryStr, $academicSession = null)
    {
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting')
            ->where('category', $programmeCategoryStr)
            ->first();

        $programmeCategoryId = $programmeCategory->id;

        if (empty($academicSession)) {
            $academicSession = $programmeCategory->academicSessionSetting->academic_session;
        } else {
            $academicSession = str_replace('-', '/', $academicSession);
        }

        $registrations = CourseRegistration::with('student.applicant')
            ->where('course_id', $courseId)
            ->where('programme_category_id', $programmeCategoryId)
            ->where('academic_session', $academicSession)
            ->get();

        $courseLectures = CourseLecture::where('course_id', $courseId)
            ->where('programme_category_id', $programmeCategoryId)
            ->where('academic_session', $academicSession)
            ->get();

        $totalLectures = $courseLectures->count();
        $courseLectureIds = $courseLectures->pluck('id');

        $authorizedStudents = [];

        foreach ($registrations as $registration) {
            $student = $registration->student;

            if (!$student) {
                continue;
            }

            $attendanceCount = LectureAttendance::whereIn('course_lecture_id', $courseLectureIds)
                ->where('student_id', $student->id)
                ->where('status', 1)
                ->count();

            $attendancePercentage = $totalLectures > 0 ? ($attendanceCount / $totalLectures) * 100 : 0;

            // Use a helper or trait for this part
            $paymentStatus = $this->checkSchoolFees($student, $academicSession, $registration->level_id);
            $course = Course::find($courseId);

            if (
                $attendancePercentage >= 75 &&
                $paymentStatus->passTuitionPayment &&
                $paymentStatus->passEightyTuition &&
                $paymentStatus->fullTuitionPayment
            ) {
                $authorizedStudents[] = [
                    'student' => $student,
                    'attendancePercentage' => round($attendancePercentage, 2)
                ];
            }
        }

        return [
            'students' => $authorizedStudents,
            'course' => $course,
            'courseLectures' => $courseLectures,
            'academicSession' => $academicSession,
            'programmeCategory' => $programmeCategory->category
        ];
    }
}

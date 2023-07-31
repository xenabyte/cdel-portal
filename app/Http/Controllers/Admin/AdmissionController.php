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

use App\Models\Programme;
use App\Models\Transaction;
use App\Models\User as Applicant;
use App\Models\Olevel;
use App\Models\Guardian;
use App\Models\NextOfKin;

use App\Mail\ApplicationMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;

class AdmissionController extends Controller
{
    //

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

    public function applicants(Request $request){
        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['application_session'];
        $applicants = Applicant::where('academic_session', $academicSession)->get();

        return view('admin.applicants', [
            'applicants' => $applicants,
        ]);
    }

    public function applicantWithSession(Request $request){
        $applicants = Applicant::with('programme', 'olevels', 'guardian')->where('academic_session', $request->session)->get();
        
        return view('admin.applicants', [
            'applicants' => $applicants
        ]);
    }

    public function applicant(Request $request, $slug){
        
        $applicant = Applicant::with('programme', 'olevels', 'guardian')->where('slug', $slug)->first();
        
        return view('admin.applicant', [
            'applicant' => $applicant
        ]);
    }

    
}

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
use League\Csv\Reader;

use App\Models\Programme;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\Student;
use App\Models\SessionSetting;
use App\Models\Admin;
use App\Models\Staff;

use App\Libraries\Google\Google;
use App\Libraries\Pdf\Pdf;


use Paystack;
use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

use KingFlamez\Rave\Facades\Rave as Flutterwave;

class ApiController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   
     public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required',
            'role' => 'required',
            'api_key' => 'required',
        ]);
        $appApiKey = env('APP_API_KEY');

        $email = $request->email;
        $password = $request->password;
        $role = $request->role;
        $apiKey = $request->api_key;

        if($appApiKey != $apiKey){
            return $this->dataResponse('Invalid Api Key', null, 'error');
        }

        switch ($role) {
            case 'admin':
                $user = Admin::where('email', $email)->first();
                break;
            case 'staff':
                $user = Staff::where('email', $email)->first();
                break;
            case 'student':
                $user = Student::where('email', $email)->first();
                break;
            default:
                return $this->dataResponse('Invalid role', null, 'error');
        }
    
        // Check if the user exists and if the password matches
        if ($user && \Hash::check($password, $user->password)) {
            return $this->dataResponse('Login Successful!', $user);
        } else {
            return $this->dataResponse('Invalid Credentials', null, 'error');
        }

    }
    
    public function validateUser(Request $request){
        $validator = Validator::make($request->all(), [
            'uniqueId' => 'required',
            'role' => 'required',
            'api_key' => 'required',
        ]);
        $appApiKey = env('APP_API_KEY');

        $uniqueId = $request->uniqueId;
        $role = $request->role;
        $apiKey = $request->api_key;

        if($appApiKey != $apiKey){
            return $this->dataResponse('Invalid Api Key', null, 'error');
        }

        switch ($role) {
            case 'staff':
                $user = Staff::where('email', $uniqueId)->first();
                break;
            case 'student':
                $user = Student::where('matric_number', $uniqueId)->first();
                break;
            default:
                return $this->dataResponse('Invalid role', null, 'error');
        }
    
        // Check if the user exists and if the password matches
        if ($user) {
            $response = new \stdClass();
            $response->lastname = $user->lastname;
            $response->othernames = $user->othernames;
            $response->email = $user->email;
            $response->image = $role == 'student'? 'https://portal.tau.edu.ng/'.$user->image : $user->image;

            return $this->dataResponse($role.' record found!', $response);
        } else {
            return $this->dataResponse($role.' record not found', null, 'error');
        }

    }
}

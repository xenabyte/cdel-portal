<?php

namespace App\Http\Controllers\Partner\Auth;

use App\Models\Partner;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Auth;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

use App\Mail\NotificationMail;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/partner/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('partner.guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:partners',
            'password' => 'required|min:6|confirmed',
            'address' => 'required',
            'phone_number' => 'required',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return Partner
     */
    protected function create(array $data)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $data['name'])));
        $name = $data['name'];

        $partner = Partner::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'address' => $data['address'],
            'phone_number' => $data['phone_number'],
            'slug' => $slug,
            'status' => 0,
            'referral_code' => $this->generateReferralCode(),
        ]);

        $message = 'A new partner - ('. $name .') has just registered on the portal and needs to be approved. Admin, please kindly go to the portal to verify the partner details.';
        $senderName = env('SCHOOL_NAME');
        $receiverName = 'Portal Admininstrator';
        $adminEmail = env('APP_EMAIL');
        
        $mail = new NotificationMail($senderName, $message, $receiverName);
        Mail::to($adminEmail)->send($mail);
        
        return $partner;
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('partner.auth.register');
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('partner');
    }
}

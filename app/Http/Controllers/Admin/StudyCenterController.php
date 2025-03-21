<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


use App\Models\Student;
use App\Models\Center;
use App\Models\Notification;

use App\Mail\NotificationMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudyCenterController extends Controller
{
    //

    public function studyCenters(){
        $studyCenters = Center::all();

        return view('admin.studyCenters', [
           'studyCenters' => $studyCenters
        ]);
    }
}

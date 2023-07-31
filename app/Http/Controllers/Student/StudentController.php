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


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StudentController extends Controller
{
    //

    public function index(Request $request){

        return view('student.home');
    }
}

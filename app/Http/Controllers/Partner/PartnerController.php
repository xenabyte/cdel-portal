<?php

namespace App\Http\Controllers\Partner;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Partner;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class PartnerController extends Controller
{
    //

    public function index(Request $request){

        return view('partner.home');
    }
}

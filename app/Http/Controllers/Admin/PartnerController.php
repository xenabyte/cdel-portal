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

use App\Models\Partner;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class PartnerController extends Controller
{
    //

    public function partners(Request $request){

        $partners = Partner::where('status', 1)->get();

        return view('admin.partners',[
            'partners' => $partners
        ]);
    }

    public function partnerApproval(Request $request){

        $partners = Partner::where('status', 0)->get();

        return view('admin.partnerApproval',[
            'partners' => $partners
        ]);
    }

    public function transactions(Request $request){

        return view('partner.transactions');
    }

    public function students(Request $request){

        return view('partner.students');
    }

    public function applicants(Request $request){

        return view('partner.applicants');
    }

    public function profile(Request $request){

        return view('partner.profile');
    }

}

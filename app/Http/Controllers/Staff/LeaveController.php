<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;


use App\Models\Attendance;
use App\Models\Staff;
use App\Models\Leave;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class LeaveController extends Controller
{
    //

    public function leaveApplication(){
        $staff = Auth::guard('staff')->user();
        $leaves = Leave::where('staff_id', $staff->id)->get();

        return view('staff.leaveApplication', [
            'leaveApplications' => $leaves,
        ]);
    }

    public function applyLeave(Request $request){
        
    }
}

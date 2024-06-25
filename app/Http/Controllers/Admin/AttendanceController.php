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

use App\Models\Attendance;
use App\Models\Staff;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    //

    public function attendance(){
        $year = Carbon::parse()->format('Y');
        $month = Carbon::parse()->format('M');
        $capturedWorkingDays = $this->capturedWorkingDays();

        $staffRecords = Staff::get();
        $staffs = array();
        foreach ($staffRecords as $staffRecord){
            $staff = $staffRecord;
            $staffId = $staffRecord->id;

            $attendance = Attendance::where('staff_id', $staffId)->where('year', $year)->where('month', $month)->where('status', 2)->get();
            $staff->attendance = $attendance;

            $leaveDays = Attendance::where('staff_id', $staffId)->where('year', $year)->where('month', $month)->where('status', 2)->where('leave_id', '!=', Null)->count();
            $staff->leaveDays = $leaveDays;


            $staffs[] = $staff;
        }

        return view('admin.attendance', [
            'staff' => $staffs,
            'capturedWorkingDays' => $capturedWorkingDays
        ]);
    }
}

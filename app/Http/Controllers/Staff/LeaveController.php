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
use App\Models\Notification;

use App\Mail\NotificationMail;

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
        $allstaff = Staff::get();

        return view('staff.leaveApplication', [
            'leaveApplications' => $leaves,
            'staff' => $allstaff
        ]);
    }

    public function applyForLeave(Request $request) {

        $validator = Validator::make($request->all(), [
            'purpose' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            'assisting_staff_id' => 'required'
        ]);

        $staff = Auth::guard('staff')->user();

        $staffId = $staff->id;
        $staffName = $staff->title.' '.$staff->lastname.' '.$staff->othernames;
        $date = Carbon::now();

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $staffName.'-'.$date)));

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        
        $startDate = Carbon::parse($request->start_date);
        $endDate = Carbon::parse($request->end_date);

        if($startDate == $endDate || $startDate > $endDate) {
            alert()->error('Error', 'Invalid leave application, review your leave starting date and resumption date')->persistent('Close');
            return redirect()->back();
        }

        $days = $startDate->diffInDays($endDate) - $this->countWeekendDays($startDate, $endDate) + 1;

        $newLeaveApplication = ([
            'slug' => $slug,
            'staff_id' => $staffId,
            'purpose' => $request->purpose,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'days' => $days,
            'destination_address' => $request->destination_address,
            'assisting_staff_id' => $request->assisting_staff_id
        ]);

        if(Leave::create($newLeaveApplication)){
            //send mail to assisting staff
            if($assistingStaff = Staff::find($request->assisting_staff_id)){
                $senderName = env('SCHOOL_NAME');
                $receiverName = $assistingStaff->lastname .' ' . $assistingStaff->othernames;
                $message = 'You have been assigned to assist in discharging duties for'.$staff->lastname.' '. $staff->othernames .' while he/she is on leave. Please log in to the staff portal to review and approve this assignment.';

    
                $mail = new NotificationMail($senderName, $message, $receiverName);
                Mail::to($assistingStaff->email)->send($mail);
                Notification::create([
                    'staff_id' => $assistingStaff->id,
                    'description' => $message,
                    'status' => 0
                ]);
            }

            alert()->success('Success', 'Leave application process started successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error', 'Error Submitting Leave Application, Report to Administrator')->persistent('Close');
        return redirect()->back();

    }

    public function leaves(){
        $staff = Auth::guard('staff')->user();
        $leaves = Leave::where('staff_id', $staff->id)->get();

        return view('staff.leaves', [
            'leaveApplications' => $leaves,
        ]);
    }

    public function manageLeaves(){
        $staff = Auth::guard('staff')->user();

        $leaves = Leave::where('hod_id', $staff->id)
        ->orWhere('dean_id', $staff->id)
        ->orWhere('hr_id', $staff->id)
        ->orWhere('registrar_id', $staff->id)
        ->orWhere('vc_id', $staff->id)
        ->get();

        return view('staff.manageLeaves', [
            'leaveManagement' => $leaves,
        ]);
    }

    public function leave($slug){
        $leave = Leave::where('slug', $slug)->first();
        $leaves = Leave::where('staff_id', $leave->staff_id)->get();

        return view('staff.leave', [
            'leave' => $leave,
        ]);
    }

}

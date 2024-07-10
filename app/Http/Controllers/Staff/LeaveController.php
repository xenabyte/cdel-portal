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
use App\Models\Unit;

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
        ->orWhere('assisting_staff_id', $staff->id)
        ->get();

        return view('staff.manageLeaves', [
            'leaveApplications' => $leaves,
        ]);
    }

    public function leave($slug){
        $leave = Leave::where('slug', $slug)->first();
        $leaves = Leave::where('staff_id', $leave->staff_id)->get();

        return view('staff.leave', [
            'leave' => $leave,
        ]);
    }


    public function assistingStaffMgt(Request $request){
        $staff = Auth::guard('staff')->user();
        
        $validator = Validator::make($request->all(), [
            'leave_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $status = $request->status;

        if($leave = Leave::where('assisting_staff_id', $staff->id)->where('id', $request->leave_id)->first()){
            $leave->assisting_staff_status = $status;
            

            $assistingStaff = Staff::find($leave->assisting_staff_id);
            $receiverName = $staff->lastname .' ' . $staff->othernames;
            $senderName = env('SCHOOL_NAME');

            $staffCategory = $staff->category;
            $preceedingOfficer = $staffCategory == "Academic" ? "Head of Department" : "Head of Unit";
            $hodId = null;
            $hodName = null;
            if ($staffCategory == "Academic") {
                $staffDepartment = Department::with('hod')->where('id', $staff->department_id)->first();
                
                $hodId = $staffDepartment? $staffDepartment->hod->id : null;
            } else {
                $staffDepartment = Unit::with('unit_head')->where('id', $staff->unit_id)->first();
                $hodId = $staffDepartment? $staffDepartment->unit_head->id : null;
            }

            


            if($status == "confirmed"){
                $message = 'Your standing in staff has agreed to stand in during your leave period. your leave application have been pushed to your '.$preceedingOfficer;

                if($assistingStaff){
                    $message = $assistingStaff->lastname.' '. $assistingStaff->othernames .' has agreed to stand in during your leave period. your leave application have been pushed to the '.$preceedingOfficer;
                }

                if($hodId){
                    $leave->hod_id = $hodId;

                    $hodMessage = 'You have a pending leave application to attend to. The standing-in staff has agreed to cover during the leave period. Please review the application on the staff portal.';
                    $mail = new NotificationMail($senderName, $hodMessage, $receiverName);
                    Mail::to($staff->email)->send($mail);
                    Notification::create([
                        'staff_id' => $hodId,
                        'description' => $hodMessage,
                        'status' => 0
                    ]);
                }

            }else{
                $message = 'Your standing in staff has declined to stand in during your leave period. Please log in to update your leave and select another staff.';

                if($assistingStaff){
                    $message = $assistingStaff->lastname.' '. $assistingStaff->othernames .'\' has declined to stand in during your leave period. Please log in to update your leave and select another staff.';
                }
            }

            $leave->save();

            $mail = new NotificationMail($senderName, $message, $receiverName);
            Mail::to($staff->email)->send($mail);
            Notification::create([
                'staff_id' => $staff->id,
                'description' => $message,
                'status' => 0
            ]);


            alert()->success('Success', 'Assisting staff has been removed from this leave application')->persistent('Close');
            return redirect()->back();
        }


        alert()->error('Error', 'Invalid Leave Application, Report to Administrator')->persistent('Close');
        return redirect()->back();
    }

}

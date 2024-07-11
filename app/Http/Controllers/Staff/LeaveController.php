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
use App\Models\StaffRole;
use App\Models\Leave;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\Role;

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

        // Validate request
        $validator = Validator::make($request->all(), [
            'leave_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        $status = $request->status;

        // Check if the leave exists for the assisting staff
        $leave = Leave::where('assisting_staff_id', $staff->id)->where('id', $request->leave_id)->first();
        if (!$leave) {
            alert()->error('Error', 'Invalid Leave Application, Report to Administrator')->persistent('Close');
            return redirect()->back();
        }

        $leave->assisting_staff_status = $status;

        $assistingStaff = Staff::find($leave->assisting_staff_id);
        $receiverName = $staff->lastname . ' ' . $staff->othernames;
        $senderName = env('SCHOOL_NAME');

        // Determine the preceding officer based on staff category
        $staffCategory = $staff->category;
        $precedingOfficer = $staffCategory == "Academic" ? "Head of Department" : "Head of Unit";

        // Get HOD details
        $hodId = null;
        if ($staffCategory == "Academic") {
            $staffDepartment = Department::with('hod')->where('id', $staff->department_id)->first();
            $hodId = $staffDepartment ? $staffDepartment->hod->id : null;
            $hodName =  $staffDepartment->hod? $staffDepartment->hod->lastname . ' ' . $staffDepartment->hod->othernames : null;
            $hodEmail = $staffDepartment->hod? $staffDepartment->hod->email : null;
        } else {
            $staffDepartment = Unit::with('unit_head')->where('id', $staff->unit_id)->first();
            $hodId = $staffDepartment->unit_head ? $staffDepartment->unit_head->id : null;
            $hodName =  $staffDepartment->unit_head? $staffDepartment->unit_head->lastname . ' ' . $staffDepartment->unit_head->othernames : null;
            $hodEmail = $staffDepartment->unit_head? $staffDepartment->unit_head->email : null;
        }

        // Construct messages based on status
        if ($status == "confirm") {
            $message = 'Your standing-in staff has agreed to stand in during your leave period. Your leave application has been pushed to your ' . $precedingOfficer;

            if ($assistingStaff) {
                $message = $assistingStaff->lastname . ' ' . $assistingStaff->othernames . ' has agreed to stand in during your leave period. Your leave application has been pushed to the ' . $precedingOfficer;
            }

            if ($hodId) {
                $leave->hod_id = $hodId;
                $hodMessage = 'You have a pending leave application to attend to. The standing-in staff has agreed to cover during the leave period. Please review the application on the staff portal.';
                
                // Send notification to HOD
                $mail = new NotificationMail($senderName, $hodMessage, $hodName);
                Mail::to($hodEmail)->send($mail);
                Notification::create([
                    'staff_id' => $hodId,
                    'description' => $hodMessage,
                    'status' => 0
                ]);
            }
        } else {
            $message = 'Your standing-in staff has declined to stand in during your leave period. Please log in to update your leave and select another staff.';

            if ($assistingStaff) {
                $message = $assistingStaff->lastname . ' ' . $assistingStaff->othernames . ' has declined to stand in during your leave period. Please log in to update your leave and select another staff.';
            }
        }

        $leave->save();

        // Send notification to the staff
        $mail = new NotificationMail($senderName, $message, $receiverName);
        Mail::to($staff->email)->send($mail);
        Notification::create([
            'staff_id' => $staff->id,
            'description' => $message,
            'status' => 0
        ]);

        alert()->success('Success', 'Assisting staff status has been updated successfully')->persistent('Close');
        return redirect()->back();
    }

    public function hodLeaveMgt(Request $request){
        $staff = Auth::guard('staff')->user();

        // Validate request
        $validator = Validator::make($request->all(), [
            'leave_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        $status = $request->status;

        // Check if the leave exists for the assisting staff
        $leave = Leave::where('hod_id', $staff->id)->where('id', $request->leave_id)->first();
        if (!$leave) {
            alert()->error('Error', 'Invalid Leave Application, Report to Administrator')->persistent('Close');
            return redirect()->back();
        }

        $leave->hod_status = $status;
        $leave->hod_comment = $request->hod_comment;

        $hodStaff = Staff::find($leave->hod_id);
        $receiverName = $staff->lastname . ' ' . $staff->othernames;
        $senderName = env('SCHOOL_NAME');

        // Determine the preceding officer based on staff category
        $staffCategory = $staff->category;
        $precedingOfficer = $staffCategory == "Academic" ? "Head of Faculty" : "HR";

        // Get HOD details
        $deanId = null;
        $hrId = null;
        if ($staffCategory == "Academic") {
            $staffFaculty = Faculty::with('dean')->where('id', $staff->faculty_id)->first();
            $deanId = $staffFaculty ? $staffFaculty->dean->id : null;
            $deanName = $staffFaculty->dean->lastname . ' ' . $staffFaculty->dean->othernames;
            $deanEmail = $staffFaculty->dean->email;
        } else {
            $hrRoleId = Role::getRole(Role::ROLE_HR);
            $hr = Staff::whereHas('staffRoles', function ($query) use ($hrRoleId) {
                $query->where('role_id', $hrRoleId);
            })->first();
            $hrName = $hr->lastname . ' ' . $hr->othernames;
            $hrEmail = $hr->email;
        }

        // Construct messages based on status
        if ($status == "confirm") {
            $message = 'Your HOD/HOU has approve your leave application. Application has been sent to the '.$precedingOfficer.' for processing';

            if ($deanId) {
                $leave->dean_id = $deanId;
                $deanMessage = 'You have a pending leave application to attend to. Please review the application on the staff portal.';
                
                // Send notification to HOD
                $deanMail = new NotificationMail($senderName, $deanMessage, $deanName);
                Mail::to($deanEmail)->send($deanMail);
                Notification::create([
                    'staff_id' => $deanId,
                    'description' => $deanMessage,
                    'status' => 0
                ]);
            }

            if ($hrId) {
                $leave->hr_id = $hrId;
                $hrMessage = 'You have a pending leave application to attend to. Please review the application on the staff portal.';
                
                // Send notification to HOD
                $hrMail = new NotificationMail($senderName, $hrMessage, $hrName);
                Mail::to($hrEmail)->send($hrMail);
                Notification::create([
                    'staff_id' => $hrId,
                    'description' => $hrMessage,
                    'status' => 0
                ]);
            }
        } else {
            $message = 'Your HOD/HOU has declined your leave application.';
            $leave->status = $request->status;
        }

        $leave->save();

        // Send notification to the staff
        $mail = new NotificationMail($senderName, $message, $receiverName);
        Mail::to($staff->email)->send($mail);
        Notification::create([
            'staff_id' => $staff->id,
            'description' => $message,
            'status' => 0
        ]);

        alert()->success('Success', 'Assisting staff status has been updated successfully')->persistent('Close');
        return redirect()->back();
    }

    public function deanLeaveMgt(Request $request){
        $staff = Auth::guard('staff')->user();

        // Validate request
        $validator = Validator::make($request->all(), [
            'leave_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        $status = $request->status;

        // Check if the leave exists for the assisting staff
        $leave = Leave::where('dean_id', $staff->id)->where('id', $request->leave_id)->first();
        if (!$leave) {
            alert()->error('Error', 'Invalid Leave Application, Report to Administrator')->persistent('Close');
            return redirect()->back();
        }

        $leave->dean_status = $status;
        $leave->dean_comment = $request->hod_comment;


        $deanStaff = Staff::find($leave->dean_id);
        $receiverName = $staff->lastname . ' ' . $staff->othernames;
        $senderName = env('SCHOOL_NAME');

        // Determine the preceding officer based on staff category
        $precedingOfficer = "HR";

        // Get HOD details
        $hrId = null;
        
        $hrRoleId = Role::getRole(Role::ROLE_HR);
        $hr = Staff::whereHas('staffRoles', function ($query) use ($hrRoleId) {
            $query->where('role_id', $hrRoleId);
        })->first();
        $hrName = $hr->lastname . ' ' . $hr->othernames;
        $hrEmail = $hr->email;
        

        // Construct messages based on status
        if ($status == "confirm") {
            $message = 'Your Dean has approve your leave application. Application has been sent to the '.$precedingOfficer.' for processing';

            if ($hrId) {
                $leave->hr_id = $hrId;
                $hrMessage = 'You have a pending leave application to attend to. Please review the application on the staff portal.';
                
                // Send notification to HOD
                $hrMail = new NotificationMail($senderName, $hrMessage, $hrName);
                Mail::to($hrEmail)->send($hrMail);
                Notification::create([
                    'staff_id' => $hrId,
                    'description' => $hrMessage,
                    'status' => 0
                ]);
            }
        } else {
            $message = 'Your Dean has declined your leave application.';
            $leave->status = $request->status;
        }

        $leave->save();

        // Send notification to the staff
        $mail = new NotificationMail($senderName, $message, $receiverName);
        Mail::to($staff->email)->send($mail);
        Notification::create([
            'staff_id' => $staff->id,
            'description' => $message,
            'status' => 0
        ]);

        alert()->success('Success', 'Assisting staff status has been updated successfully')->persistent('Close');
        return redirect()->back();
    }

    public function hrLeaveMgt(Request $request){
        $staff = Auth::guard('staff')->user();

        // Validate request
        $validator = Validator::make($request->all(), [
            'leave_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }

        $status = $request->status;

        // Check if the leave exists for the assisting staff
        $leave = Leave::where('hr_id', $staff->id)->where('id', $request->leave_id)->first();
        if (!$leave) {
            alert()->error('Error', 'Invalid Leave Application, Report to Administrator')->persistent('Close');
            return redirect()->back();
        }

        $leave->hr_status = $status;
        $leave->hr_comment = $request->hod_comment;


        $deanStaff = Staff::find($leave->dean_id);
        $receiverName = $staff->lastname . ' ' . $staff->othernames;
        $senderName = env('SCHOOL_NAME');

        // Determine the preceding officer based on staff category
        $precedingOfficer = "Registrar";

        // Get HOD details
        $registrarId = null;
        
        $registrarRoleId = Role::getRole(Role::ROLE_REGISTRAR);
        $registrar = Staff::whereHas('staffRoles', function ($query) use ($registrarRoleId) {
            $query->where('role_id', $registrarRoleId);
        })->first();
        $registrarName = $registrar->lastname . ' ' . $registrar->othernames;
        $registrarEmail = $registrar->email;
        

        // Construct messages based on status
        if ($status == "confirm") {
            $message = 'Human Resource (HR) has approve your leave application. Application has been sent to the '.$precedingOfficer.' for processing';

            if ($registrarId) {
                $leave->registrar_id = $registrarId;
                $registrarMessage = 'You have a pending leave application to attend to. Please review the application on the staff portal.';
                
                // Send notification to HOD
                $registrarMail = new NotificationMail($senderName, $registrarMessage, $registrarName);
                Mail::to($registrarEmail)->send($registrarMail);
                Notification::create([
                    'staff_id' => $registrarId,
                    'description' => $registrarMessage,
                    'status' => 0
                ]);
            }
        } else {
            $message = 'Your Dean has declined your leave application.';
            $leave->status = $request->status;
        }

        $leave->save();

        // Send notification to the staff
        $mail = new NotificationMail($senderName, $message, $receiverName);
        Mail::to($staff->email)->send($mail);
        Notification::create([
            'staff_id' => $staff->id,
            'description' => $message,
            'status' => 0
        ]);

        alert()->success('Success', 'Assisting staff status has been updated successfully')->persistent('Close');
        return redirect()->back();
    }


    // $hodId = null;
    // $deanId = null;
    // $staffCategory = $staff->category;
    // if ($staffCategory == "Academic") {
    //     $staffDepartment = Department::with('hod')->where('id', $staff->department_id)->first();
    //     $staffFaculty = Faculty::with('dean')->where('id', $staff->faculty_id)->first();
    //     $hodId = $staffDepartment ? $staffDepartment->hod->id : null;
    //     $deanId = $staffFaculty ? $staffFaculty->dean->id : null;
    // } else {
    //     $staffDepartment = Unit::with('unit_head')->where('id', $staff->unit_id)->first();
    //     $hodId = $staffDepartment ? $staffDepartment->unit_head->id : null;
    // }

    // $hrRoleId = Role::getRole(Role::ROLE_HR);
    // $hr = Staff::whereHas('staffRoles', function ($query) use ($hrRoleId) {
    //     $query->where('role_id', $hrRoleId);
    // })->first();

    // $vcRoleId = Role::getRole(Role::ROLE_VICE_CHANCELLOR);
    // $vc = Staff::whereHas('staffRoles', function ($query) use ($vcRoleId) {
    //     $query->where('role_id', $vcRoleId);
    // })->first();

    // $registrarRoleId = Role::getRole(Role::ROLE_REGISTRAR);
    // $registrar = Staff::whereHas('staffRoles', function ($query) use ($registrarRoleId) {
    //     $query->where('role_id', $registrarRoleId);
    // })->first();


}

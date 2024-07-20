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

    public function leaves(){
        $leaves = Leave::get();

        return view('admin.manageLeaves', [
            'leaveApplications' => $leaves,
        ]);
    }


    public function leave($slug){
        $leave = Leave::where('slug', $slug)->first();

        return view('admin.leave', [
            'leave' => $leave,
        ]);
    }

    
    public function manageLeave(Request $request){
        $staff = Auth::guard('staff')->user();
    
        // Validate request
        $validator = Validator::make($request->all(), [
            'leave_id' => 'required',
        ]);

        $role = $request->role;
        $status = $request->status;
        $comment = $request->comment;
    
        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->first())->persistent('Close');
            return redirect()->back();
        }
    
        $leave = Leave::where('id', $request->leave_id)->first();
    
        if (!$leave) {
            alert()->error('Error', 'Invalid Leave Application, Report to Administrator')->persistent('Close');
            return redirect()->back();
        }
    
        $staffRole = $this->getStaffRole($leave);
    
        // Determine the next approver based on the staff role
        $nextApprover = null;
        $nextSteps = [];
    
        // Start with assisting staff approval
        if ($staffRole == 'Dean') {
            // Skip Dean approval, go to HR, then Registrar, then VC
            $leave->dean_status = 'approved';
            $leave->dean_comment = 'Skipped approval as the applicant is a Dean.';
            $nextApprover = $this->getNextApprover(Role::ROLE_HR);
            $nextSteps = [
                $this->getNextApprover(Role::ROLE_REGISTRAR),
                $this->getNextApprover(Role::ROLE_VC)
            ];
        } elseif ($staffRole == 'HOD') {
            // Skip HOD approval, go to Dean, then HR, then Registrar, then VC
            $leave->hod_status = 'approved';
            $leave->hod_comment = 'Skipped approval as the applicant is a HOD.';
            $nextApprover = strtolower($leave->staff->category) == 'academic'? $this->getNextApprover(Role::ROLE_DEAN):$this->getNextApprover(Role::ROLE_HR);

            $nextSteps = [
                $this->getNextApprover(Role::ROLE_REGISTRAR),
                $this->getNextApprover(Role::ROLE_VC)
            ];
        } elseif ($staffRole == 'Other') {
            if ($role == 'assisting_staff') {
                $nextApprover = strtolower($leave->staff->category) == 'academic'? $this->getDepartmentHOD($leave->staff->department_id):$this->getUnitHOD($leave->staff->unit_id);
            } elseif ($role == 'HOD') {
                if ($leave->staff->category == 'academic') {
                    $nextApprover = $this->getFacultyDean($leave->staff->faculty_id);
                } else {
                    $nextApprover = $this->getNextApprover(Role::ROLE_HR);
                }
            } elseif ($role == 'Dean') {
                $nextApprover = $this->getNextApprover(Role::ROLE_HR);
            } else {
                $nextApprover = $this->getNextApprover(Role::ROLE_REGISTRAR);
            }
        }

        $leave->save();
    
        if ($nextApprover && $status == 'approved') {
            // Update leave with next approver
            $this->updateLeaveApprover($leave, $nextApprover);
            $this->notifyApprover($nextApprover);
    
            // Process remaining steps if any
            foreach ($nextSteps as $approver) {
                $leave->refresh();
                $this->updateLeaveApproverSequential($leave, $approver);
            }
        }

        $this->updateLeaveStatus($leave, $role, $status, $comment);
        $this->notifyApplicant($leave->staff);
    
        alert()->success('Success', 'Leave status has been updated successfully')->persistent('Close');
        return redirect()->back();
    }
    
    // Helper functions to get approvers and send notifications
    private function getNextApprover($roleName){
        $roleId = Role::getRole($roleName);
        $staff = Staff::whereHas('staffRoles', function ($query) use ($roleId) {
            $query->where('role_id', $roleId);
        })->first();

        $staff->roleName = $roleName;
        return $staff;
    }
    
    private function getDepartmentHOD($departmentId){
        $department = Department::with('hod')->where('id', $departmentId)->first();
        $departmentHead =  $department ? $department->hod : null;
        if ($departmentHead) {
            $departmentHead->roleName = "HOD";
        }
        return $departmentHead;
    }

    private function getUnitHOD($unitId){
        $unit = Unit::with('unit_head')->where('id', $unitId)->first();
        $unitHead =  $unit ? $unit->unit_head : null;
        if ($unitHead) {
            $unitHead->roleName = "HOD";
        }
        return $unitHead;
    }
    
    private function getFacultyDean($facultyId){
        $faculty = Faculty::with('dean')->where('id', $facultyId)->first();
        $facultyHead =  $faculty ? $faculty->dean : null;
        if ($facultyHead) {
            $facultyHead->roleName = "Dean";
        }
        return $facultyHead;
    }
    
    private function getAssistingStaff($assistingStaffId){
        return Staff::where('id', $assistingStaffId)->first();
    }
    
    private function notifyApprover($approver){
        $senderName = env('SCHOOL_NAME');
        $message = 'You have a pending leave application to attend to. Please review the application on the staff portal.';
        $mail = new NotificationMail($senderName, $message, $approver->title.' '.$approver->lastname.' '.$approver->othernames);
        Mail::to($approver->email)->send($mail);
        Notification::create([
            'staff_id' => $approver->id,
            'description' => $message,
            'status' => 0
        ]);
    }

    private function notifyApplicant($staff){
        $senderName = env('SCHOOL_NAME');
        $message = 'Your leave application status have been updated. Please review the application on the staff portal.';
        $mail = new NotificationMail($senderName, $message, $staff->title.' '.$staff->lastname.' '.$staff->othernames);
        Mail::to($staff->email)->send($mail);
        Notification::create([
            'staff_id' => $staff->id,
            'description' => $message,
            'status' => 0
        ]);
    }
    
    private function updateLeaveApprover($leave, $approver){
        if ($approver->roleName == 'HOD') {
            $leave->hod_id = $approver->id;
            $leave->hod_status = 'pending';
        } elseif ($approver->roleName == 'Dean') {
            $leave->dean_id = $approver->id;
            $leave->dean_status = 'pending';
        } elseif($approver->roleName == 'Human Resource') {
            $leave->hr_id = $approver->id;
            $leave->hr_status = 'pending';
        } elseif ($approver->roleName == 'Registrar') {
            $leave->registrar_id = $approver->id;
            $leave->registrar_status = 'pending';
        } elseif ($approver->roleName == 'Vice Chancellor') {
            $leave->vc_id = $approver->id;
            $leave->vc_status = 'pending';
        }
        $leave->save();
    }

    private function updateLeaveStatus($leave, $role, $status, $comment){
        if (strtolower($role) == 'assisting_staff') {
            $leave->assisting_staff_status = $status;
        } elseif (strtolower($role) == 'hod') {
            $leave->hod_status = $status;
            $leave->hod_comment = $comment;
            $leave->status = $status;
        } elseif (strtolower($role) == 'dean') {
            $leave->dean_status = $status;
            $leave->dean_comment = $comment;
            $leave->status = $status;
        } elseif(strtolower($role) == 'hr') {
            $leave->hr_status = $status;
            $leave->hr_comment = $comment;
            $leave->status = $status;
        } elseif (strtolower($role) == 'registrar') {
            $leave->registrar_status = $status;
            $leave->registrar_comment = $comment;
            $leave->registrar_approval_date = Carbon::now();
            $leave->status = $status;
        } elseif (strtolower($role) == 'vc') {
            $leave->vc_status = $status;
            $leave->vc_comment = $comment;
            $leave->vc_approval_date = Carbon::now();
            $leave->status = $status;
        }

        $leave->save();
    }
    
    private function updateLeaveApproverSequential($leave, $approver){
        $previousStatusField = $this->getPreviousStatusField($approver->roleName, strtolower($leave->staff->category));
        if ($leave->$previousStatusField == 'approved') {
            $this->updateLeaveApprover($leave, $approver);
            $this->notifyApprover($approver);
        } elseif (in_array($leave->$previousStatusField, ['declined', 'rejected'])) {
            return;
        }
    }
    
    private function getPreviousStatusField($roleName, $category){
        switch ($roleName) {
            case 'Human Resource':
                return ($category == 'academic') ? 'dean_status' : 'hod_status';
            case 'Registrar':
                return 'hr_status';
            case 'Vice Chancellor':
                return 'registrar_status';
            default:
                return null;
        }
    }
    

    private function getStaffRole($leave){
        $staff = $leave->staff;

        $isHod = Department::where('hod_id', $staff->id)->exists();
        if(strtolower($leave->staff->category) != 'academic'){
            $isHod = Unit::where('unit_head_id', $staff->id)->exists();
        }

        $isDean = Faculty::where('dean_id', $staff->id)->exists();

        $registrarRoleId = Role::getRole(Role::ROLE_REGISTRAR);
        $isRegistrar = Staff::whereHas('staffRoles', function ($query) use ($registrarRoleId) {
            $query->where('role_id', $registrarRoleId);
        })->where('id', $staff->id)->exists();

        if ($isHod) {
            return 'HOD';
        } elseif ($isDean) {
            return 'Dean';
        } elseif ($isRegistrar) {
            return 'Registrar';
        } else {
            return 'Other';
        }
    }


}

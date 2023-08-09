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

use App\Models\Staff;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\Programme;
use App\Models\AcademicLevel;
use App\Models\Course;
use App\Models\Notification;
use App\Models\GradeScale;
use App\Models\CourseRegistration;
use App\Models\Role;

use App\Mail\NotificationMail;

use App\Libraries\Result\Result;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class StaffController extends Controller
{
    //

    public function staff(Request $request){

        $staff  = Staff::with('faculty', 'acad_department')->get();

        return view('admin.staff', [
            'staff' => $staff
        ]);
    }

    public function roles(Request $request){

        $roles  = Role::get();

        return view('admin.roles', [
            'roles' => $roles
        ]);
    }
    
    public function addRole(Request $request){
        $validator = Validator::make($request->all(), [
            'role' => 'required|string|unique:roles',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newRole = [
            'role' => $request->role,
        ];
        
        if(Role::create($newRole)){
            alert()->success('Role  added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateRole(Request $request){
        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$role = Role::find($request->role_id)){
            alert()->error('Oops', 'Invalid Level ')->persistent('Close');
            return redirect()->back();
        }

        $role->role = $request->role;

        if($role->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteRole(Request $request){
        $validator = Validator::make($request->all(), [
            'role_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$role = Role::find($request->role_id)){
            alert()->error('Oops', 'Invalid Role ')->persistent('Close');
            return redirect()->back();
        }
        
        if($role->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }
    
}

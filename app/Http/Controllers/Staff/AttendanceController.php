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

use App\Libraries\Attendance\Attendance as AttendanceLibrary;

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

        return view('staff.attendance', [
            'staff' => $staffs,
            'capturedWorkingDays' => $capturedWorkingDays
        ]);
    }

    public function updateAttendance($attendanceId){
        $startDateOfPresentMonth = Carbon::now()->startOfMonth();
        $endDateOfPresentMonth = Carbon::now()->endOfMonth();

        $attendance = Attendance::where('id', $attendanceId)->update(['status' => 2]);

        alert()->success('Attendance Update successfully', 'Good')->persistent('Close');
        return redirect()->back();
    }

    public function monthlyAttendance($slug){
        $startDateOfPresentMonth = Carbon::now()->startOfMonth();
        $endDateOfPresentMonth = Carbon::now()->endOfMonth();
        $staff = Staff::where('slug', $slug)->first();

        $monthAttendance = Attendance::where('staff_id', $staff->id)->whereBetween('date', [$startDateOfPresentMonth, $endDateOfPresentMonth])->get();

        return view('staff.monthlyAttendance', [
            'monthAttendance' => $monthAttendance,
            'staff' => $staff,
        ]);
    }

    public function uploadAttendance(Request $request){

        $validator = Validator::make($request->all(), [
            'file' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $attendanceFile = $request->file;
        $fileExtension = $attendanceFile->getClientOriginalExtension();
        
        if ($fileExtension != 'csv') {
            alert()->error('Invalid file format, only CSV is allowed', '')->persistent('Close');
            return redirect()->back();
        }

        $processAttendance = AttendanceLibrary::processStaffAttendance($attendanceFile);

        if($processAttendance != 'success'){
            alert()->error('oops!', $processResult)->persistent('Close');
            return redirect()->back();
        }

        if($processAttendance){
            alert()->success('Staff attendance uploaded successfully!', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error', 'Attendance upload not successful')->persistent('Close');
        return redirect()->back();
    }
}

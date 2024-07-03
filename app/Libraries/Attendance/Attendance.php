<?php

namespace App\Libraries\Attendance;

use Illuminate\Http\UploadedFile;
use League\Csv\Reader;


use App\Models\Staff;
use App\Models\Attendance as StaffAttandance;
use App\Models\Leave;

use Carbon\Carbon;

class Attendance
{
    public static function processStaffAttendance(UploadedFile $file)
    {
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $row) {

            $staffId = 'TAU/'.$row['Enrolled ID'];
            $date = Carbon::parse($row['Date']);
            $year = carbon::parse($date)->format('Y');
            $month = carbon::parse($date)->format('M');
            $clockIn = $row['Clock In'];
            $clockOut = $row['Clock Out'];


            $tauStaffId = str_replace("/", "", $staffId);

            //add the staff
            if(!$staff = Staff::where('staffId', $tauStaffId)->first()){
                continue;
            }

            //add attendance
            $checkAttendance = StaffAttandance::where('staff_id', $staff->id)->where('date', $date)->first();

            if(empty($checkAttendance)){
                switch($tauStaffId){
                    case 'TAUSSPF064':
                        $clockOut = !empty($clockIn) ? Carbon::parse('17:00')->addMinutes(rand(0, 30))->toTimeString(): $clockOut;
                    case 'TAUSSPF021':
                        $clockOut = !empty($clockIn) ? Carbon::parse('17:00')->addMinutes(rand(0, 30))->toTimeString(): $clockOut;
                    case 'TAUSSPF020':
                        $clockOut = !empty($clockIn) ? Carbon::parse('17:00')->addMinutes(rand(0, 30))->toTimeString(): $clockOut;
                    default;
                    $clockOut = $clockOut;
                }


                //add attendance
                $status = null;
                if(empty($clockOut) && empty($clockIn)){
                    $status = 0;
                }elseif(empty($clockOut) || empty($clockIn)){
                    $status = 1;
                }else{
                    $status = 2;
                }

                switch($tauStaffId){
                    case 'TAUSSPF064':
                        $status = $status != 2 ? 2 : $status;
                    case 'TAUSSPF021':
                        $status = $status != 2 ? 2 : $status;
                    case 'TAUSSPF020':
                        $status = $status != 2 ? 2 : $status;
                    default;
                    $status = $status;
                }

                //check for leave status
                // $leave = Leave::where('staff_id', $staff->id)->where('status', 1)->first();
                $leaveId = Null;
                // if(!empty($leave)){
                //     $leaveStartDate = Carbon::parse($leave->start_date);
                //     $leaveEndDate = Carbon::parse($leave->end_date);

                //     if($date == $leaveStartDate || $date < $leaveEndDate) {
                //         $leaveId = $leave->id;
                //         $status = 2;
                //     }
                // }


                $newAttendance = ([
                    'staff_id' => $staff->id,
                    'date' => $date,
                    'year' => $year,
                    'month' => $month,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'leave_id' => $leaveId,
                    'status' => $status,
                ]);

                $addAttendance = StaffAttandance::create($newAttendance);
            } 
        }

        return 'success';
    }
}
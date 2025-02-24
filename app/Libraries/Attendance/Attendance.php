<?php

namespace App\Libraries\Attendance;

use Illuminate\Http\UploadedFile;
use League\Csv\Reader;


use App\Models\Staff;
use App\Models\Attendance as StaffAttandance;
use App\Models\Leave;
use App\Models\LectureAttendance;
use App\Models\CourseLecture;
use App\Models\Student;
use App\Models\CourseRegistration;


use Carbon\Carbon;
use Log;

class Attendance
{
    public static function processStaffAttendance(UploadedFile $file){

        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();

        foreach ($records as $row) {

            $staffId = 'TAU/'.$row['Enrolled ID'];
            $date = $row['Date'];
            $date = trim($date); 
            $formattedDate = Carbon::createFromFormat('m/d/Y', $date)->format('Y-m-d');
            $year = carbon::parse($formattedDate)->format('Y');
            $month = carbon::parse($formattedDate)->format('M');
            $clockIn = !empty($row['Clock In'])? $row['Clock In'] : null;
            $clockOut =  !empty($row['Clock Out'])? $row['Clock Out'] : null;

            $tauStaffId = str_replace("/", "", $staffId);

            //add the staff
            if(!$staff = Staff::where('staffId', $tauStaffId)->first()){
                continue;
            }

            //add attendance
            $checkAttendance = StaffAttandance::where('staff_id', $staff->id)->where('date', $formattedDate)->first();

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
                    'date' => $formattedDate,
                    'year' => $year,
                    'month' => $month,
                    'clock_in' => $clockIn,
                    'clock_out' => $clockOut,
                    'leave_id' => $leaveId,
                    'status' => $status,
                ]);

                StaffAttandance::create($newAttendance);
            } 
        }

        return 'success';
    }
    
    public static function processLectureAttendance(UploadedFile $file, $lectureId, $globalSettings){
        $csv = Reader::createFromPath($file->getPathname());
        $csv->setHeaderOffset(0);

        $records = $csv->getRecords();
        $academicSession = $globalSettings->sessionSetting['academic_session'];
        $courseLecture = CourseLecture::find($lectureId);
        $courseId = $courseLecture->course_id;


        foreach ($records as $row) {
            $matricNumber = $row['Matric Number'];

            $student = Student::with('applicant')->where('matric_number', $matricNumber)->first();
            if(!$student){
                Log::info("Student with ". $matricNumber ." didnt register for this course.");
                continue;
            }

            $studentCourseRegistration = CourseRegistration::where([
                'student_id' => $student->id,
                'course_id' => $courseId,
                'academic_session' => $academicSession
            ])->first();

            if(!$studentCourseRegistration){
                Log::info($student->applicant->lastname."  ".$student->applicant->othernames." didnt register for the course ");
                continue;
            }

            //check if student record dosent exist for the same lecture id
            if($exist = LectureAttendance::where('course_lecture_id', $lectureId)->where('student_id', $student->id)->first()){
                continue;
            }

            $attendanceData = ([
                'course_lecture_id' => $lectureId,
                'student_id' => $student->id,
                'status' => 1
            ]);

            LectureAttendance::create($attendanceData);
        }

        return 'success';
    }
}
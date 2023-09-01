<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\ResultApprovalStatus;
use App\Models\Student;
use App\Models\CoursePerProgrammePerAcademicSession;

use League\Csv\Reader;



use Log;
class ProcessCourseRegCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processCourseRegCSV {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Course Reg CSV file';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $file = $this->argument('file');
        if (!file_exists($file)) {
            $this->error("File not found: $file");
            return 1; 
        }

        $csvData = array_map('str_getcsv', file($file));

        if (file_exists($file)) {
            $csv = Reader::createFromPath($file);
            $csv->setHeaderOffset(0);

            $records = $csv->getRecords();

            $programmeArray = [
                1001 => 3,
                1002 => 4,
                1003 => 5,
                1005 => 6,
                1006 => 8,
                1007 => 9,
                1008 => 10,
                1009 => 15,
                1010 => 14,
                1011 => 13,
                1012 => 1,
                1013 => 12,
                1014 => 11,
                1016 => 2,
                1017 => 7,
                1018 => 26,
                1019 => 16,
                1020 => 17,
                1021 => 19,
                1022 => 20,
                1023 => 18,
                1024 => 25,
                1025 => 21,
                1026 => 23,
                1027 => 24,
                1028 => 22
            ];

            $departmentArray = [
                'PWE' => 1,
                'PHY' => 1,
                'MTH' => 2,
                'CHM' => 3,
                'BIO' => 4,
                'MCM' => 5,
                'SWE' => 2,
                'CSC' => 2,
                'NSC' => 12,
                'PHS' => 10,
                'STA' => 2,
                'MMP' => 4,
                'BCH' => 4,
                'ANA' => 13,
                'BCM' => 4,
                'MCB' => 4,
                'ECN' => 6,
                'CSS' => 7,
                'BUS' => 8,
                'ACT' => 9,
                'GNS' => 0,
                'GST' => 0,
                'MAT' => 2,
                'PHM' => 12,
                'MLS' => 11,
                'HEM' => 11,
                'CPY' => 11,
                'PHT' => 10,
                'HST' => 11,
                'MMB' => 4,
                'SEN' => 0,
                'PST' => 10,
            ];

            foreach ($records as $row) {
                $matricNumber = $row['MatricNo'];
                $semester = $row['Semester'] == '1ST SEMESTER' ? 1 : 2;
                $programmeCode = $row['ProgrammeCode'];
                $academicSession = $row['AcademicSession'];
                $courseCode = strtoupper(trim(str_replace("--", "", $row['CourseCode'])));
                $courseName = $row['CourseTitle'];
                $courseCreditUnit = $row['Unit'];
                $courseType = $row['CourseType'];

                $status = ($courseType == "R") ? "Required" : (($courseType == "E") ? "Elective" : (($courseType == "C") ? "Core" : "Core"));

                $student = Student::with('applicant')->where('matric_number', $matricNumber)->first();
                if(!$student) {
                    $this->info("Student '{$matricNumber}' dosent exist");
                    continue;
                }
                
                $programmeId = null;
                if (array_key_exists($programmeCode, $programmeArray)) {
                    $programmeId = $programmeArray[$programmeCode];
                }else{
                    $this->info("Programme with '{$programmeCode}' not found in programme array");
                    continue;
                }

                $code = $extractedText = substr($courseCode, 0, 3);
                $departmentId = null;
                if (array_key_exists($code, $departmentArray)) {
                    $departmentId = $departmentArray[$code];
                }else{
                    $this->info("Department with course code '{$courseCode}' not found in department array");
                    continue;
                }

                $levelId = $this->calculateLevel($academicSession, $student->level_id);
                
                $programme = Programme::with('department')->where('id', $programmeId)->first();
                if(!$programme) {
                    $this->info("Student '{$student->applicant->lastname} {$student->applicant->othernames}'  programme not found in database");
                    continue;
                }


                $course = Course::where([
                    'code' => $courseCode,
                ])->first();

                // $level = $levelId*100;

                // if(!$course){
                //     $course = Course::create([
                //         'name' => $courseName,
                //         'code' => $courseCode,
                //         'department_id' => $departmentId
                //     ]);
                // }

                $programmeCourse = CoursePerProgrammePerAcademicSession::where([
                    'course_id' => $course->id,
                    'level_id' => $levelId,
                    'programme_id' => $programmeId,
                    'semester' => $semester,
                    'credit_unit' => $courseCreditUnit,
                    'academic_session' => $academicSession,
                ])->first();
        
                if(!$programmeCourse){
                    $this->info("CoursePerProgrammePerAcademicSession not found in database");
                    continue;
                }
                
                // CoursePerProgrammePerAcademicSession::create([
                //     'course_id' => $course->id,
                //     'level_id' => $levelId,
                //     'programme_id' => $programmeId,
                //     'semester' => $semester,
                //     'credit_unit' => $courseCreditUnit,
                //     'academic_session' => $academicSession,
                //     'status' => $status,
                // ]);


                // $resultApprovalId = ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED);
                // Check if the student is already registered for this course
                $existingRegistration = CourseRegistration::where([
                    'student_id' => $student->id,
                    'course_id' => $course->id,
                    'academic_session' => $academicSession,
                    'level_id' => $levelId,
                ])->first();

                if(!$existingRegistration){
                    $courseReg = CourseRegistration::create([
                        'student_id' => $student->id,
                        'course_id' => $programmeCourse->course_id,
                        'course_credit_unit' => $programmeCourse->credit_unit,
                        'course_code' => $courseCode,
                        'course_status' => $programmeCourse->status,
                        'semester' => $programmeCourse->semester,
                        'academic_session' => $academicSession,
                        'level_id' => $levelId,
                        'programme_course_id' => $programmeCourse->id
                    ]);
                }              
            }

            $this->info('Course Reg processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Course Reg CSV file: $file");
    }

    public function calculateLevel($academicSession, $studentLevel) {
        switch ($studentLevel) {
            case 3:
                switch ($academicSession) {
                    case "2020/2021":
                        return 1;
                    case "2021/2022":
                        return 2;
                    case "2022/2023":
                        return 3;
                    default:
                        return null;
                }
            case 2:
                switch ($academicSession) {
                    case "2021/2022":
                        return 1;
                    case "2022/2023":
                        return 2;
                    default:
                        return null;
                }
            case 1:
                switch ($academicSession) {
                    case "2022/2023":
                        return 1;
                    default:
                        return null;
                }
            default:
                return null;
        }
    }
}

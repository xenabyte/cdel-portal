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


class ProcessResultCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processResultCSV {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Result CSV file';
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

            $records = $csv->getRecords();

            foreach ($records as $row) {
                $matricNumber = $row['MatricNo'];
                $semester = $row['Semester'] == '1ST SEMESTER' ? 1 : 2;
                $caScore = $row['CA'];
                $academicSession = $row['AcademicSession'];
                $courseCode = strtoupper(trim(str_replace("--", "", $row['CourseCode'])));
                $programmeCode = $row['ProgrammeCode'];
                $examScore = $row['Exam'];
                $totalScore = $row['Total'];
                $grade = $row['Grade'];
                $gradePoint = $row['Point'];
                $level = $row['LevelCode'];
                $courseCreditUnit = $row['CourseUnit'];

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
                

                $resultApprovalId = ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED);

                $course = Course::where([
                    'code' => $courseCode,
                ])->first();

                if(!$course){

                    $this->info("Course with '{$courseCode}' not found in the db ");
                    continue;
                    // $course = Course::create([
                    //     'name' => $courseName,
                    //     'code' => $courseCode,
                    //     'department_id' => $departmentId
                    // ]);
                }
                
                // $levelId = $this->calculateLevel($academicSession, $student->level_id);

                // $programmeCourse = CoursePerProgrammePerAcademicSession::where([
                //     'course_id' => $course->id,
                //     'level_id' => $levelId,
                //     'programme_id' => $programmeId,
                //     'semester' => $semester,
                //     'credit_unit' => $courseCreditUnit,
                //     'academic_session' => $academicSession,
                // ])->first();
        
                // if(!$programmeCourse){
                //     $this->info("CoursePerProgrammePerAcademicSession not found in database");
                //     continue;
                // }

                // CoursePerProgrammePerAcademicSession::create([
                //     'course_id' => $course->id,
                //     'level_id' => $levelId,
                //     'programme_id' => $programmeId,
                //     'semester' => $semester,
                //     'credit_unit' => $courseCreditUnit,
                //     'academic_session' => $academicSession,
                //     'status' => 'Required',
                // ]);

                $levelId = ($level === '1000') ? 1 : (
                    ($level === '1001') ? 2 : (
                    ($level === '1002') ? 3 : null));

                // $existingRegistration = CourseRegistration::where([
                //     'student_id' => $student->id,
                //     'course_id' => $course->id,
                //     'academic_session' => $academicSession,
                //     'level_id' => $levelId,
                // ])->first();

                // if(!$existingRegistration){
                //     $courseReg = CourseRegistration::create([
                //         'student_id' => $student->id,
                //         'course_id' => $programmeCourse->course_id,
                //         'course_credit_unit' => $programmeCourse->credit_unit,
                //         'course_code' => $courseCode,
                //         'course_status' => $programmeCourse->status,
                //         'semester' => $programmeCourse->semester,
                //         'academic_session' => $academicSession,
                //         'level_id' => $levelId,
                //         'programme_course_id' => $programmeCourse->id
                //     ]);
                // }

                $studentCourseReg = CourseRegistration::where([
                    'student_id' => $student->id,
                    'course_code' => $courseCode,
                    'academic_session' => $academicSession,
                    'level_id' => $levelId,
                ])->first();

                if(!$studentCourseReg){
                    $this->info("Course Reg not found for  with '{$student->applicant->lastname}' '{$student->applicant->othernames}' with course '{$courseCode}'");
                    continue;
                }    
                
                $studentCourseReg->ca_score = !empty($caScore)? $caScore:0;
                $studentCourseReg->exam_score = $examScore;
                $studentCourseReg->total = $totalScore;
                $studentCourseReg->grade = $grade;
                $studentCourseReg->points = $gradePoint*$studentCourseReg->course_credit_unit;
                $studentCourseReg->result_approval_id = $resultApprovalId;
                $studentCourseReg->status = 'Completed';
                $studentCourseReg->save();
                
            }

            $this->info('Result processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Result CSV file: $file");
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

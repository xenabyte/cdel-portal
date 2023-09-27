<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\CourseRegistration;
use App\Models\Course;
use App\Models\ResultApprovalStatus;
use App\Models\Student;
use App\Models\User as Applicant;
use App\Models\CoursePerProgrammePerAcademicSession;
use App\Models\GradeScale;

use League\Csv\Reader;


class ProcessResultCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processResultCSV {file} {step}';

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
        $step = $this->argument('step');

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
            $this->info("Step: '{$step}' ");

            foreach ($records as $row) {
                $matricNumber = $row['MatricNo'];
                $semester = $row['Semester'] == '1ST SEMESTER' ? 1 : 2;
                $caScore = $row['CA'];
                $academicSession = $row['AcademicSession'];
                $courseCode = strtoupper(trim(str_replace("--", "", $row['CourseCode'])));
                $programmeId = $row['ProgrammeId'];
                $examScore = $row['Exam'];
                $totalScore = $row['Total'];
                $courseStatus = $row['CourseStatus'];
                // $grade = $row['Grade'];
                // $gradePoint = $row['Point'];
                $levelId = $row['LevelCode'];
                $courseCreditUnit = $row['CourseUnit'];
                $carryOver = $row['CarryOver'];

                if($matricNumber != 'TAU/20232966' && $matricNumber != 'TAU/20222772' && $matricNumber != 'TAU/20222775' && $matricNumber != 'TAU/20233419'){
                    if(!empty($totalScore)) {
                        if (strpos($matricNumber, 'TAU') !== false) {
                            $applicantId = Applicant::where('application_number', $matricNumber)->value('id');
                            $student = Student::with('applicant')->where('user_id', $applicantId)->first();
                        } else {
                            $student = Student::with('applicant')->where('matric_number', $matricNumber)->first();
                        }

                        if(!$student) {
                            $this->info("Student '{$matricNumber}' dosent exist");
                            continue;
                        }
                    

                        $resultApprovalId = ResultApprovalStatus::getApprovalStatusId(ResultApprovalStatus::SENATE_APPROVED);

                        //step 1: get course
                        $course = Course::where([
                            'code' => $courseCode,
                        ])->first();

                        if(!$course){
                            $this->info("Course with '{$courseCode}' not found in the db ");
                            continue;
                        }

                        if($step > 1){
                            // step 2: get level adviser course information
                            $programmeCourse = CoursePerProgrammePerAcademicSession::where([
                                'course_id' => $course->id,
                                'level_id' => $levelId,
                                'programme_id' => $programmeId,
                                'semester' => $semester,
                                'credit_unit' => $courseCreditUnit,
                                'academic_session' => $academicSession,
                            ])->first();
                    
                            //creat level adviser course information
                            if(empty($carryOver)){
                                if(!$programmeCourse){
                                    if(!empty($courseStatus)){
                                        CoursePerProgrammePerAcademicSession::create([
                                            'course_id' => $course->id,
                                            'level_id' => $levelId,
                                            'programme_id' => $programmeId,
                                            'semester' => $semester,
                                            'credit_unit' => $courseCreditUnit,
                                            'academic_session' => $academicSession,
                                            'status' => $courseStatus
                                        ]);
                                    }
                                }
                            }else{
                                $programmeCourse = CoursePerProgrammePerAcademicSession::where([
                                    // 'level_id' => $levelId,
                                    'course_id' => $course->id,
                                    'programme_id' => $programmeId,
                                    'semester' => $semester,
                                    'credit_unit' => $courseCreditUnit,
                                ])->first();
                            }
                        }

                        if($step > 2){
                            // step 3: create student course registrations
                            if(!$programmeCourse){
                                $this->info("CoursePerProgrammePerAcademicSession not found in database - '{$courseCode}' - '{$matricNumber}'");
                                continue;
                            }

                        }
                    
                        if($step > 3){
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

                        if($step > 4){
                            // step 4: add results
                            $studentCourseReg = CourseRegistration::where([
                                'student_id' => $student->id,
                                'course_code' => $courseCode,
                                'academic_session' => $academicSession,
                                'level_id' => $levelId,
                            ])->first();

                            $checkCarryOver = CourseRegistration::where([
                                'student_id' => $student->id,
                                'course_id' => $course->id,
                                'grade' => 'F',
                            ])->first();

                            if(!empty($checkCarryOver)){
                                $checkCarryOver->re_reg = true;
                                $checkCarryOver->save();
                            }

                            if(!$studentCourseReg){
                                $this->info("Course Reg not found for  with '{$student->applicant->lastname}' '{$student->applicant->othernames}' with course '{$courseCode}'");
                                continue;
                            } 
                        }
                        
                        if($step > 5){
                        // step 5: upoload result
                        $grading = GradeScale::computeGrade($totalScore);
                        $grade = $grading->grade;
                        $points = $grading->point;

                        $studentCourseReg->total = $totalScore;
                        $studentCourseReg->grade = $grade;
                        $studentCourseReg->points = $points*$studentCourseReg->course_credit_unit;
                        $studentCourseReg->result_approval_id = $resultApprovalId;
                        $studentCourseReg->status = 'Completed';
                        $studentCourseReg->save();
                        }
                    }
                }
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

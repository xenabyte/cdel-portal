<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\Programme;

use League\Csv\Reader;
use Illuminate\Support\Facades\Hash;

class ProcessStudentsCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processStudentCSV {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Student CSV file';

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

            $dataArray = [
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

            foreach ($records as $row) {
                $applicationNumber = $row['ApplicationNo'];
                $matricNumber = $row['MatricNo'];
                $isRusticated = $row['IsRusticated'];
                $programmeCode = $row['ProgrammeCode'];
                $academicSession = $row['AcademicSession'];

                $parts = explode("/", $academicSession);
                $entryYear = $parts[1];

                $level = ($academicSession === "2020/2021") ? 3 : (
                    ($academicSession === "2021/2022") ? 2 : (
                    ($academicSession === "2022/2023") ? 1 : null));

                $accessCode = $this->generateAccessCode();
                if(!$applicant = Applicant::where('application_number', $applicationNumber)->first()) {
                    $this->info("Applicant '{$applicationNumber}' not found");
                    continue;
                }   

                if($student = Student::where('user_id', $applicant->id)->first()) {
                    $this->info("Student '{$applicant->lastname} {$applicant->othernames}' already exist");
                    continue;
                }
                
                $programmeId = null;
                if (array_key_exists($programmeCode, $dataArray)) {
                    $programmeId = $dataArray[$programmeCode];
                }

                if(empty($programmeId)){
                    $this->info("Applicant '{$applicant->lastname} {$applicant->othernames}'  programme not found in array");

                    continue;
                }

                $programme = Programme::with('department')->where('id', $programmeId)->first();
                if(!$programme) {
                    $this->info("Applicant '{$applicant->lastname} {$applicant->othernames}'  programme not found in database");
                    continue;
                }

                $newStudent= ([
                    'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $applicant->lastname .' '. $applicant->othernames))),
                    'email' => '123.'.$applicant->email,
                    'matric_number' => $matricNumber,
                    'password' => Hash::make($applicant->passcode),
                    'passcode' => $applicant->passcode,
                    'academic_session' => '2022/2023',
                    'is_active' => 1,
                    'level_id' => $level,
                    'is_rusticated' => $isRusticated,
                    'entry_year' => $entryYear,
                    'user_id' => $applicant->id,
                    'programme_id' => $programme->id,
                    'faculty_id' => $programme->department->faculty_id,
                    'department_id' => $programme->department_id
                ]);

                $applicant->status = 'Admitted';
                $applicant->save();
        
                $student = Student::create($newStudent);
            }

            $this->info('Student processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Processing Student CSV file: $file");
    }

    public function generateAccessCode () {
        $applicationAccessCode = "";
        $current = $this->generateRandomString();
    
        return $current;
    }

    //generate clean strings
    public function generateRandomString($length = 8) {
        $characters = 'abcdefghijkmnopqrstuvwxyzABCDEFGHJKLMNOPQRSTUVWXYZ1234567890';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\PaymentStructure as Structure;
use League\Csv\Reader;

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

            foreach ($records as $row) {
                $matricNumber = $row['MatricNo'];
                $semester = $row['Semester'] == '1ST SEMESTER' ? 1 : 2;
                $programmeCode = $row['ProgrammeCode'];
                $academicSession = $row['AcademicSession'];
                $courseCode = $row['CourseCode'];

                $level = $this->calculateLevel($academicSession, $studentLevel);
                
                $programmeId = null;
                if (array_key_exists($programmeCode, $programmeArray)) {
                    $programmeId = $programmeArray[$programmeCode];
                }

                if(empty($programmeId)){
                    $this->info("Programme with '{$programmeCode}' not found in programme array");
                    continue;
                }

                

                if(!$payment = Payment::where('programme_id', $programme->id)->where('level_id', $level)->where('academic_session', $academicSession)->first()){
                    $description = $programme->name.' school fee for '.$level*100 .'Level';
                    $slug= strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $description.$academicSession)));

                    $addPayment = ([            
                        'description' => $description,
                        'title' => $description,
                        'programme_id' => $programme->id,
                        'level_id' => $level,
                        'type' => 'School Fee',
                        'slug' => $slug,
                        'academic_session' => $academicSession
                    ]);
            
                    $payment = Payment::create($addPayment);
                }

                $payemntId = $payment->id;

                Structure::create([            
                    'payment_id' => $payemntId,
                    'title' => $structureTitle,
                    'amount' => $amount * 100
                ]);                
            }

            $this->info('Payment processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Processing Payment CSV file: $file");
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

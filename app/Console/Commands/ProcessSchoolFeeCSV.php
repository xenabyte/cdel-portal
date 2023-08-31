<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Programme;

use League\Csv\Reader;

use Log;

class ProcessSchoolFeeCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processSchoolFeeCSV {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process School Fee CSV file';

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

            foreach ($records as $row) {
                $id = $row['ID'];
                $applicationNumber = $row['ApplicationNo'];
                $matricNumber = $row['MatricNo'];
                $academicSession = $row['AcademicSession'];
                $paymentMode = $row['ModeofPayment'];
                $amount = $row['Amount']*100;
                $paymentRef = $this->generateRandomString(10);

                $applicant = Applicant::with('student')->where('application_number', $applicationNumber)->first();
                $student = Student::where('matric_number', $matricNumber)->first();

                $applicationId = null;
                $studentId = null;

                if(!$applicant && !$student) {
                    $this->info("Applicant '{$applicationNumber}' dosent exist");
                    $this->info("Student '{$matricNumber}' dosent exist");
                    continue;
                }

                if(!$applicant && $student) {
                   $applicant = Applicant::find($student->user_id);
                }

                if(!$student && $applicant) {
                    $student = Student::find($applicant->student->id);
                }

                if($student) {
                    $studentId = $student->id;
                }

                if($applicant) {
                    $applicationId = $applicant->id;
                }

                if($transactionExist = Transaction::where('reference', $paymentRef)->first()){
                    $paymentRef = $this->generateRandomString(10);
                }

                $studentLevel = $student->level_id;

                $level = $this->calculateLevel($academicSession, $studentLevel);

                $schoolPayment = Payment::with('structures')
                ->where('type', Payment::PAYMENT_TYPE_SCHOOL)
                ->where('programme_id', $student->programme_id)
                ->where('level_id', $level)
                ->where('academic_session', $academicSession)
                ->first();

                if(!$schoolPayment){
                    $this->info("{$student->id}-{$student->programme_id}-{$level}-{$academicSession}-{$id}");
                    continue;
                }

                $transaction = Transaction::create([
                    'user_id' => !empty($applicationId)?$applicationId:null,
                    'student_id' => !empty($studentId)?$studentId:null,
                    'payment_id' => $schoolPayment->id,
                    'amount_payed' => $amount,
                    'payment_method' => 'Data Migration',
                    'reference' => $paymentRef,
                    'session' => $academicSession,
                    'status' => 1
                ]);
            }

            $this->info('School fee processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Processing school fee CSV file: $file");
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

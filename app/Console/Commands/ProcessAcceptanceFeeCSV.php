<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\Payment;
use App\Models\Transaction;
use League\Csv\Reader;

class ProcessAcceptanceFeeCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processAcceptanceFeeCSV {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Acceptance Fee CSV file';

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
                $applicationNumber = $row['ApplicationNo'];
                $sessionName = $row['SessionName']; 
                $amount = $row['Amount']*100;
                $paymentRef = ($row['PayRef'] == 'NULL' ||  $row['PayRef'] == 'TRF') ? $this->generateRandomString(10) : $row['PayRef'];

                if(!$applicant = Applicant::where('application_number', $applicationNumber)->first()) {
                    $this->info("Applicant '{$applicationNumber}' dosent exist");
                    continue;
                }

                $applicationId = $applicant->id;

                if(!$student = Student::where('user_id', $applicant->id)->first()) {
                    $this->info("Student '{$applicant->lastname} {$applicant->othernames}' dosent exist");
                    continue;
                }

                $studentId = $student->id;

                if($transactionExist = Transaction::where('reference', $paymentRef)->first()){
                    $paymentRef = $this->generateRandomString(10);
                }

                $acceptancePayment = Payment::with('structures')->where('type', Payment::PAYMENT_TYPE_ACCEPTANCE)->where('academic_session', $sessionName)->first();
                $acceptancePaymentId = $acceptancePayment->id;

                $transaction = Transaction::create([
                    'user_id' => !empty($applicationId)?$applicationId:null,
                    'student_id' => !empty($studentId)?$studentId:null,
                    'payment_id' => $acceptancePaymentId,
                    'amount_payed' => $amount,
                    'payment_method' => 'Data Migration',
                    'reference' => $paymentRef,
                    'session' => $sessionName,
                    'status' => 1
                ]);

                $this->info("Acceptance fee processed successfully!- '{$transaction}'");
            }

            $this->info('Acceptance fee processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Processing Acceptance fee CSV file: $file");
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

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Payment;
use App\Models\Programme;
use App\Models\PaymentStructure as Structure;
use League\Csv\Reader;

class ProcessPaymentCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processPaymentCSV {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Payment CSV file';

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

            $paymentArray = [
                1001 => 'Clinical, Lab, Studio Fees',
                1002 => 'Development Fee',
                1003 => 'Caution Fee',
                1005 => 'ICT Fee',
                1006 => 'ID Card',
                1007 => 'Registration Fee',
                1008 => 'Matriculation Fee',
                1009 => 'Library Fee',
                1010 => 'Sports Fee',
                1011 => 'Entrepreneurial Fee',
                1012 => 'Medical Fee',
                1013 => 'MLS Tuition Fee',
                1014 => 'PST Tuition Fee',
                1016 => 'BIO Tuition Fee',
                1017 => 'CYS Tuition Fee',
                1018 => 'SWE Tuition Fee',
                1019 => 'CHM Tuition Fee',
                1020 => 'MTH Tuition Fee',
                1021 => 'PHE Tuition Fee',
                1022 => 'ACT Tuition Fee',
                1023 => 'BUS Tuition Fee',
                1024 => 'ECN Tuition Fee',
                1025 => 'CCS Tuition Fee',
                1026 => 'MCM Tuition Fee',
                1027 => '2023/2024 Nursing 100L Tuition',
                1028 => 'Application Form fee',
                1029 => 'ACC Tuition Fee',
                1030 => 'Hostel',
                1043 => 'CSC Tuition',
                1044 => 'MBIO Tution',
                1045 => 'Faculty Levy',
                1046 => 'Departmental Levy',
                1047 => 'FBMHS Clinical Fee 200Level',
                1048 => 'Clinical, Lab, Studio Fees 200L',
                1049 => '2022 NSc Tuition Fee',
                1050 => '2022 MLS Tuition Fee',
                1051 => '2022 PST Tuition Fee',
                1052 => '2022 BCH Tuition Fee',
                1053 => '2022 MCB Tuition Fee',
                1054 => '2022 CSC Tuition Fee',
                1055 => '2022 SWE Tuition Fee',
                1056 => '2022 MTH Tuition Fee',
                1057 => '2022 CHM Tuition Fee',
                1058 => '2022 PHE Tuition Fee',
                1059 => '2022 ACCT Tuition Fee',
                1060 => '2022 BUS Tuition Fee',
                1061 => '2022 CSS Tuition Fee',
                1062 => '2022 ECN Tuition Fee',
                1063 => '2022 MCM Tuition Fee',
                1064 => '200L Clinical/Studio Fees',
                1065 => '2022 Law Tuition Fee',
                1066 => 'Law Registration Fee',
                1067 => 'Law Faculty Levy',
                1068 => 'Law Matriculation Levy',
                1069 => 'Law ID Card Fee',
                1070 => 'Student Hand Book',
                1071 => 'Verification Fee',
                1072 => 'Medical Test',
                1073 => 'Practicum',
                1074 => 'Law Library Levy',
                1075 => 'Acceptance Fee',
                1076 => 'Acceptance Fee',
                1077 => 'Application Fee',
            ];

            foreach ($records as $row) {

                $level = $row['LevelCode'];
                $descriptionCode = $row['DescriptionCode'];
                $amount = $row['Amount'];
                $programmeCode = $row['ProgrammeCode'];
                $academicSession = $row['CurrSession'];

                $level = ($level === '1000') ? 1 : (
                    ($level === '1001') ? 2 : (
                    ($level === '1002') ? 3 : null));
                
                $programmeId = null;
                if (array_key_exists($programmeCode, $programmeArray)) {
                    $programmeId = $programmeArray[$programmeCode];
                }

                if(empty($programmeId)){
                    $this->info("Programme with '{$programmeCode}' not found in programme array");
                    continue;
                }

                $structureTitle = null;
                if (array_key_exists($descriptionCode, $paymentArray)) {
                    $structureTitle = $paymentArray[$descriptionCode];
                }

                if(empty($structureTitle)){
                    $this->info("Payment '{$descriptionCode}' not found in payment array");
                    continue;
                }

                $programme = Programme::with('department')->where('id', $programmeId)->first();
                if(!$programme) {
                    $this->info("Programme with '{$programmeCode}' not found in database");
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
}

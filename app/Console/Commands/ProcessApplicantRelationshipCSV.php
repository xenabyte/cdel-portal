<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User as Applicant;
use App\Models\Utme;
use App\Models\NextOfKin;
use App\Models\Guardian;
use League\Csv\Reader;

use Illuminate\Support\Facades\Hash;

class ProcessApplicantRelationshipCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processApplicantRelationshipCSV {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Applicants Relationship CSV file';

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
    

            $records = $csv->getRecords();

            foreach ($records as $row) {
                $applicationNumber = $row['ApplicationNo'];

                $accessCode = $this->generateAccessCode();
                if(!$existingApplicant = Applicant::where('application_number', $applicationNumber)->first()) {
                    $this->info("Applicant '{$applicationNumber}' dosent exists");
                    continue;
                }
                $applicantId = $existingApplicant->id;

                if(empty($existingApplicant->application_type)){

                    $parentFullname = $row['PFullName'];
                    $parentPhoneNo = $row['PPhoneNo'];
                    $parentEmail = $row['PEmail'];
                    $parentAddress = $row['ContAddress'];
                    $guardianId = $this->addGuardian($applicantId, $parentEmail, $parentFullname, $parentPhoneNo, $parentAddress, $accessCode);

                    $nokName = $row['nokName'];
                    $nokPhone = $row['nokPhoneNo'];
                    $nokAddress = $row['nokContAddress'];
                    $nokRelationship = $row['nokRelationship'];
                    $nokId = $this->addNok($applicantId, $parentEmail, $nokName, $nokPhone, $nokAddress, $nokRelationship);

                    $entryMode = $row['ModeEntry'];
                    $programmeCode = $row['Degree'];

                    $UTMERegNo = $row['UTMERegNo'];
                    $deRegNo = $row['DERegNo'];

                    $utmeSubject1 = $row['Subject1'];
                    $utmeScore1 = $row['Score1'];
                    $this->addUtmeRecord($applicantId, $utmeSubject1, $utmeScore1);

                    $utmeSubject2 = $row['Subject2'];
                    $utmeScore2 = $row['Score2'];
                    $this->addUtmeRecord($applicantId, $utmeSubject2, $utmeScore2);

                    $utmeSubject3 = $row['Subject3'];
                    $utmeScore3 = $row['Score3'];
                    $this->addUtmeRecord($applicantId, $utmeSubject3, $utmeScore3);

                    $utmeSubject4 = $row['Subject4'];
                    $utmeScore4 = $row['Score4'];
                    $this->addUtmeRecord($applicantId, $utmeSubject4, $utmeScore4);

                    $deSchool = $row['DECenter'];

                    $programmeId = null;
                    if (array_key_exists($programmeCode, $dataArray)) {
                        $programmeId = $dataArray[$programmeCode];
                    }

                    $updateApplicant = ([
                        'application_type' => $entryMode,
                        'jamb_reg_no' => $entryMode=='UTME' ? $UTMERegNo : $deRegNo,
                        'de_school_attended' => $deSchool=='NULL' ? Null : $deSchool,
                        'next_of_kin_id' => $nokId,
                        'guardian_id' => $guardianId,
                        'programme_id' => $programmeId,
                    ]);
            
                    $existingApplicant->update($updateApplicant);
                }
                $this->info('Applicant relationship already processed!');
            }

            $this->info('Applicant relationship  processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Processing Applicant CSV file: $file");
    }

    public function addUtmeRecord ($applicantId, $subject, $score){

        $newUtme = ([
            'subject' => $subject,
            'user_id' => $applicantId,
            'score' => $score,
        ]);

        if(Utme::create($newUtme)){
            $this->info('Applicant utme record added successfully!');
        }
    }

    public function addNok($nokEmail, $nokName, $nokPhone, $nokAddress, $nokRelationship){

        if($nok= NextOfKin::where('email', $nokEmail)->first()){
            $this->info('Applicant next of kin record added successfully!');
            return $nok->id;
        }

        $newNok = ([
            'name' => $nokName,
            'phone_number' => $nokPhone,
            'email' => $nokEmail,
            'relationship' => $nokRelationship,
            'address' => $nokAddress
        ]);

        if($nok = NextOfKin::create($newNok)){
            $this->info('Applicant next of kin record added successfully!');
            return $nok->id;
        }

        $this->info('Applicant next of kin record not added!');
    }

    public function addGuardian($parentEmail, $parentFullname, $parentPhoneNo, $parentAddress, $accessCode){

        if($guardian= Guardian::where('email', $parentEmail)->first()){
            $this->info('Applicant guardian record added successfully!');
            return $guardian->id;
        }

        $newGuardian = ([
            'name' => $parentFullname,
            'phone_number' => $parentPhoneNo,
            'email' => $parentEmail,
            'passcode' => $accessCode,
            'address' => $parentAddress,
            'password' => Hash::make($accessCode)
        ]);

        if($guardian = Guardian::create($newGuardian)){
            $this->info('Applicant guardian record added successfully!');
            return $guardian->id;
        }

        $this->info('Applicant guardian record not added!');
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

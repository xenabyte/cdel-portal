<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User as Applicant;
use League\Csv\Reader;
use Illuminate\Support\Facades\Hash;


class ProcessApplicantCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processApplicantCSV {file}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Applicants CSV file';

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
                $firstname = $row['FirstName'];
                $lastname = $row['LastName'];
                $middleName = $row['MiddleName'];
                $phoneNo = $row['PhoneNo'];
                $email = $row['Email'];
                $address = $row['ResidentialAddress'];
                $dob = $row['DateofBirth'];
                $gender = $row['Gender'];
                $maritalStatus = $row['MaritalStatus'];
                $religion = $row['Religion'];
                $nationality = $row['Nationality'];
                $stateOfOrigin = $row['StateofOrigin'];
                $lga = $row['LGA'];
                $sessionName = $row['SessionName'];
                $lastname = preg_replace('/[^A-Za-z0-9\s]+/', '-', $lastname);

                $name = $lastname .' '. $middleName.' '. $firstname;
                $accessCode = $this->generateAccessCode();
                if($existingApplicant = Applicant::where('email', $email)->first()) {
                    $this->info("Applicant '{$name}' already exists");
                    continue;
                }  
                
                if($existingApplicant = Applicant::where('application_number', $applicationNumber)->first()) {
                    $this->info("Applicant '{$name}' already exists");
                    continue;
                }

                $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $lastname .' '. $firstname.' '. $middleName)));
                if($existingApplicant = Applicant::where('slug', $slug)->first()) {
                    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $lastname .' '. $firstname.' '. $middleName.' '. $accessCode)));
                }

                $newApplicant = ([
                    'slug' => $slug,
                    'email' => $email,
                    'lastname' => ucwords(strtolower($lastname)),
                    'phone_number' => $phoneNo,
                    'othernames' => ucwords(strtolower($firstname.' '. $middleName)),
                    'password' => Hash::make($accessCode),
                    'passcode' => $accessCode,
                    'academic_session' => $sessionName,
                    'dob' => $dob, 
                    'nationality' => $nationality,
                    'religion' => $religion,
                    'marital_status' => $maritalStatus,
                    'state' => $stateOfOrigin,
                    'lga' => ucwords($lga),
                    'gender' => $gender,
                    'address' => $address,
                    'application_number' => $applicationNumber
                ]);
        
                $applicant = Applicant::create($newApplicant);
            }

            $this->info('Applicant processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Processing Applicant CSV file: $file");
    }

    public function generateAccessCode () {
        $applicationAccessCode = "";
        $current = $this->generateRandomString();
        $isExist = Applicant::where('passcode', $current)->get();
        if(!($isExist->count() > 0)) {
            $applicationAccessCode = $current;
            return $applicationAccessCode;
        } else {
            return $this->generateUserCode();
        }           
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

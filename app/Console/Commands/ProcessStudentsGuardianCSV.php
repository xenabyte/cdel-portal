<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\Programme;
use App\Models\Guardian;

use League\Csv\Reader;
use Illuminate\Support\Facades\Hash;

class ProcessStudentsGuardianCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:processStudentsGuardianCSV {file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process Student Guardian CSV file';

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
                $name = $row['Name'];
                $matricNumber = $row['Matric Number'];
                $email = $row['Email'];
                $address = $row['Address'];
                $phoneNumber = $row['Phone number'];
                $studentName = $row['Ward'];

                $accessCode = $this->generateAccessCode();

                if(!$student = Student::where('matric_number', $matricNumber)->first()) {
                    $this->info("Student '{$studentName}' not found");
                    continue;
                }

                if(!$user = Applicant::where('id', $student->user_id)->first()) {
                    $this->info("Applicant '{$studentName}' not found");
                    continue;
                }

                
                $guardian = Guardian::where('email', $email)->first();
                if($guardian){
                    $user->guardian_id = $guardian->id;
                    $user->save();

                    if(!empty($name) &&  $name != $guardian->name){
                        $guardian->name = $name;
                    }

                    if(!empty($address) && $address != $guardian->address){
                        $guardian->address = $address;
                    }

                    if(!empty($phoneNumber) &&  $phoneNumber != $guardian->phone_number){
                        $guardian->phone_number = $phoneNumber;
                    }
                
                    if($guardian->save()){
                        $this->info("Guardian Exist and Record updated for '{$studentName}'");
                    }

                    continue;
                }else{
                    $accessCode = $this->generateAccessCode();
                    
                    $guardianData = ([
                        'name' => $name,
                        'password' => Hash::make($accessCode),
                        'passcode' => $accessCode,
                        'phone_number' => $phoneNumber,
                        'address' => $address,
                        'email' => $email,
                    ]);
                    $newGuardian = Guardian::create($guardianData);
                    if ($newGuardian){
                        $gua = Guardian::where('email', $email)->first();
                        $user->guardian_id = $gua->id;
                        if($user->save()){
                            $this->info("New guardian created for '{$studentName}'");
                        }
                    }
                }
                
            }

            $this->info('Student guardian processed successfully!');
        } else {
            $this->error('File not found.');
        }

        $this->info("Processing student guardian CSV file: $file");
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use League\Csv\Reader;

use App\Models\CourseManagement;
use App\Models\Transaction;


use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class CronController extends Controller
{
    //

    public function changeCourseManagementPasscode(Request $request){

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];
        $resultProcessStatus = $globalData->examSetting['result_processing_status'];

        $courseManagements = CourseManagement::where([
            'academic_session' => $academicSession
        ])->get();

        if(!$courseManagements){
            return $this->dataResponse('courses have not been assigned to lectures', null, 'error');
        }
        
        foreach($courseManagements as $courseManagement){
            $courseManagement->passcode = $this->generateRandomString();
            $courseManagement->save();
        }

        return $this->dataResponse('Passcode Updated', null);

    }


    public function deletePendingTransactions(){

        $transactions = Transaction::where('status', null)
                                    ->where('payment_method', '!=', 'Manual/BankTransfer')
                                    ->where('payment_method', '!=', null)
                                    ->get();

        if (!$transactions) {
            return $this->dataResponse('No pending transactions found that can be deleted.', null);
        }

        $deletedCount = $transactions->each->forceDelete();

        return $this->dataResponse('Pending transactions deleted successfully.', null);
    }

    public function exportDatabase(){
        $backupDir = public_path('backups');
        if (!file_exists($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $fileName = 'database_backup_' . date('Y-m-d_H-i-s') . '.sql';
        $exportPath = $backupDir . '/' . $fileName;

        $databaseName = env('DB_DATABASE');
        $username = env('DB_USERNAME');
        $password = env('DB_PASSWORD');

        exec("mysqldump --user={$username} --password={$password} {$databaseName} > {$exportPath}");

        if (!file_exists($exportPath)) {
            return response()->json(['error' => 'Failed to create the backup file.'], 500);
        }

        Mail::send([], [], function ($message) use ($exportPath, $fileName) {
            $message->to(env('BACKUP_EMAIL'))
                ->subject('Database Backup ' . date('Y-m-d H:i:s'))
                ->attach($exportPath, ['as' => $fileName]);
        });

        unlink($exportPath);

        return response()->json(['message' => 'Database exported and email sent successfully.']);
    }


}

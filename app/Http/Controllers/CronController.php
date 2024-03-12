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

}

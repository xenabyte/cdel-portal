<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\AcademicLevel;
use App\Models\Session;
use App\Models\SessionSetting;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class AcademicController extends Controller
{
    //

    public function academicLevel(){

        $academicLevels = AcademicLevel::get();
        
        return view('admin.academicLevel', [
            'academicLevels' => $academicLevels
        ]);
    }

    public function sessionSetup(){

        $sessions = Session::orderBy('id', 'DESC')->get();
        
        return view('admin.sessionSetup', [
            'sessions' => $sessions
        ]);
    }

    public function setSession(Request $request){
        $validator = Validator::make($request->all(), [
            'admission_session' => 'required',
            'academic_session' => 'required',
            'application_session' => 'required',
        ]);


        $sessionSetting = new SessionSetting;
        if(!empty($sessionSetting->sessionSetting_id) && !$sessionSetting = SessionSetting::find($request->sessionSetting_id)){
            alert()->error('Oops', 'Invalid Session Setting Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->admission_session) &&  $request->admission_session != $sessionSetting->admission_session){
            $sessionSetting->admission_session = $request->admission_session;
        }

        if(!empty($request->academic_session) &&  $request->academic_session != $sessionSetting->academic_session){
            $sessionSetting->academic_session = $request->academic_session;
        }

        if(!empty($request->application_session) &&  $request->application_session != $sessionSetting->application_session){
            $sessionSetting->application_session = $request->application_session;
        }

        if($sessionSetting->save()){
            alert()->success('Changes Saved', 'Session changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function addSession(Request $request){
        $validator = Validator::make($request->all(), [
            'year' => 'required|string|unique:sessions',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newSession = [
            'year' => $request->year,
        ];
        
        if(Session::create($newSession)){
            alert()->success('Session added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateSession(Request $request){
        $validator = Validator::make($request->all(), [
            'session_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$session = Session::find($request->session_id)){
            alert()->error('Oops', 'Invalid Session ')->persistent('Close');
            return redirect()->back();
        }

        $session->year = $request->year;

        if($session->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteSession(Request $request){
        $validator = Validator::make($request->all(), [
            'session_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$session = Session::find($request->session_id)){
            alert()->error('Oops', 'Invalid Session ')->persistent('Close');
            return redirect()->back();
        }
        
        if($session->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function addLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level' => 'required|string|unique:academic_levels',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $newLevel = [
            'level' => $request->level,
        ];
        
        if(AcademicLevel::create($newLevel)){
            alert()->success('Academic level added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$level = AcademicLevel::find($request->level_id)){
            alert()->error('Oops', 'Invalid Level ')->persistent('Close');
            return redirect()->back();
        }

        $level->level = $request->level;

        if($level->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteLevel(Request $request){
        $validator = Validator::make($request->all(), [
            'level_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$level = AcademicLevel::find($request->level_id)){
            alert()->error('Oops', 'Invalid Level ')->persistent('Close');
            return redirect()->back();
        }
        
        if($level->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }
}

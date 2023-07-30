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

use App\Models\GlobalSetting as Setting;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class AdminController extends Controller
{
    //

    public function index(){

        return view('admin.home');
    }

    public function setting(){
        $setting = Setting::first();
        return view('admin.globalSettings', [
            'siteInfo' => $setting
        ]);
    }

    public function updateSiteInfo(Request $request){
        $validator = Validator::make($request->all(), [
            'logo' => 'nullable|image',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $siteInfo = new Setting;
        if(!empty($request->siteinfo_id) && !$siteInfo = Setting::find($request->siteinfo_id)){
            alert()->error('Oops', 'Invalid Site Information')->persistent('Close');
            return redirect()->back();
        }

       
        if(!empty($request->logo)){
            if(!empty($siteInfo->logo)){
                unlink($siteInfo->logo);
            }

            $imageUrl = 'uploads/siteInfo/logoa.'.$request->file('logo')->getClientOriginalExtension();
            $image = $request->file('logo')->move('uploads/siteInfo', $imageUrl);

            $siteInfo->logo = $imageUrl;
        }

        if($siteInfo->save()){
            alert()->success('Changes Saved', 'Site informationn changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function chargeStudent(){
        return view('admin.chargeStudent');
    }
}

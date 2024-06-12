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

use App\Models\Partner;
use App\Models\Notification;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

use App\Mail\NotificationMail;

class PartnerController extends Controller
{
    //

    public function partner($slug){
        $partner = Partner::where('slug', $slug)->first();
        return view('admin.partnerProfile',[
            'partner' => $partner
        ]);
    }

    public function partners(Request $request){

        $partners = Partner::where('status', 1)->get();

        return view('admin.partners',[
            'partners' => $partners
        ]);
    }

    public function partnerApproval(Request $request){

        $partners = Partner::where('status', 0)->get();

        return view('admin.partnerApproval',[
            'partners' => $partners
        ]);
    }

    public function approvePartner(Request $request){
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$partner = Partner::find($request->partner_id)){
            alert()->error('Oops', 'Invalid Partner ')->persistent('Close');
            return redirect()->back();
        }

        $partner->status = true;
        
        if($partner->save()){

            $message = $partner->name . ', we are pleased to inform you that your registration details have been reviewed and approved. We appreciate your efforts in becoming a partner with us. Your commitment to this partnership is highly valued. As a token of our appreciation, we are excited to let you know that there are various incentives available for your participation. We look forward to a fruitful and successful collaboration. Thank you for choosing to be a part of our community.';

            $senderName = env('SCHOOL_NAME');
            $receiverName = $partner->name;
            $receiverEmail = $partner->email;
            
            $mail = new NotificationMail($senderName, $message, $receiverName);
            Mail::to($receiverEmail)->send($mail);

            Notification::create([
                'partner_id' => $partner->id,
                'description' => $message,
                'status' => 0
            ]);


            alert()->success('Approved Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }
    
    
    public function deletePartner(Request $request){
        $validator = Validator::make($request->all(), [
            'partner_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$partner = Partner::find($request->partner_id)){
            alert()->error('Oops', 'Invalid Partner ')->persistent('Close');
            return redirect()->back();
        }
        
        if($partner->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }
}

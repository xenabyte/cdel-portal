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


use App\Models\Committee;
use App\Models\CommitteeMember;
use App\Models\Meeting;
use App\Models\MeetingAttendee;
use App\Models\Staff;
use App\Models\Notification;

use App\Mail\NotificationMail;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class CommitteeController extends Controller
{
    //

    public function committees(){

        $committees = Committee::get();
        
        return view('admin.committees', [
            'committees' => $committees
        ]);
    }

    public function addCommittee(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:committees',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        $newCommittee = [
            'name' => $request->name,
            'duties' => $request->duties,
            'slug' => $slug
        ];
        
        if(Committee::create($newCommittee)){
            alert()->success('Committee created successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateCommittee(Request $request){
        $validator = Validator::make($request->all(), [
            'committee_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$committee = Committee::find($request->committee_id)){
            alert()->error('Oops', 'Invalid Committee ')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->name) && $request->name != $committee->name){
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));
            $committee->name = $request->name;
            $committee->slug = $slug;
        }

        if(!empty($request->duties) && $request->duties!= $committee->duties){
            $committee->duties = $request->duties;
        }

        if($committee->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

    public function deleteCommittee(Request $request){
        $validator = Validator::make($request->all(), [
            'committee_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        if(!$committee = Committee::find($request->committee_id)){
            alert()->error('Oops', 'Invalid Committee ')->persistent('Close');
            return redirect()->back();
        }
        
        if($committee->delete()){
            alert()->success('Delete Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }


    public function assignCommitteePosition(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required',
            'committee_id' => 'required',
            'role' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }

        if(!$committee = Committee::find($request->committee_id)){
            alert()->error('Oops', 'Invalid Committee ')->persistent('Close');
            return redirect()->back();
        }

        if($request->role == 'chairman'){
            $committee->chairman_id = $staff->id;
        }

        if($request->role == 'secretary'){
            $committee->secretary_id = $staff->id;
        }

        if($committee->save()){
            alert()->success('Staff Assigned Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back(); 
    }

    public function addMember(Request $request){
        $validator = Validator::make($request->all(), [
            'staff_id' => 'required|string',
            'committee_id' => 'required|string',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$staff = Staff::find($request->staff_id)){
            alert()->error('Oops', 'Invalid Staff ')->persistent('Close');
            return redirect()->back();
        }

        if(!$committee = Committee::find($request->committee_id)){
            alert()->error('Oops', 'Invalid Committee ')->persistent('Close');
            return redirect()->back();
        }

        $newCommitteeMember = [
            'staff_id' => $request->staff_id,
            'committee_id' => $request->committee_id,
        ];
        
        if(CommitteeMember::create($newCommitteeMember)){
            alert()->success('Committee Member created successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
        
    }

     
    public function committee($slug){

        $committee = Committee::with('members', 'chairman', 'secretary', 'meetings')->where('slug', $slug)->first();
        $staffs = Staff::all();
        
        return view('admin.committee', [
            'committee' => $committee,
            'staffs' => $staffs,
        ]);
    }


    public function createMeeting(Request $request){
        $validator = Validator::make($request->all(), [
            'committee_id' => 'required',
            'time' => 'required',
            'date' => 'required',
            'venue' => 'required',
            'status' => 'required',
            'agenda' => 'nullable',
            'title' => 'required|unique:meetings',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$committee = Committee::find($request->committee_id)){
            alert()->error('Oops', 'Invalid Committee ')->persistent('Close');
            return redirect()->back();
        }
        $slugName = $committee->slug.' '.$request->title;

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slugName)));

        $fileUrl = null;
        if(!empty($request->agenda)){
            $fileUrl = 'uploads/meetings/'.$slug.'.'.$request->file('agenda')->getClientOriginalExtension();
            $image = $request->file('agenda')->move('uploads/meetings', $fileUrl);
        }
        
        $newMeeting = ([
            'committee_id' => $request->committee_id,
            'time' => $request->time,
            'date' => $request->date,
            'venue' => $request->venue,
            'agenda' => $fileUrl,
            'status' => 'pending',
            'title' => $request->title,
            'slug' => $slug
        ]);

        if(Meeting::create($newMeeting)){

            $committeeMembers = CommitteeMember::where('committee_id', $request->committee_id)->pluck('staff_id');

            foreach ($committeeMembers as $memberId) {
                $member = Staff::find($memberId);
                $memberEmail = $member->email;
                $senderName = env('SCHOOL_NAME');
                $receiverName = $member->lastname .' ' . $member->othernames;
                $message = 'New meeting "' . $request->title . '" has been scheduled for ' . $request->date .' at ' . $request->time;
                $mail = new NotificationMail($senderName, $message, $receiverName);

                Notification::create([
                    'staff_id' => $memberId,
                    'message' => $message,
                    'status' => 0
                ]);
            }
            alert()->success('Meeting Created Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateMeeting(Request $request){
        $validator = Validator::make($request->all(), [
            'meeting_id' => 'required',
            'committee_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$meeting = Meeting::find($request->meeting_id)){
            alert()->error('Oops', 'Invalid Meeting ')->persistent('Close');
            return redirect()->back();
        }

        if(!$committee = Committee::find($request->committee_id)){
            alert()->error('Oops', 'Invalid Committee ')->persistent('Close');
            return redirect()->back();
        }

        $slug = $meeting->slug;

        if(!empty($request->title) && $request->title!= $meeting->title){
            $slugName = $committee->slug.' '.$request->title;
            $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $slugName)));
            $meeting->title = $request->title;
        }

        if(!empty($request->time) && $request->time!= $meeting->time){
            $meeting->time = $request->time;
        }

        if(!empty($request->date) && $request->date!= $meeting->date){
            $meeting->date = $request->date;
        }

        if(!empty($request->venue) && $request->venue!= $meeting->venue){
            $meeting->venue = $request->venue;
        }

        if(!empty($request->agenda) && $request->agenda!= $meeting->agenda){
            $fileUrl = 'uploads/meetings/'.$slug.'.'.$request->file('agenda')->getClientOriginalExtension();
            $image = $request->file('agenda')->move('uploads/meetings', $fileUrl);
            $meeting->agenda = $fileUrl;
        }

        if(!empty($request->minute) && $request->minute!= $meeting->minute){
            $minuteUrl = 'uploads/meetings/'.$slug.'.'.$request->file('minute')->getClientOriginalExtension();
            $image = $request->file('minute')->move('uploads/meetings', $minuteUrl);
            $meeting->minute = $minuteUrl;

            $committeeMembers = CommitteeMember::where('committee_id', $request->committee_id)->pluck('staff_id');

            foreach ($committeeMembers as $memberId) {
                $member = Staff::find($memberId);
                $memberEmail = $member->email;
                $senderName = env('SCHOOL_NAME');
                $receiverName = $member->lastname .' ' . $member->othernames;
                $message = 'Meeting Minute"' . $request->title . '" has been uploaded for ' . $meeting->title;
                $mail = new NotificationMail($senderName, $message, $receiverName);

                Notification::create([
                    'staff_id' => $memberId,
                    'message' => $message,
                    'status' => 0
                ]);
            }
        }

        if(!empty($request->excerpt) && $request->excerpt!= $meeting->excerpt){
            $excerptUrl = 'uploads/meetings/'.$slug.'.'.$request->file('excerpt')->getClientOriginalExtension();
            $image = $request->file('excerpt')->move('uploads/meetings', $excerptUrl);
            $meeting->excerpt = $excerptUrl;

            $committeeMembers = CommitteeMember::where('committee_id', $request->committee_id)->pluck('staff_id');

            foreach ($committeeMembers as $memberId) {
                $member = Staff::find($memberId);
                $memberEmail = $member->email;
                $senderName = env('SCHOOL_NAME');
                $receiverName = $member->lastname .' ' . $member->othernames;
                $message = 'Meeting Except"' . $request->title . '" has been uploaded for ' . $meeting->title;
                $mail = new NotificationMail($senderName, $message, $receiverName);

                Notification::create([
                    'staff_id' => $memberId,
                    'message' => $message,
                    'status' => 0
                ]);
            }
        }

        if(!empty($request->status) && $request->status!= $meeting->status){
            $meeting->status = $request->status;
        }

        if($meeting->save()){
            alert()->success('Changes Saved', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function deleteMeeting(Request $request){
        $validator = Validator::make($request->all(), [
            'meeting_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$meeting = Meeting::find($request->meeting_id)){
            alert()->error('Oops', 'Invalid Meeting ')->persistent('Close');
            return redirect()->back();
        }

        if($meeting->delete()){
            alert()->success('Meeting Deleted Successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }
}

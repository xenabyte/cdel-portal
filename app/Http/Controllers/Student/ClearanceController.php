<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Programme;
use App\Models\Transaction;
use App\Models\User as Applicant;
use App\Models\Olevel;
use App\Models\Guardian;
use App\Models\NextOfKin;
use App\Models\Payment;
use App\Models\Utme;
use App\Models\Faculty;
use App\Models\Department;
use App\Models\StaffRole;
use App\Models\Leave;
use App\Models\Notification;
use App\Models\Unit;
use App\Models\Role;
use App\Models\FinalClearance;

use App\Mail\ApplicationMail;
use App\Mail\BankDetailsMail;

use App\Libraries\Pdf\Pdf;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;
use Paystack;
use KingFlamez\Rave\Facades\Rave as Flutterwave;

class ClearanceController extends Controller
{
    protected $programmes;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->programmes = Programme::get();
    }

    public function saveBioData(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'image' => 'required',
            'dob' => 'required',
            'religion' => 'required',
            'gender' => 'required',
            'marital_status' => 'required',
            'nationality' => 'required',
            'state' => 'required',
            'lga' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('student')->user()->applicant;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $user->application_number.$user->lastname .' '. $user->othernames)));
        if(!empty($user->slug)){
            $slug = $user->slug;
        }else{
            $user->slug = $slug;
        }
        
        if(!empty($request->dob) && $request->dob != $user->dob){
            $user->dob = $request->dob;
        }

        if(!empty($request->religion) && $request->religion != $user->religion){
            $user->religion = $request->religion;
        }

        if(!empty($request->gender) && $request->gender != $user->gender){
            $user->gender = $request->gender;
        }

        if(!empty($request->marital_status) && $request->marital_status != $user->marital_status){
            $user->marital_status = $request->marital_status;
        }

        if(!empty($request->nationality) && $request->nationality != $user->nationality){
            $user->nationality = $request->nationality;
        }

        if(!empty($request->state) && $request->state != $user->state_of_origin){
            $user->state = $request->state;
        }

        if(!empty($request->address) && $request->address != $user->address){
            $user->address = $request->address;
        }

        if(!empty($request->lga) && $request->lga != $user->lga){
            $user->lga = $request->lga;
        }

        if(!empty($request->image)){
            $imageUrl = 'uploads/applicant/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
            $image = $request->file('image')->move('uploads/applicant', $imageUrl);

            $user->image = $imageUrl;
        }

        session()->put('previous_section', 'bio-data');
        if($user->save()){
            alert()->success('Changes Saved', 'Bio data saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveUtme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'jamb_reg_no' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('student')->user()->applicant;

        if(!empty($request->jamb_reg_no) && $request->jamb_reg_no != $user->jamb_reg_no){
            $user->jamb_reg_no = $request->jamb_reg_no;
        }

        session()->put('previous_section', 'utme');
        if($user->save()){
            alert()->success('Changes Saved', 'Jamb registration number saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveProgramme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'programme_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('student')->user()->applicant;

        if(!empty($request->application_type) && $request->application_type != $user->application_type){
            $user->application_type = $request->application_type;
        }

        if(!empty($request->jamb_reg_no) && $request->jamb_reg_no != $user->jamb_reg_no){
            $user->jamb_reg_no = $request->jamb_reg_no;
        }

        if(!empty($request->programme_id) && $request->programme_id != $user->programme_id){
            $user->programme_id = $request->programme_id;
        }

        session()->put('previous_section', 'programme');
        if($user->save()){
            alert()->success('Changes Saved', 'Type saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function guardianBioData(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone_number' => 'required',
            'email' => 'required',
            'address' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        $user = Auth::guard('student')->user()->applicant;

        $guardian = Guardian::where('email', $request->email)->first();
        if($guardian && empty($user->guardian_id)){
            $user->guardian_id = $guardian->id;
            $user->save();

            alert()->success('Changes Saved', 'Guardian changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        $guardian = new Guardian;
        if(!empty($request->guardian_id) && !$guardian = Guardian::find($request->guardian_id)){
            alert()->error('Oops', 'Invalid Guardian Information')->persistent('Close');
            return redirect()->back();
        }

        $accessCode = $this->generateAccessCode();
        if(empty($guardian->password)){
            $guardian->password = Hash::make($accessCode);
            $guardian->passcode = $accessCode;
        }

        if(!empty($request->name) &&  $request->name != $guardian->name){
            $guardian->name = $request->name;
        }

        if(!empty($request->phone_number) &&  $request->phone_number != $guardian->phone_number){
            $guardian->phone_number = $request->phone_number;
        }

        if(!empty($request->email) &&  $request->email != $guardian->email){
            $guardian->email = $request->email;
        }

        if(!empty($request->address) && $request->address != $guardian->address){
            $guardian->address = $request->address;
        }

        session()->put('previous_section', 'guardian');
        if($guardian->save()){
            $gua = Guardian::where('email', $request->email)->first();
            $user->guardian_id = $gua->id;
            $user->save();

            alert()->success('Changes Saved', 'Guardian changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }
  
    public function programmeById($id) {

        $programme = Programme::where('id', $id)->first();
        return $programme;
    }

    public function facultyById($id) {

        $faculty = Faculty::where('id', $id)->first();
        return $faculty;
    }

    public function departmentById($id) {

        $department = Department::where('id', $id)->first();
        return $department;
    }
    
    public function uploadUtme(Request $request)
    {
        $user = Auth::guard('student')->user()->applicant;
        
        $validator = Validator::make($request->all(), [
            'utme' => 'required',
        ]);
        
        if(!empty($request->utme)){
            if(!empty($user->utme)){
                unlink($user->utme);
            }

            $slug = $user->slug;
            $imageUrl = 'uploads/utme/'.$slug.'.'.$request->file('utme')->getClientOriginalExtension();
            $image = $request->file('utme')->move('uploads/utme', $imageUrl);

            $user->utme = $imageUrl;
        } 

        if($user->save()){
            alert()->success('Good Job', 'UTME Result Uploaded')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

    public function deleteFile(Request $request){
        $user = Auth::guard('student')->user()->applicant;
        
        $validator = Validator::make($request->all(), [
            'file_type' => 'required',
        ]);

        if($request->file_type =='utme'){
            if(!empty($user->utme)){
                unlink($user->utme);
            }

            $user->utme = null;
        }
        
        if($request->file_type =='de'){
            if(!empty($user->de_result)){
                unlink($user->de_result);
            }

            $user->de_result = null;
        }

        if($request->file_type =='olevel_1'){
            if(!empty($user->olevel_1)){
                unlink($user->olevel_1);
            }

            $user->olevel_1 = null;
        }

        if($request->file_type =='olevel_2'){
            if(!empty($user->olevel_2)){
                unlink($user->olevel_2);
            }

            $user->olevel_2 = null;
        }

        if($user->save()){
            alert()->success('Good Job', 'File Deleted')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveDe(Request $request)
    {
        $user = Auth::guard('student')->user()->applicant;
        
        $validator = Validator::make($request->all(), [
            'de_result' => 'required',
            'de_school_attended' => 'required',
        ]);
        
        if(!empty($request->de_result)){
            if(!empty($user->de_result)){
                unlink($user->de_result);
            }

            $slug = $user->slug;
            $imageUrl = 'uploads/de/'.$slug.'.'.$request->file('de_result')->getClientOriginalExtension();
            $image = $request->file('de_result')->move('uploads/de', $imageUrl);

            $user->de_result = $imageUrl;
        } 

        if(!empty($request->de_school_attended) && $request->de_school_attended != $user->de_school_attended){
            $user->de_school_attended = $request->de_school_attended;
        }

        session()->put('previous_section', 'de');
        if($user->save()){
            alert()->success('Good Job', 'DE Result Uploaded')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

    public function nokBioData(Request $request){

        $user = Auth::guard('student')->user()->applicant;
        
        $nok = NextOfKin::where('email', $request->email)->first();
        if($nok && empty($user->next_of_kin_id)){
            $user->next_of_kin_id = $nok->id;
            $user->save();

            alert()->success('Changes Saved', 'Next of kin changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        $nextOfKin = new NextOfKin;
        if(!empty($request->nextOfKin_id) && !$nextOfKin = NextOfKin::find($request->nextOfKin_id)){
            alert()->error('Oops', 'Invalid Next of Kin Information')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->name) &&  $request->name != $nextOfKin->name){
            $nextOfKin->name = $request->name;
        }

        if(!empty($request->relationship) &&  $request->relationship != $nextOfKin->relationship){
            $nextOfKin->relationship = $request->relationship;
        }

        if(!empty($request->phone_number) &&  $request->phone_number != $nextOfKin->phone_number){
            $nextOfKin->phone_number = $request->phone_number;
        }

        if(!empty($request->email) &&  $request->email != $nextOfKin->email){
            $nextOfKin->email = $request->email;
        }

        if(!empty($request->address) && $request->address != $nextOfKin->address){
            $nextOfKin->address = $request->address;
        }

        session()->put('previous_section', 'nok');
        if($nextOfKin->save()){
            $nok = NextOfKin::where('email', $request->email)->first();
            $user->next_of_kin_id = $nok->id;
            $user->save();
            
            alert()->success('Changes Saved', 'Next of Kin changes saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function saveSitting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sitting_no' => 'required',
            'schools_attended' => 'required'
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $user = Auth::guard('student')->user()->applicant;

        if(!empty($request->sitting_no) && $request->sitting_no != $user->sitting_no){
            $user->sitting_no = $request->sitting_no;
        }

        if(!empty($request->schools_attended) && $request->schools_attended != $user->schools_attended){
            $user->schools_attended = $request->schools_attended;
        }

        session()->put('previous_section', 'olevel');
        if($user->save()){
            alert()->success('Changes Saved', 'Number of sittings saved successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function addOlevel(Request $request)
    {
        $userId = Auth::guard('student')->user()->applicant->id;
        $sittingNo = Auth::guard('student')->user()->applicant->sitting_no;
        $subjects = $request->input('subjects');

        foreach ($subjects as $subject) {
            if (!empty($subject['subject'])) {
                $validator = Validator::make($subject, [
                    'subject' => 'nullable|string|max:255',
                    'grade' => 'required|string',
                    'year' => 'required|integer|min:2010|max:2099',
                    'reg_no' => 'required|string|max:255',
                ]);
        
                if ($validator->fails()) {
                    alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                    return redirect()->back();
                }
        
                if (Olevel::where('user_id', $userId)->count() > 8) {
                    alert()->error('oops', 'Nine subjects already added, you can\'t add more')->persistent('Close');
                    return redirect()->back();
                }
        
                // Check registration numbers
                $regNos = Olevel::where('user_id', $userId)->pluck('reg_no')->toArray();
                $regNos[] = $subject['reg_no']; // Corrected this line
        
                $uniqueRegNos = array_unique($regNos);
                $uniqueRegNosCount = count($uniqueRegNos);
        
                if ($uniqueRegNosCount > $sittingNo) {
                    alert()->error('oops', 'You specified ' . $sittingNo . ' sittings but we are getting more than specified')->persistent('Close');
                    return redirect()->back();
                }
        
                // Check years
                $years = Olevel::where('user_id', $userId)->pluck('year')->toArray();
                $years[] = $subject['year']; // Corrected this line
        
                $uniqueYears = array_unique($years);
                $uniqueYearsCount = count($uniqueYears);
        
                if ($uniqueYearsCount > $sittingNo) {
                    alert()->error('oops', 'You specified ' . $sittingNo . ' sittings but we are getting more than specified')->persistent('Close');
                    return redirect()->back();
                }
        
                if (Olevel::where('user_id', $userId)->where('subject', $subject['subject'])->count() > 0) { 
                    continue;
                }
        
                Olevel::create([
                    'subject' => $subject['subject'],
                    'grade' => $subject['grade'],
                    'year' => $subject['year'],
                    'reg_no' => $subject['reg_no'],
                    'user_id' => $userId,
                ]);
            }
        }
        
        session()->put('previous_section', 'olevel');
        alert()->success('Good Job!', 'Subjects and grades added successfully')->persistent('Close');
        return redirect()->back();
    }

    public function updateOlevel(Request $request){
        $validator = Validator::make($request->all(), [
            'olevel_id' => 'required|min:1',
        ]);

        if($validator->fails()) {

            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if(!$olevel = Olevel::find($request->olevel_id)){
            alert()->error('Oops', 'Invalid OLevel Information')->persistent('Close');
            return redirect()->back();
        }

        $olevel->year = $request->year;
        $olevel->subject = $request->subject;
        $olevel->reg_no = $request->reg_no;
        $olevel->grade = $request->grade;

        session()->put('previous_section', 'olevel');
        if($olevel->save()){
            alert()->success('Record Updated', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    /**
     * Delete Olevel
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deleteOlevel(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'olevel_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }


        if(!$olevel = Olevel::find($request->olevel_id)){
            alert()->error('Oops', 'Invalid OLevel Information')->persistent('Close');
            return redirect()->back();
        }

        session()->put('previous_section', 'olevel');
        if($olevel->delete()){
            alert()->success('Record Deleted', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function addUtme(Request $request)
    {
        $userId = Auth::guard('student')->user()->applicant->id;        
        $subjects = $request->input('subjects');

        foreach ($subjects as $subject) {
            if (!empty($subject['subject'])) {
                $validator = Validator::make($subject, [
                    'subject' => 'required|string|max:255',
                    'score' => 'required|integer|min:0|max:100',
                ]);
        
                if($validator->fails()) {
                    alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
                    return redirect()->back();
                }

                Utme::create([
                    'subject' => $subject['subject'],
                    'score' => $subject['score'],
                    'user_id' => $userId,
                ]);
            }
        }

        session()->put('previous_section', 'utme');

        alert()->success('Changes Saved', 'Subject saved successfully')->persistent('Close');
        return redirect()->back();
        
    }

    public function updateUtme(Request $request){
        $validator = Validator::make($request->all(), [
            'utme_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }


        if(!$utme = Utme::find($request->utme_id)){
            alert()->error('Oops', 'Invalid UTME Information')->persistent('Close');
            return redirect()->back();
        }

        $utme->subject = $request->subject;
        $utme->score = $request->score;

        session()->put('previous_section', 'utme');
        if($utme->save()){
            alert()->success('Record updated', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    /**
     * Delete UTME
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function deleteUtme(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'utme_id' => 'required|min:1',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }


        if(!$utme = Utme::find($request->utme_id)){
            alert()->error('Oops', 'Invalid UTME Information')->persistent('Close');
            return redirect()->back();
        }

        if($utme->delete()){
            alert()->success('Record Deleted', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function uploadOlevel(Request $request)
    {
        $user = Auth::guard('student')->user()->applicant;
        
        $validator = Validator::make($request->all(), [
            'olevel_1' => 'required',
        ]);

        if($user->sitting_no > 1){
            $validator = Validator::make($request->all(), [
                'olevel_1' => 'required',
                'olevel_2' => 'required',
            ]);
        }

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
        
        if(!empty($request->olevel_1)){
            if(!empty($user->olevel_1)){
                unlink($user->olevel_1);
            }

            $slug = $user->slug .'-1';
            $imageUrl = 'uploads/olevel/'.$slug.'.'.$request->file('olevel_1')->getClientOriginalExtension();
            $image = $request->file('olevel_1')->move('uploads/olevel', $imageUrl);

            $user->olevel_1 = $imageUrl;
        } 

        if(!empty($request->olevel_2)){
            if(!empty($user->olevel_2)){
                unlink($user->olevel_2);
            }

            $slug = $user->slug .'-2';
            $imageUrl2 = 'uploads/olevel/'.$slug.'.'.$request->file('olevel_2')->getClientOriginalExtension();
            $image = $request->file('olevel_2')->move('uploads/olevel', $imageUrl2);

            $user->olevel_2 = $imageUrl2;
        } 

        session()->put('previous_section', 'olevel');
        if($user->save()){
            alert()->success('Good Job', 'Olevel Result Uploaded')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }

    public function submitClearanceApplication(Request $request)
    {
        $student = Auth::guard('student')->user();

        $student->clearance_status = 2;

        if($student->save()){
            alert()->success('Good Job', 'Application Submitted')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function startClearance(Request $request){
           
        $validator = Validator::make($request->all(), [
            'experience' => 'required',
        ]);


        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Auth::guard('student')->user();
        $hodId = $student->department->hod_id;
        $deanId = $student->faculty->dean_id;

        $unitNames = ['UNIT_REGISTRY', 'UNIT_BURSARY', 'UNIT_STUDENT_CARE', 'UNIT_LIBRARY', 'UNIT_PPD'];
        $unitHeadIds = [];

        foreach ($unitNames as $unitName) {
            $unitConstant = constant("App\Models\Unit::$unitName");
            $unit = Unit::where('name', $unitConstant)->first();
            if ($unit) {
                $unitHeadIds[$unitName] = $unit->unit_head_id;
            } else {
                $unitHeadIds[$unitName] = null; 
            }
        }
       
        $clearanceData = ([
            'student_id' => $student->id,
            'experience' => $request->experience,
            'hod_id' => $hodId,
            'dean_id' => $deanId,
            'library_id' => $unitHeadIds['UNIT_LIBRARY'],
            'student_care_dean_id' => $unitHeadIds['UNIT_STUDENT_CARE'],
            'registrar_id' => $unitHeadIds['UNIT_REGISTRY'],
            'bursary_id' => $unitHeadIds['UNIT_BURSARY'],
            'ppd_id' => $unitHeadIds['UNIT_PPD'],
        ]);

        if($clearance = FinalClearance::create($clearanceData)){
            alert()->success('Good Job', 'Application Submitted')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }


    public function downloadClearance(Request $request){
        $student = Auth::guard('student')->user();

        $clearance = FinalClearance::where('student_id', $student->id)->first();

        if(!empty($clearance->file)){
            return redirect(asset($clearance->file));
        }

        $pdf = new Pdf();
        $file = $pdf->generateDownloadClearance($student->id);

        $clearance->file = $file;
        $clearance->save();

        return redirect(asset($file));        
    }

}

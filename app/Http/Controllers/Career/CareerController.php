<?php

namespace App\Http\Controllers\Career;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\CareerProfile;
use App\Models\Career;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\Models\JobLevel;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class CareerController extends Controller
{
    //

    public function index(Request $request){
        $jobVacancies = JobVacancy::where('status', 'active')->where('type', JobVacancy::TYPE_JOB)->get();

        return view('career.home', [
            'jobVacancies' => $jobVacancies
        ]);
    }

    public function profile(Request $request){

        return view('career.profile');
    }

    public function applications(Request $request){

        return view('career.applications');
    }


    public function manageProfile(Request $request){
        $career = Auth::guard('career')->user();
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $career->lastname.' '.$career->othernames)));


        $profile = CareerProfile::firstOrNew(['career_id' => $career->id]);

        if ($request->has('biodata')) {
            $profile->biodata = $request->input('biodata');
        }

        if ($request->has('education_history')) {
            $profile->education_history = $request->input('education_history');
            session()->put('previous_section', 'educationHistory');
        }

        if ($request->has('professional_information')) {
            $profile->professional_information = $request->input('professional_information');
            session()->put('previous_section', 'professionalInformation');
        }

        if ($request->has('publications')) {
            $profile->publications = $request->input('publications');
            session()->put('previous_section', 'publications');
        }

        if ($request->hasFile('cv_path')) {
            $imageUrl = 'uploads/career/cv/'.$slug.'.'.$request->file('cv_path')->getClientOriginalExtension();
            $image = $request->file('cv_path')->move('uploads/career/cv/', $imageUrl);
            $profile->cv_path = $imageUrl;
            session()->put('previous_section', 'CV');
        }

        if ($request->hasFile('image')) {
            $imageUrl = 'uploads/career/'.$slug.'.'.$request->file('image')->getClientOriginalExtension();
            $image = $request->file('image')->move('uploads/career/', $imageUrl);

            $career->image = $imageUrl;
            $career->save(); 
        }

        $profile->save();

        alert()->success('Good Job', 'Application reviewed')->persistent('Close');
        return redirect()->back();
    }


    public function apply(Request $request){
        $career = Auth::guard('career')->user();

        $validator = Validator::make($request->all(), [
            'job_vacancy_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$jobVacancy = JobVacancy::where('id', $request->job_vacancy_id)->first()) {
            alert()->error('Oops', 'Invalid Job Vacancy')->persistent('Close');
            return redirect()->back();
        }

        $existingApplication = JobApplication::where('job_vacancy_id', $request->job_vacancy_id)
            ->where('career_id', $career->id)
            ->first();

        if ($existingApplication) {
            alert()->error('Oops', 'You have already applied for this job')->persistent('Close');
            return redirect()->back();
        }

        $jobApplicationData = [
            'job_vacancy_id' => $request->job_vacancy_id,
            'career_id' => $career->id,
        ];

        if (JobApplication::create($jobApplicationData)) {
            alert()->success('Success', 'Application submitted successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function deleteApplication(Request $request){
        $career = Auth::guard('career')->user();

        $validator = Validator::make($request->all(), [
            'application_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$jobApplication = JobApplication::where('id', $request->application_id)->first()) {
            alert()->error('Oops', 'Invalid Job Application')->persistent('Close');
            return redirect()->back();
        }

        if ($jobApplication->career_id != $career->id) {
            alert()->error('Oops', 'You are not authorized to delete this application')->persistent('Close');
            return redirect()->back();
        }

        if ($jobApplication->delete()) {
            alert()->success('Success', 'Application deleted successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function manageApplication(Request $request){
        $validator = Validator::make($request->all(), [
            'vacancy_id' => 'required|integer',
            'application_id' => 'required|integer',
            'response' => 'required|string|in:accepted,rejected',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if (!$jobApplication = JobApplication::where('id', $request->application_id)->first()) {
            alert()->error('Oops', 'Invalid Job Application')->persistent('Close');
            return redirect()->back();
        }

         // Ensure the status hasn't already been set
        if ($jobApplication->status === 'accepted' || $jobApplication->status === 'rejected') {
            alert()->error('Oops', 'This action has already been taken')->persistent('Close');
            return redirect()->back();
        }

        if (!$jobVacancy = JobVacancy::where('id', $request->vacancy_id)->first()) {
            alert()->error('Oops', 'Invalid Job Vacancy')->persistent('Close');
            return redirect()->back();
        }

        if ($request->input('response') === 'accepted') {
            $jobApplication->status = 'accepted';
        } elseif ($request->input('response') === 'rejected') {
            $jobApplication->status = 'rejected';
        }

        if($jobApplication->save()){
            if ($request->input('response') === 'accepted') {
                
            }

            alert()->success('Success', 'Offer status set successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }
}

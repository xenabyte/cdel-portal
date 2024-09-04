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

    public function apply (){
        
    }
}

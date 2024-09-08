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

use App\Models\Career;
use App\Models\Staff;
use App\Models\Session;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\MOdels\Unit;
use App\MOdels\Faculty;
use App\MOdels\Department;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class CareerController extends Controller
{
    //

    public function jobVacancy(){

        $jobVacancies = JobVacancy::get();

        return view('admin.jobVacancy', [
            'jobVacancies' => $jobVacancies
        ]);
    }

    public function prospectiveStaff(){

        return view('admin.prospectiveStaff');
    }

    public function postJobVacancy(Request $request){  
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'description' =>'required',
            'requirements' =>'required',
            'application_deadline' =>'required',
            'type' =>'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = md5(strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->title.time()))));

        $jobVacancyData = ([
            'title' => $request->title,
            'description' => $request->description,
            'requirements' => $request->requirements,
            'application_deadline' => Carbon::parse($request->application_deadline)->format('Y-m-d'),
            'type' => $request->type,
            'status' => 'active',
            'cgpa' => $request->cgpa,
            'slug' => $slug
        ]);

        if(JobVacancy::create($jobVacancyData)){
            alert()->success('Job vacancy posted successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function viewJobVacancy($slug){

        $jobVacancy = JobVacancy::with('applications', 'applications.jobApplicant', 'applications.workStudyApplicant')->where('slug', $slug)->first();

        return view('admin.jobVacancyDetails', [
            'jobVacancy' => $jobVacancy
        ]);
    }

    public function updateJobVacancy(Request $request){
        $validator = Validator::make($request->all(), [
            'job_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        if($jobVacancy = JobVacancy::find($request->job_id)){
            alert()->error('Oops', 'Invalid Job ')->persistent('Close');
            return redirect()->back();
        }

        if(!empty($request->title) &&  $request->title != $jobVacancy->title){
            $jobVacancy->title = $request->title;
        }

        if(!empty($request->description) &&  $request->description != $jobVacancy->description){
            $jobVacancy->description = $request->description;
        }

        if(!empty($request->requirements) &&  $request->requirements != $jobVacancy->requirements){
            $jobVacancy->requirements = $request->requirements;
        }

        if(!empty($request->application_deadline) &&  $request->application_deadline != $jobVacancy->application_deadline){
            $jobVacancy->application_deadline = $request->application_deadline;
        }

        if(!empty($request->type) && $request->type != $jobVacancy->type){
            $jobVacancy->type = $request->type;
        }

        if(!empty($request->status) && $request->status != $jobVacancy->status){
            $jobVacancy->status = $request->status;
        }

        if(!empty($request->cgpa) && $request->cgpa != $jobVacancy->cgpa){
            $jobVacancy->cgpa = $request->cgpa;
        }

        if($jobVacancy->save()){
            alert()->success('Job vacancy updated successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops', 'error in saving changes ')->persistent('Close');
        return redirect()->back();
    }
}

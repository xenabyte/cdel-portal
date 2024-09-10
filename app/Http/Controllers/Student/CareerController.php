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

use App\Models\CareerProfile;
use App\Models\student;
use App\Models\JobVacancy;
use App\Models\JobApplication;
use App\Models\WorkStudy;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class CareerController extends Controller
{
    //

    public function vacancies(Request $request){
        $jobVacancies = JobVacancy::where('status', 'active')->where('type', JobVacancy::TYPE_WORKSTUDY)->get();

        return view('student.vacancies', [
            'jobVacancies' => $jobVacancies
        ]);
    }

    public function applications(Request $request){

        return view('student.applications');
    }

    public function apply(Request $request){
        $student = Auth::guard('student')->user();

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

        if($student->cgpa > $jobVacancy->cgpa){
            alert()->error('Oops', 'Your CGPA is below the required CGPA for this job')->persistent('Close');
            return redirect()->back();
        }

        $existingApplication = JobApplication::where('job_vacancy_id', $request->job_vacancy_id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingApplication) {
            alert()->error('Oops', 'You have already applied for this job')->persistent('Close');
            return redirect()->back();
        }

        $jobApplicationData = ([
            'job_vacancy_id' => $request->job_vacancy_id,
            'student_id' => $student->id,
        ]);

        if (JobApplication::create($jobApplicationData)) {
            alert()->success('Success', 'Application submitted successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function deleteApplication(Request $request){
        $student = Auth::guard('student')->user();

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

        if ($jobApplication->student_id!= $student->id) {
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
                $workStudyData = ([
                    'student_id' => $jobApplication->student_id,
                    'job_title' => $jobVacancy->title,
                    'job_description' => $jobVacancy->description,
                    'job_requirements' => $jobVacancy->requirements,
                    'job_level_id' => $jobVacancy->level_id,
                    'appointment_letter' => $jobApplication->appointment_letter,
                    'job_status' => 'active'
                ]);
                WorkStudy::create($workStudyData);
            }

            alert()->success('Success', 'Offer status set successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();

    }
}

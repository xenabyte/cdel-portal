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

        $existingApplication = JobApplication::where('job_vacancy_id', $request->job_vacancy_id)
            ->where('student_id', $student->id)
            ->first();

        if ($existingApplication) {
            alert()->error('Oops', 'You have already applied for this job')->persistent('Close');
            return redirect()->back();
        }

        $jobApplicationData = [
            'job_vacancy_id' => $request->job_vacancy_id,
            'student_id' => $student->id,
        ];

        if (JobApplication::create($jobApplicationData)) {
            alert()->success('Success', 'Application submitted successfully')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

}

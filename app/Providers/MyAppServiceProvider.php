<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\SessionSetting;
use App\Models\GlobalSetting as Setting;
use App\Models\ExaminationSetting;
use App\Models\StudentExit;
use App\Models\LevelAdviser;
use App\Models\Student;
use App\Models\StudentCourseRegistration;
use App\Models\Partner;

use Illuminate\Support\Facades\Auth;

use Log;

class MyAppServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('*', function ($view) {
            $view->with('pageGlobalData', $this->pageGlobalData());
        });
    }

    public function pageGlobalData(){
        $sessionSetting = SessionSetting::first();
        $setting = Setting::first();
        $examinationSetting = ExaminationSetting::first();
        $exitApplicationCount = StudentExit::where('status', 'pending')->orderBy('id', 'DESC')->count(); 
        $pendingPartnerCount = Partner::where('status', 0)->count();

        $totalPendingRegistrations = 0;
        $staff = Auth::guard('staff')->user();

        if ($staff) {
            $academicSession = !empty($sessionSetting)? $sessionSetting->academic_session : null;

            $staffHod = false;
            if($staff->acad_department && $staff->id == $staff->acad_department->hod_id){
                $staffHod = true;
            }
    
            $adviserProgrammesQuery = LevelAdviser::with('programme', 'level')->where('academic_session', $academicSession);
            if ($staffHod) {
                $adviserProgrammesQuery->where(function ($query) use ($staff) {
                    $query->whereHas('programme', function ($query) use ($staff) {
                        $query->where('department_id', $staff->department_id);
                    })->orWhere('staff_id', $staff->id);
                });
            } else {
                $adviserProgrammesQuery->where(function ($query) use ($staff) {
                    $query->whereHas('programme', function ($query) use ($staff) {
                        $query->where('department_id', $staff->department_id);
                    })->where('staff_id', $staff->id);
                });
            }
            $adviserProgrammes = $adviserProgrammesQuery->get();
    
            foreach ($adviserProgrammes as $adviserProgramme) {
                $levelId = $adviserProgramme->level_id;
                $programmeId = $adviserProgramme->programme_id;
    
                $studentIds = Student::where('level_id', $levelId)
                    ->where('programme_id', $programmeId)
                    ->pluck('id')
                    ->toArray();
    
                $studentRegistrationsCount = StudentCourseRegistration::with('student', 'student.applicant')
                ->whereIn('student_id', $studentIds)
                ->where('level_id', $levelId)
                ->where('academic_session', $academicSession)
                ->where(function ($query) {
                    $query->where('level_adviser_status', null)
                            ->orWhere('hod_status', null);
                })
                ->count();
                
    
                $totalPendingRegistrations += $studentRegistrationsCount;
            }
        }
        

        $data = new \stdClass();
        $data->sessionSetting = $sessionSetting;
        $data->setting = $setting;
        $data->examSetting = $examinationSetting;
        $data->exitApplicationCount = $exitApplicationCount;
        $data->totalPendingRegistrations = $totalPendingRegistrations;
        $data->pendingPartnerCount = $pendingPartnerCount;

        return $data;
    }
}

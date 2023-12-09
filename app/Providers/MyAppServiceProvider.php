<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\SessionSetting;
use App\Models\GlobalSetting as Setting;
use App\Models\ExaminationSetting;
use App\Models\StudentExit;

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
        

        $data = new \stdClass();
        $data->sessionSetting = $sessionSetting;
        $data->setting = $setting;
        $data->examSetting = $examinationSetting;
        $data->exitApplicationCount = $exitApplicationCount;

        return $data;
    }
}

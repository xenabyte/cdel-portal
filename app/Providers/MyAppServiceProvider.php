<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

use App\Models\SessionSetting;

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

        $data = new \stdClass();
        $data->sessionSetting = $sessionSetting;

        return $data;
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\SessionSetting;
use App\Models\AcademicSessionSetting;
use App\Models\ExaminationSetting;

use Log;

class GlobalDataMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $academicSessionSetting = AcademicSessionSetting::all()->keyBy('programme_category_id');

        $appSetting = SessionSetting::first();
        $examinationSetting = ExaminationSetting::first();

        $data = new \stdClass();
        $data->sessionSettings = $academicSessionSetting;
        $data->examSetting = $examinationSetting;
        $data->appSetting = $appSetting;

        $request->merge([
            'global_data' => $data,
        ]);

        return $next($request);
    }
}

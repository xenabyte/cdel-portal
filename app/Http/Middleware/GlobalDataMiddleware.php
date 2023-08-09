<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

use App\Models\SessionSetting;
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
        // Add your data to the request object.
        $sessionSetting = SessionSetting::first();
        $examinationSetting = ExaminationSetting::first();

        $data = new \stdClass();
        $data->sessionSetting = $sessionSetting;
        $data->examSetting = $examinationSetting;

        $request->merge([
            'global_data' => $data,
        ]);

        
        return $next($request);
    }
}

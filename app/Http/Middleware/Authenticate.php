<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            $guard = $this->getGuard($request);
            
            switch ($guard) {
                case 'admin':
                    return route('admin.login'); 
    
                case 'staff':
                    return route('staff.login'); 

                case 'partner':
                    return route('partner.login'); 
                
                case 'student':
                    return route('student.login'); 

                case 'user':
                    return route('applicant.login'); 

                case 'guardian':
                    return route('guardian.login'); 
    
                default:
                    return env('WEBSITE_URL');
            }
            
            return env('WEBSITE_URL');
        }
    }

    protected function getGuard($request)
    {
        if ($request->expectsJson()) {
            return null;
        }

        return $request->segment(1);
    }
}

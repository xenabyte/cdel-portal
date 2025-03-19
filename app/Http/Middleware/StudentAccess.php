<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use App\Models\StudentSuspension;
use App\Models\StudentExpulsion;


use Log;
class StudentAccess
{
	/**
	 * Handle an incoming request.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  \Closure  $next
	 * @param  string|null  $guard
	 * @return mixed
	 */
	public function handle($request, Closure $next, $guard = 'student')
	{
        $student = Auth::guard($guard)->user();

        if (!$student) {
            return redirect('student/login');
        }

        // 1️⃣ Check for expulsion
        if ($expulsion = $this->checkExpulsion($student)) {
            return response()->view('student.expelled', $expulsion);
        }

        // 2️⃣ Check for active suspension
        if ($suspension = $this->checkSuspension($student)) {
            // Allow only specific routes while suspended
            $allowedRoutes = [
                'student.home',
                'student.profile', 
                'student.transactions',
                'student.examResult',
                'student.saveBioData',
                'student.updatePassword',
                'student.uploadImage',
                'student.generateResult',
                'student.studentDisciplinary',
                'student.makePayment',
                'student.viewSuspension',
                'student.manageSuspension'
            ];
            
            if (!in_array(request()->route()->getName(), $allowedRoutes)) {
                return response()->view('student.suspended', $suspension);
            }
        }

        // 3️⃣ Check for withdrawal status
        if ($this->isWithdrawn($student, $request)) {
            return response()->view('student.withdrawn', [
                'message' => 'You have been withdrawn. You can only access Transactions and Change Programme.',
            ]);
        }

        $response = $next($request);
        return $response;
    }

    /**
     * Check if a student is expelled.
     */
    private function checkExpulsion($student)
    {
        $expulsion = StudentExpulsion::where('student_id', $student->id)->first();
        
        if ($expulsion) {
            return [
                'message' => 'You have been expelled from the institution.',
                'reason' => $expulsion->reason ?? 'No reason provided.',
                'date' => $expulsion->created_at->format('d M, Y'),
            ];
        }

        return null;
    }

    /**
     * Check if a student is currently suspended.
     */
    private function checkSuspension($student)
    {
        $suspension = StudentSuspension::where('student_id', $student->id)
            ->whereNull('end_date')
            ->first();


        if ($suspension) {
            return [
                'message' => 'You are currently suspended.',
                'reason' => $suspension->reason ?? 'No reason provided.',
                'suspension_start' => $suspension->start_date,
            ];
        }

        return null;
    }

    /**
     * Check if a student is withdrawn and restrict access.
     */
    private function isWithdrawn($student, $request)
    {
        if (strtolower($student->academic_status) === 'withdrawn') {
            $allowedRoutes = ['transactions.index', 'change.programme'];

            return !in_array($request->route()->getName(), $allowedRoutes);
        }

        return false;
    }
    
}
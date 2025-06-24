<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ProgrammeCategory;
use App\Models\Staff;
use App\Models\StaffProgramAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffProgramAssignmentController extends Controller
{
    
    // Assign a staff to a program category
    public function assign(Request $request)
    {
        $validated = $request->validate([
            'programme_category_id' => 'required|exists:programme_categories,id',
            'staff_id' => 'required|exists:staff,id',
            'role_in_programme' => 'required|in:Secretary,Coordinator',
            'status' => 'required|in:Active,Inactive,Suspended',
        ]);

        $userId = Auth::id(); // The person making the assignment

        $assignment = StaffProgramAssignment::create([
            'programme_category_id' => $validated['programme_category_id'],
            'staff_id' => $validated['staff_id'],
            'role_in_programme' => $validated['role_in_programme'],
            'status' => $validated['status'],
            'assigned_by_id' => $userId,
            'assigned_at' => now(),
            'slug' => \Str::slug("staff-{$validated['staff_id']}-program-{$validated['programme_category_id']}-".uniqid()),
        ]);

        if ($assignment) {
            alert()->success('Success', 'Staff assigned to program successfully.')->persistent('Close');
            return redirect()->back();
        }
        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function unassign(Request $request)
    {
        $request->validate([
            'staff_program_assignment_id' => 'required|exists:staff_program_assignments,id',
        ]);

        $assignment = StaffProgramAssignment::findOrFail($request->staff_program_assignment_id);

        if ($assignment->delete()) {         alert()->success('Success', 'Staff has been unassigned from the program.')->persistent('Close');
        } else {
            alert()->error('Oops!', 'Failed to unassign staff.')->persistent('Close');
        }

        return redirect()->back();
    }

}

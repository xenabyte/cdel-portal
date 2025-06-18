<?php

namespace App\Http\Controllers\Student;

use App\Models\Election;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

use App\Models\Position;
use App\Models\Candidate;
use App\Models\Student;
use App\Models\Vote;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class ElectionController extends Controller
{

    public function elections () {
        $student = Auth::guard('student')->user();

        // Only show elections where voting is currently ongoing
        $now = Carbon::now();

        $elections = Election::with(['positions', 'candidates'])
            ->where('start_time', '<=', $now)
            ->where('end_time', '>=', $now)
            ->get();

        return view('student.elections', [
            'student' => $student,
            'elections' => $elections,
        ]);
    }

    public function election($slug){

        $election = Election::where('slug', $slug)->with(['candidates', 'votes'])->first();

        if (!$election) {
            alert()->error('Not Found', 'Election not found')->persistent('Close');
            return redirect()->back();
        }

        return view('student.election', [
            'election' => $election,
        ]);
    }

    public function castVote(Request $request){
        $request->validate([
            'candidate_id' => 'required|exists:candidates,id',
        ]);

        $student = Auth::guard('student')->user();
        $candidate = Candidate::with('election', 'position')->findOrFail($request->candidate_id);
        $election = $candidate->election;

        // Check if election is ongoing
        if (now()->lt($election->start_time) || now()->gt($election->end_time)) {
            alert()->error('Voting Not Allowed', 'This election is not currently open.');
            return redirect()->back();
        }

        // Check if student has already voted for this position in this election
        $alreadyVoted = Vote::where('student_id', $student->id)
            ->whereHas('candidate', function ($query) use ($candidate) {
                $query->where('position_id', $candidate->position_id)
                    ->where('election_id', $candidate->election_id);
            })->exists();

        if ($alreadyVoted) {
            alert()->error('Vote Denied', 'You have already voted for this position.');
            return redirect()->back();
        }

        // Cast the vote
        Vote::create([
            'student_id' => $student->id,
            'candidate_id' => $candidate->id,
            'election_id' => $candidate->election_id,
            'position_id' => $candidate->position_id,
        ]);

        alert()->success('Vote Cast', 'Your vote has been recorded successfully.')->persistent('Close');
        return redirect()->back();
    }
}

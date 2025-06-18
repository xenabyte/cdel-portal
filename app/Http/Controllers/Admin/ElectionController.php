<?php

namespace App\Http\Controllers\Admin;

use App\Models\Election;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

use App\Models\Position;
use App\Models\Candidate;
use App\Models\Student;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;


class ElectionController extends Controller
{
    public function elections(){
        $elections = Election::all();

        return view('admin.elections', [
            'elections' => $elections
        ]);
    }


    public function createElection(Request $request){
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'type' => 'required|in:election,poll',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'description' => 'nullable|string',
            'eligible_group' => 'nullable|string|max:255',
            'show_result' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $data = $request->only(['title', 'type', 'description', 'start_time', 'end_time', 'eligible_group']);
        $data['show_result'] = $request->has('show_result');

        // Generate unique slug
        $slug = Str::slug($request->title);
        $originalSlug = $slug;
        $counter = 1;
        while (Election::where('slug', $slug)->exists()) {
            $slug = $originalSlug . '-' . $counter++;
        }
        $data['slug'] = $slug;

        // Handle image upload
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $request->image->move('uploads/elections', $imageName);
            $data['image'] = 'uploads/elections/' . $imageName;
        }

        $election = Election::create($data);

        alert()->success('Success', 'Election or poll created successfully')->persistent('Close');
        return redirect()->back();
    }

    public function updateElection(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'election_id'    => 'required|exists:elections,id',
            'title'          => 'required|string|max:255',
            'type'           => 'required|in:election,poll',
            'start_time'     => 'required|date',
            'end_time'       => 'required|date|after:start_time',
            'description'    => 'nullable|string',
            'eligible_group' => 'nullable|string|max:255',
            'show_result'    => 'nullable|boolean',
            'image'          => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $election = Election::find($request->election_id);
        if (!$election) {
            alert()->error('Error', 'Election not found.')->persistent('Close');
            return redirect()->back();
        }

        $election->title          = $request->title;
        $election->type           = $request->type;
        $election->start_time     = $request->start_time;
        $election->end_time       = $request->end_time;
        $election->description    = $request->description;
        $election->eligible_group = $request->eligible_group;
        $election->show_result    = $request->has('show_result');

        // Optional: regenerate slug on title change
        $election->slug = \Str::slug($request->title) . '-' . $election->id;

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($election->image && file_exists($election->image)) {
                unlink($election->image);
            }

            $imageName = time() . '.' . $request->image->extension();
            $request->image->move('uploads/elections', $imageName);
            $election->image = 'uploads/elections/' . $imageName;
        }

        $election->save();

        alert()->success('Success', 'Election updated successfully')->persistent('Close');
        return redirect()->back();
    }


    public function deleteElection(Request $request){

        $validator = Validator::make($request->all(), [
            'election_id' => 'required|exists:elections,id',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $election = Election::find($request->election_id);

        if ($election->image && file_exists($election->image)) {
            unlink($election->image);
        }

        $election->delete();

        alert()->success('Deleted', 'Election deleted successfully')->persistent('Close');
        return redirect()->back();
    }

    public function election($slug){
        $election = Election::where('slug', $slug)->with(['candidates', 'votes'])->first();

        if (!$election) {
            alert()->error('Not Found', 'Election not found')->persistent('Close');
            return redirect()->back();
        }

        return view('admin.election', [
            'election' => $election,
        ]);
    }

    public function addPosition(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'election_id' => 'required|exists:elections,id',
            'title' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Failed', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        Position::create([
            'election_id' => $request->election_id,
            'title' => $request->title,
        ]);

        alert()->success('Success', 'Position added successfully.')->persistent('Close');
        return redirect()->back();
    }

    public function deletePosition(Request $request)
    {
        $position = Position::find($request->position_id);
        if (!$position) {
            alert()->error('Error', 'Position not found.')->persistent('Close');
            return redirect()->back();
        }

        $position->delete();

        alert()->success('Deleted', 'Position deleted successfully.')->persistent('Close');
        return redirect()->back();
    }


    public function addCandidate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'election_id' => 'required|exists:elections,id',
            'position_id' => 'required|exists:positions,id',
            'matric_number' => 'required|string|exists:students,matric_number',
            'manifesto' => 'nullable|string',
            'photo' => 'nullable|image|max:2048',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Failed', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $student = Student::where('matric_number', $request->matric_number)->first();
        if (!$student) {
            alert()->error('Error', 'Student not found.')->persistent('Close');
            return redirect()->back();
        }


        $photoPath = null;
        if ($request->hasFile('photo')) {
            $imageName = time() . '.' . $request->photo->extension();
            $request->photo->move('uploads/election', $imageName);
            $photoPath = 'uploads/election/' . $imageName;
        }

        if($photoPath == null){
            $photoPath = $student->image;
        }
        Candidate::create([
            'election_id' => $request->election_id,
            'position_id' => $request->position_id,
            'student_id' => $student->id,
            'manifesto' => $request->manifesto,
            'photo' => $photoPath,
        ]);

        alert()->success('Success', 'Candidate added successfully.')->persistent('Close');
        return redirect()->back();
    }

    public function deleteCandidate(Request $request)
    {
        $candidate = Candidate::find($request->candidate_id);
        if (!$candidate) {
            alert()->error('Error', 'Candidate not found.')->persistent('Close');
            return redirect()->back();
        }

        // Optional: delete photo
        if ($candidate->photo && \Storage::disk('public')->exists($candidate->photo)) {
            \Storage::disk('public')->delete($candidate->photo);
        }

        $candidate->delete();

        alert()->success('Deleted', 'Candidate deleted successfully.')->persistent('Close');
        return redirect()->back();
    }
}

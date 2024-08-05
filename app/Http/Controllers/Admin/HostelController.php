<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Hostel;
use App\Models\RoomType;
use App\Models\RoomBedSpace;
use App\Models\Room;
use App\Models\Allocation;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class HostelController extends Controller
{
    //

    public function hostelType(){
        $roomTypes = RoomType::all();

        $roomTypesByCampus = $roomTypes->groupBy('campus');

        return view('admin.hostelType', [
            'roomTypesByCampus' => $roomTypesByCampus
        ]);
    }

    public function addRoomType(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'amount' => 'required|numeric',
            'campus' => 'required|string|in:' . RoomType::EAST_CAMPUS . ',' . RoomType::WEST_CAMPUS,
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $roomType = ([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'amount' => $request->amount * 100,
            'campus' => $request->campus,
            'gender' => $request->gender
        ]);

        if(RoomType::create($roomType)){
            alert()->success('Room Type added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateRoomType(Request $request){
        $validator = Validator::make($request->all(), [
            'room_type_id' => 'required|exists:room_types,id',
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer',
            'amount' => 'sometimes|numeric',
            'campus' => 'sometimes|string|in:' . RoomType::EAST_CAMPUS . ',' . RoomType::WEST_CAMPUS,
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $roomType = RoomType::findOrFail($request->room_type_id);

        $roomTypeData = array_filter([
            'name' => $request->name,
            'capacity' => $request->capacity,
            'campus' => $request->campus,
            'gender' => $request->gender,
        ]);

        if ($request->has('amount') && $request->amount*100 != $roomType->amount) {
            $roomTypeData['amount'] = $request->amount * 100;
        }

        if ($roomType->update($roomTypeData)) {
            alert()->success('Changes saved successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }


    public function deleteRoomType(Request $request){

        $validatedData = $request->validate([
            'room_type_id' => 'required|exists:room_types,id',
        ]);

        $roomType = RoomType::findOrFail($request->room_type_id);


        if($roomType->delete()){
            alert()->success('Record deleted successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function hostel(){
        $hostels = Hostel::get();
        
        return view('admin.hostel', [
            'hostels' => $hostels
        ]);
    }

    public function addHostel(Request $request){

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'gender' => 'required|string|in:' . RoomType::GENDER_MALE . ',' . RoomType::GENDER_FEMALE,
            'campus' => 'required|string|in:' . RoomType::EAST_CAMPUS . ',' . RoomType::WEST_CAMPUS,
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        $hostel = ([
            'slug' => $slug,
            'name' => $request->name,
            'campus' => $request->campus,
            'gender' => $request->gender,
        ]);

        if(Hostel::create($hostel)){
            alert()->success('Hostel added successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function updateHostel(Request $request){

        $validator = Validator::make($request->all(), [
            'hostel_id' => 'required|exists:hostels,id',
            'name' => 'sometimes|string|max:255',
            'campus' => 'sometimes|string|in:' . RoomType::EAST_CAMPUS . ',' . RoomType::WEST_CAMPUS,
            'gender' => 'required|string|in:' . RoomType::GENDER_MALE . ',' . RoomType::GENDER_FEMALE
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $request->name)));

        $hostel = Hostel::findOrFail($request->hostel_id);

        $hostelData = ([
            'slug' => $slug,
            'name' => $request->name,
            'campus' => $request->campus,
            'gender' => $request->gender
        ]);

        if($hostel->update($hostelData)){
            alert()->success('Changes saved successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function deleteHostel(Request $request){

        $validatedData = $request->validate([
            'hostel_id' => 'required|exists:hostels,id',
        ]);

        $hostel = Hostel::findOrFail($request->hostel_id);


        if($hostel->delete()){
            alert()->success('Record deleted successfully', '')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Oops!', 'Something went wrong')->persistent('Close');
        return redirect()->back();
    }

    public function viewHostel($slug){
        $hostel = Hostel::with('rooms', 'rooms.type')->where('slug', $slug)->first();
        $roomTypes = RoomType::where('campus', $hostel->campus)->get();

        return view('admin.hostelDetails', [
            'hostel' => $hostel,
            'roomTypes' => $roomTypes
        ]);
    }
}


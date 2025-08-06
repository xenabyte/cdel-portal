<?php

namespace App\Http\Controllers\Staff;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


use App\Models\Hostel;
use App\Models\RoomType;
use App\Models\RoomBedSpace;
use App\Models\Room;
use App\Models\Allocation;
use App\Models\Transaction;
use App\Models\Payment;

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class HostelController extends Controller
{
    public function hostelType() {
        $roomTypes = RoomType::all();
    
        $roomTypesByCampus = $roomTypes->groupBy('campus')->map(function ($group) {
            return $group->sortByDesc('capacity');
        });
    
        return view('staff.hostelType', [
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

        if (!is_null($request->name)) {
            $roomTypeData['name'] = $request->name;
        }

        if (!is_null($request->capacity)) {
            $roomTypeData['capacity'] = $request->capacity;
        }

        if (!is_null($request->campus)) {
            $roomTypeData['campus'] = $request->campus;
        }

        if ($request->has('gender')) {
            $roomTypeData['gender'] = $request->gender;
        }

        if ($request->has('amount') && $request->amount * 100 != $roomType->amount) {
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
        
        return view('staff.hostel', [
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
        $hostel = Hostel::with(['rooms' => function ($query) {
            $query->orderBy('id', 'desc');
        }, 'rooms.type', 'rooms.allocations'])
        ->where('slug', $slug)
        ->first();
        
        $roomTypes = RoomType::where('campus', $hostel->campus)
            ->orderByRaw('CAST(capacity AS UNSIGNED) DESC')
            ->get();

        $hostelRooms = $hostel->rooms->pluck('id');
        
        $hostelBedSpaces = RoomBedSpace::whereIn('room_id', $hostelRooms)->count();

        $allocatedBedSpaces = RoomBedSpace::whereIn('room_id', $hostelRooms)
        ->whereHas('currentAllocation') 
        ->count();

        return view('staff.hostelDetails', [
            'hostel' => $hostel,
            'roomTypes' => $roomTypes,
            'hostelBedSpaces' => $hostelBedSpaces,
            'allocatedBedSpaces' => $allocatedBedSpaces
        ]);
    }


    public function addRoom(Request $request){
        $validator = Validator::make($request->all(), [
            'number' => 'required',
            'type_id' => 'required|exists:room_types,id',
            'hostel_id' => 'required|exists:hostels,id',
        ]);
    
        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }
    
        $roomType = RoomType::findOrFail($request->type_id);
    
        $roomExists = Room::where('number', $request->number)
                          ->where('hostel_id', $request->hostel_id)
                          ->exists();
    
        if ($roomExists) {
            alert()->error('Error', 'Room with the same number already exists in this hostel')->persistent('Close');
            return redirect()->back();
        }
    
        $roomCapacity = $roomType->capacity;
    
        $room = [
            'number' => $request->number,
            'type_id' => $request->type_id,
            'hostel_id' => $request->hostel_id,
        ];
    
        if ($createRoom = Room::create($room)) {
            for ($i = 1; $i <= $roomCapacity; $i++) {
                RoomBedSpace::create([
                    'room_id' => $createRoom->id,
                    'space' => $i,
                ]);
            }
    
            alert()->success('Room added successfully', '')->persistent('Close');
            return redirect()->back();
        }
    }
    

    public function deleteRoom(Request $request){
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $roomId = $request->room_id;
        $room = Room::findOrFail($roomId);


        $roomAllocationExists = Allocation::where('room_id', $roomId)
                                ->where('academic_session', $academicSession)
                                ->exists();

        if ($roomAllocationExists) {
            alert()->error('Error', 'Room cannot be deleted as it is allocated for the current academic session')->persistent('Close');
            return redirect()->back();
        }

        DB::beginTransaction();

        try {
            RoomBedSpace::where('room_id', $room->id)->forceDelete();

            $room->forceDelete();

            DB::commit();

            alert()->success('Room and associated bed spaces deleted successfully', '')->persistent('Close');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            alert()->error('Error', 'Failed to delete room. Please try again.')->persistent('Close');
            return redirect()->back();
        }
    }

    public function reserveRoom(Request $request){
        $validator = Validator::make($request->all(), [
            'room_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }


        $roomId = $request->room_id;
        $room = Room::findOrFail($roomId);
        
        $room->is_reserved = $room->is_reserved === null ? 1 : null;

        DB::beginTransaction();

        try {
            $room->save();
            DB::commit();
            $statusMessage = $room->is_reserved ? 'Room reserved successfully' : 'Room reservation removed successfully';
            alert()->success($statusMessage, '')->persistent('Close');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();

            alert()->error('Error', 'Failed to update room reservation. Please try again.')->persistent('Close');
            return redirect()->back();
        }
    }

    public function allocations (Request $request){

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $totalBedSpaces = RoomBedSpace::get()->count();

        $allocations = Allocation::with(['student', 'room', 'room.hostel', 'room.type'])->where('academic_session', $academicSession)->get();

        return view('staff.allocations', [
            'allocations' => $allocations,
            'totalBedSpaces' => $totalBedSpaces
        ]);
    }


    public function deleteAllocation(Request $request){

        $validator = Validator::make($request->all(), [
            'allocation_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $allocationId = $request->allocation_id;

        $allocation = Allocation::findOrFail($allocationId);

        DB::beginTransaction();

        try {
            $allocation->delete();
            DB::commit();
           
            alert()->success('Room association reversed successfully', '')->persistent('Close');
            return redirect()->back();
        } catch (\Exception $e) {
            DB::rollback();
            alert()->error('Failed to delete allocation. Please try again.', '')->persistent('Close');
            return redirect()->back();
        }
    }


    public function allocateRoom(Request $request){
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|exists:students,id',
            'reference' => 'required',
            'campus' => 'required',
            'hostel_id' => 'required',
            'type_id' => 'required',
            'room_id' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $reference = $request->reference;
        $campus = $request->campus;
        $hostelId = $request->hostel_id;
        $typeId = $request->type_id;
        $roomId = $request->room_id;
        $studentId = $request->student_id;

        if(!$transaction = Transaction::where('reference', $reference)->where('student_id', $studentId)->first()){
            alert()->error('Error', 'Invalid Transaction Reference.')->persistent('Close');
            return redirect()->back();
        }

        $paymentId = $transaction->payment_id;
        $payment = Payment::with('structures')->where('id', $paymentId)->first();
        if(strtolower($payment->type) != strtolower(Payment::PAYMENT_TYPE_ACCOMMODATION)){
            alert()->error('Error', 'Transaction from reference supplied is not for accomondation purposes.')->persistent('Close');
            return redirect()->back();
        }

        $hostelData = array(
            "room_id" => $roomId,
            "hostel_id" => $hostelId,
            "campus" => $campus,
            "type_id" => $typeId,
        );
    
        $hostelMeta = json_encode($hostelData);
        $transaction->additional_data = $hostelMeta;
        $transaction->save();
    
        $roomType = RoomType::find($typeId);
        $amount = $roomType->amount;

        if(!$roomType) {
            alert()->error('Error', 'Selected room type not found.')->persistent('Close');
            return redirect()->back();
        }

        $room = Room::with('allocations', 'type', 'hostel')->find($roomId);
    
        if(!$room) {
            alert()->error('Error', 'Selected room not found.')->persistent('Close');
            return redirect()->back();
        }
    
        $totalBedSpaces = RoomBedSpace::where('room_id', $roomId)->count();
    
        $occupiedSpaces = Allocation::where('room_id', $roomId)
                                     ->whereNull('release_date')
                                     ->count();
    
        if($occupiedSpaces >= $totalBedSpaces) {
            alert()->error('Error', 'No available bed spaces in the selected room.')->persistent('Close');
            return redirect()->back();
        } 

        $transaction->refresh();
        $creditStudent = $this->creditAccommodation($transaction);
        if (is_string($creditStudent)) {
            alert()->error('Oops', $creditStudent)->persistent('Close');
        }

        alert()->success('Room allocated successfully', '')->persistent('Close');
        return redirect()->back();
    }
    
}


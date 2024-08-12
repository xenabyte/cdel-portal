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
    public function allocations (Request $request){

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $allocations = Allocation::with(['student', 'room', 'room.hostel', 'room.type'])->where('academic_session', $academicSession)->get();

        return view('staff.allocations', [
            'allocations' => $allocations,
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
        if(strtolower($payment->type) != strtolower(Payment::PAYMENT_TYPE_ACCOMONDATION)){
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


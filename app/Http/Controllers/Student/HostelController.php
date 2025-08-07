<?php

namespace App\Http\Controllers\Student;

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

use SweetAlert;
use Mail;
use Alert;
use Log;
use Carbon\Carbon;

class HostelController extends Controller
{
    //

    public function getHostels(Request $request) {
        $campus = $request->campus;
        $gender = $request->gender;
        $student = Auth::guard('student')->user();
        $applicantGender = !empty($student)? $student->applicant->gender : $gender;

        $hostels = Hostel::where('gender', $applicantGender)->where('campus', $campus)->get();

        return $hostels;
    }

    public function getRoomTypes(Request $request) {
        $campus = $request->campus;
        $gender = $request->gender;
        $hostelId = $request->hostelId;
        
        $student = Auth::guard('student')->user();
        $applicantGender = !empty($student)? $student->applicant->gender : $gender;

        $uniqueTypeIds = Room::where('hostel_id', $hostelId)
        ->whereHas('bedSpaces', function($query) {
            // Only include rooms where there is at least one bed space without an active allocation
            // $query->whereDoesntHave('currentAllocation');  // Only consider bed spaces without a current allocation
        })
        ->pluck('type_id')
        ->unique();


        $roomTypes = RoomType::whereIn('id', $uniqueTypeIds)->orderByRaw('CAST(capacity AS UNSIGNED) DESC')
        ->get();

        return $roomTypes;
    }

    public function getTypes(Request $request) {
        $campus = $request->campus;
        $gender = $request->gender;
        
        $student = Auth::guard('student')->user();
        $applicantGender = !empty($student)? $student->applicant->gender : $gender;
        $hostelIds = Hostel::where('campus', $campus)->pluck('id');

        // $uniqueTypeIds = Room::whereIn('hostel_id', $hostelIds)
        // ->whereHas('bedSpaces', function($query) {
            // $query->whereDoesntHave('currentAllocation');
        // })
        // ->pluck('type_id')
        // ->unique();

        // $roomTypes = RoomType::whereIn('id', $uniqueTypeIds)->orderByRaw('CAST(capacity AS UNSIGNED) DESC')
        // ->get();

        $roomTypes = RoomType::where('gender', $gender)->where('campus', $campus)->get();

        return $roomTypes;
    }


    public function getRooms(Request $request) {
        $typeId = $request->typeId;
        $hostelId = $request->hostelId;
        $student = Auth::guard('student')->user();

        $rooms = Room::where('type_id', $typeId)
            ->where('hostel_id', $hostelId)
            ->whereHas('bedSpaces', function($query) {
                $query->whereDoesntHave('currentAllocation');  // Only consider bed spaces without a current allocation
            })
            ->get();

        if ($student) {
            $rooms = Room::where('type_id', $typeId)
                ->where('hostel_id', $hostelId)
                ->where('is_reserved', null)
                ->whereHas('bedSpaces', function($query) {
                    $query->whereDoesntHave('currentAllocation');  // Only consider bed spaces without a current allocation
                })
                ->get();
        }

        return $rooms;
    }
}

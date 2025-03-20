<?php

namespace App\Http\Controllers\Student;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests;
use Illuminate\Support\Facades\Validator;

use App\Models\Payment;
use App\Models\Transaction;
use App\Models\Student;
use App\Models\StudentSuspension;
use App\Models\StudentExpulsion;
use App\Models\Notification;

use App\Mail\NotificationMail;

class StudentDisciplinaryController extends Controller
{
    //

    public function studentDisciplinary(){

        $student = Auth::guard('student')->user();

        $suspensions = $student->suspensions;

        return view('student.studentDisciplinary', [
            'suspensions' => $suspensions
        ]);

    }


    public function viewSuspension(Request $request, $slug){

        $globalData = $request->input('global_data');
        $academicSession = $globalData->sessionSetting['academic_session'];

        $suspension = StudentSuspension::where('slug', $slug)->first();
        $suspensionPayment = Payment::where("type", Payment::PAYMENT_TYPE_READMISSION_FEE)->where('academic_session', $academicSession)->first();

        return view('student.viewSuspension', [
            'suspension' => $suspension,
            'suspensionPayment' => $suspensionPayment
        ]);

    }


    public function manageSuspension(Request $request)
    {
        // Validation rules
        $validator = Validator::make($request->all(), [
            'suspension_id' => 'nullable|string',
            'court_affidavit' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'undertaking_letter' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'traditional_ruler_reference' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'ps_reference' => 'nullable|file|mimes:pdf,jpg,png,jpeg',
            'admin_comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            alert()->error('Validation Error', $validator->errors()->first())->persistent('Close');
            return redirect()->back();
        }

        $suspension = StudentSuspension::find($request->suspension_id);
        
        if (!$suspension) {
            alert()->error('Error', 'Suspension record not found.')->persistent('Close');
            return redirect()->back();
        }

        // Handle file uploads
        $fields = ['court_affidavit', 'undertaking_letter', 'traditional_ruler_reference', 'ps_reference'];
        foreach ($fields as $field) {
            if ($request->hasFile($field)) {
                // Delete existing file if any
                if ($suspension->$field) {
                    @unlink(public_path('uploads/' . $suspension->$field));
                }
                // Store new file
                $file = $request->file($field);
                $filename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('uploads'), $filename);
                $suspension->$field = $filename;
            }
        }

        // Update fields        
        if($suspension->save()){
            alert()->success('Success', 'Suspension details updated successfully.')->persistent('Close');
            return redirect()->back();
        }

        alert()->error('Error', 'Failed to update suspension details.')->persistent('Close');
        return redirect()->back();
    }
}

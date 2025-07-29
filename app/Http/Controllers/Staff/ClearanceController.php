<?php

namespace App\Http\Controllers\Staff;

use App\Models\ProgrammeCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use App\Libraries\Pdf\Pdf;

use App\Models\Allocation;

class ClearanceController extends Controller
{
    //

    public function resumptionClearance(){
        $programmeCategory = ProgrammeCategory::with('academicSessionSetting', 'examSetting')->where('category', ProgrammeCategory::UNDERGRADUATE)->first();
        $students = Student::where('programme_category_id', $programmeCategory->id)->where('is_active', true)->where('is_passed_out', false)->where('is_rusticated', false)->get();

        return view('staff.resumptionClearance', [
            'programmeCategory' => $programmeCategory,
            'students' => $students
        ]);
    }

    public function getStudentResumptionClearance(Request $request){

        $validator = Validator::make($request->all(), [
            'semester' => 'required',
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Student::find($request->student_id);
        if(!$student) {
            alert()->error('Error', 'Invalid Student')->persistent('Close');
            return redirect()->back();
        }

        $paymentCheck = $this->checkSchoolFees($student);
        $allocatedRoom = Allocation::where('student_id', $student->id)->where('academic_session', $student->academicSession)->first();

        
        $student->paymentCheck = $paymentCheck;

        return view('staff.resumptionClearance', [
            'student' => $student,
            'semester' => $request->semester,
            'allocatedRoom' => $allocatedRoom,
        ]);
    }

    public function generateResumptionClearance(Request $request) {
         $validator = Validator::make($request->all(), [
            'semester' => 'required',
            'student_id' => 'required',
        ]);

        if($validator->fails()) {
            alert()->error('Error', $validator->messages()->all()[0])->persistent('Close');
            return redirect()->back();
        }

        $student = Student::find($request->student_id);
        if(!$student) {
            alert()->error('Error', 'Invalid Student')->persistent('Close');
            return redirect()->back();
        }

        $paymentCheck = $this->checkSchoolFees($student);
        $allocatedRoom = Allocation::where('student_id', $student->id)->where('academic_session', $student->academicSession)->first();

        $student->paymentCheck = $paymentCheck;
        $student->roomAllocation = $allocatedRoom;

        $resumptionClearance = Pdf::generateResumptionClearance($student, $request->semester);

        return $resumptionClearance;
    }
}

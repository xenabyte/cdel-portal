<?php

namespace App\Libraries\Pdf;

use PDF as PDFDocument;
use App\Models\User as Applicant;
use App\Models\Student;
use App\Models\GlobalSetting as Setting;


use Log;

Class Pdf {

    public function generateAdmissionLetter($slug){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('programme', 'faculty', 'department', 'applicant')->where('slug', $slug)->first();
        $setting = Setting::first();

        $fileDirectory = 'uploads/files/admission/letters/'.$slug.'.pdf';

        $studentData = [
            'created_at' => $student->created_at,
            'jamb_reg_no' => $student->applicant->jamb_reg_no,
            'programme_name' => $student->programme->name,
            'duration' => $student->programme->duration,
            'department_name' => $student->department->name,
            'faculty_name' => $student->faculty->name,
            'student_name' => $student->applicant->lastname .' '. $student->applicant->othernames,
            'academic_session' => $student->academic_session,
            'application_type' => $student->application_type,
            'logo' => asset($setting->logo)
        ];

        $pdf = PDFDocument::loadView('pdf.admissionLetter', $studentData)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }

    public function generateCourseRegistration($studentId, $academicSession){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_modify' => true,
        ];

        $student = Student::with('applicant')->where('id', $studentId)->first();
        $name = $student->applicant->lastname.' '.$student->applicant->othernames;
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name .' '. $academicSession)));

        $fileDirectory = 'uploads/files/course_registration/'.$slug.'.pdf';
    }
}

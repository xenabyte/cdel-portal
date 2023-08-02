<?php

namespace App\Libraries\Pdf;

use Barryvdh\DomPDF\Facade as PDFDocument;
use App\Models\User as Applicant;
use App\Models\Student;

Class Pdf {

    public function generateAdmissionLetter($slug){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_print' => true,
            'no_modify' => true,
        ];

        $student = Student::with('programme', 'applicant')->where('slug', $slug)->first();

        $fileDirectory = 'uploads/files/admission/letters/'.$slug.'.pdf';

        $pdf = PDFDocument::loadView('pdf.admissionLetter', $student)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }
}

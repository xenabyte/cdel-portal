<?php

namespace App\Libraries\Pdf;

use Barryvdh\DomPDF\Facade as PDFDocument;
use App\Models\User as Applicant;

Class Pdf {

    public function generateAdmissionLetter($applicantSlug){
        $options = [
            'isRemoteEnabled' => true,
            'encryption' => '128',
            'no_print' => true,
            'no_modify' => true,
        ];

        $applicant = Applicant::with('programme', 'olevels', 'utmes')->where('slug', $applicantSlug)->first();

        $fileDirectory = 'uploads/files/admission/letters/'.$applicantSlug.'.pdf';

        $pdf = PDFDocument::loadView('pdf.admissionLetter', $applicant)
        ->setOptions($options)
        ->save($fileDirectory);

        return $fileDirectory;
    }
}

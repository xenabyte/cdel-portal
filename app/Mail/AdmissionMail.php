<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdmissionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicationData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($applicationData)
    {
        //
        $this->applicationData = $applicationData;
    }

    public function build()
    {

        $subject = 'Exciting News: Your Admission Has Been Granted, Welcome to '.env('SCHOOL_NAME');
        $message = $this->subject($subject)
            ->view('mail.admission.offer');

        if(!empty($this->applicationData->admission_letter)){
            if(env('SEND_ADMISSION_LETTER')){
                $message->attach($this->applicationData->admission_letter);
            }
            $message->attach('public/documents_for_resumption.pdf');
            $message->attach('public/dress_code.pdf');
        }
        
        return $message;
    }

}

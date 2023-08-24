<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudentActivated extends Mailable
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

        $subject = 'Your Official Institutional Email and Matriculation Number';
        $message = $this->subject($subject)
            ->view('mail.admission.student_activated');

        
        return $message;
    }

}

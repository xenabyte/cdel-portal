<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class StudyCenterOnboardingMail extends Mailable
{
    use Queueable, SerializesModels;


    public $senderName;
    public $studyCenter;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($senderName, $studyCenter)
    {
        //
        $this->senderName = $senderName;
        $this->studyCenter = $studyCenter;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "TAU: Study Center Registration - {$this->studyCenter->center_name}";
        $message = $this->subject($subject)
            ->view('mail.center.onboarding');

        
        return $message;
    }
}

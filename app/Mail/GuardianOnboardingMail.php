<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GuardianOnboardingMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guardianData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($guardianData)
    {
        $this->guardianData = $guardianData;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Guardian Onboarding Information';
        return $this->subject($subject)
            ->view('mail.onboarding.guardian');
    }
}

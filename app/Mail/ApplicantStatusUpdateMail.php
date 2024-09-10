<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantStatusUpdateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $applicant;
    public $messageContent;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($applicant, $messageContent)
    {
        $this->applicant = $applicant;
        $this->messageContent = $messageContent;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Your Application Status Update')
                    ->view('mail.career.applicant_status_update');
    }
}


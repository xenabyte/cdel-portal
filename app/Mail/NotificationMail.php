<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $senderName;
    public $messageBody;
    public $receiverName;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($senderName, $messageBody, $receiverName)
    {
        $this->senderName = $senderName;
        $this->messageBody = $messageBody;
        $this->receiverName = $receiverName;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'TAU: Message from ' . $this->senderName;
        return $this->subject($subject)
            ->view('mail.notification.generalNotification');
    }
}

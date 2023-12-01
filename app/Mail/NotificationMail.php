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
    public $attachment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($senderName, $messageBody, $receiverName, $attachment = null)
    {
        $this->senderName = $senderName;
        $this->messageBody = $messageBody;
        $this->receiverName = $receiverName;
        $this->attachment = $attachment;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'TAU: Message from ' . $this->senderName;
        $message = $this->subject($subject)
            ->view('mail.notification.generalNotification');

        if(!empty($this->attachment)){
            $message->attach($this->attachment);
        }
        
        return $message;
    }
}

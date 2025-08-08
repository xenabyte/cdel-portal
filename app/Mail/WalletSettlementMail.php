<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class WalletSettlementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $attachments;
    public $date;
    public $count;

    /**
     * Create a new message instance.
     *
     * @param array $attachmentsPaths
     * @return void
     */
   public function __construct(array $attachments, string $date, int $count)
    {
        $this->attachments = $attachments;
        $this->date = $date;
        $this->count = $count;
    }
    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Wallet Settlement Reports';

        $message = $this->subject($subject)
            ->view('mail.notification.walletSettlementMail', [
                'date' => $this->date,
                'count' => $this->count,
            ]);

        foreach ($this->attachments as $attachment) {
            $message->attach($attachment['file'], $attachment['options']);
        }

        return $message;
    }
}
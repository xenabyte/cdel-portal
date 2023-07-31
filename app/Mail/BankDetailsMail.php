<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class BankDetailsMail extends Mailable
{
    use Queueable, SerializesModels;

    public $bankData;
    public $name;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($bankData, $name)
    {
        //
        $this->bankData = $bankData;
        $this->name = $name;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = 'Bank Information';
        return $this->subject($subject)
            ->view('mail.transaction.bankInformation');
    }
}

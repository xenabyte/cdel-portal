<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TransactionMail extends Mailable
{
    use Queueable, SerializesModels;

    public $transactionData;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($transactionData)
    {
        //
        $this->transactionData = $transactionData;
    }

    public function build()
    {

        $subject = 'Payment Received - Receipt Attached';
        $message = $this->subject($subject)
            ->view('mail.transaction.payment');

        if(!empty($this->transactionData->invoice)){
            $message->attach($this->transactionData->invoice);
        }
        
        return $message;
    }

}
<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TACMail extends Mailable
{
    use Queueable, SerializesModels;

    public $tacCode;

    public function __construct($tacCode)
    {
        $this->tacCode = $tacCode;
    }

    public function build()
    {
        return $this->view('emails.tac')->with(['tacCode' => $this->tacCode]);
    }
}


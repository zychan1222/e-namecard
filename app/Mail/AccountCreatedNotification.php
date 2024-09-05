<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AccountCreatedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $employee;

    public function __construct($employee)
    {
        $this->employee = $employee;
    }

    public function build()
    {
        return $this->view('emails.account-created')
                    ->subject('Your Account Has Been Created')
                    ->with([
                        'name' => $this->employee->name,
                        'loginUrl' => route('login'),
                    ]);
    }
}
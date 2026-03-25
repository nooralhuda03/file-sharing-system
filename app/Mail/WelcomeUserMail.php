<?php

namespace App\Mail;

use Illuminate\Mail\Mailable;

class WelcomeUserMail extends Mailable
{
    public $user;

    public function __construct($user)
    {
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Welcome')
            ->html("
                <h2>Welcome {$this->user->name}</h2>
                <p>Your account has been created successfully.</p>
            ");
    }
}
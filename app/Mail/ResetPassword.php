<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $new_password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($user, $new_password)
    {
        $this->user = $user;
        $this->new_password = $new_password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->from(['address' => 'beerzha@gmail.com', 'name' => 'Beerzha'])
            ->subject('Сброс пароля')
            ->view('emails.resetPassword');
    }
}

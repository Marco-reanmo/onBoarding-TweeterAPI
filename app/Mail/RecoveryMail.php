<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use PhpParser\Node\Scalar\String_;

class RecoveryMail extends Mailable
{
    use Queueable, SerializesModels;

    private String $username;
    private String $newPassword;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $username, String $newPassword)
    {
        $this->username = $username;
        $this->newPassword = $newPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('MAIL_FROM_ADDRESS'), 'DoNotReply')
            ->subject('Password Reset')
            ->markdown('mails.recovery')
            ->with([
                'name' => $this->username,
                'newPassword' => $this->newPassword,
                'link' => 'http://localhost:8000/api/login'
            ]);
    }
}

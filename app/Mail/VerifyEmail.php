<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    private String $username;

    private String $verificationString;


    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $username, String $randomString)
    {
        $this->verificationString = $randomString;
        $this->username = $username;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from(config('MAIL_FROM_ADDRESS'), 'DoNotReply')
            ->subject('Verify your Tweeter Account')
            ->markdown('mails.verification')
            ->with([
                'name' => $this->username,
                'link' => 'http://localhost:8000/api/verify/'. $this->verificationString
            ]);
    }
}

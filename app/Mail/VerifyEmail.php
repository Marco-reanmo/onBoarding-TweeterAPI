<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VerifyEmail extends Mailable
{
    use Queueable, SerializesModels;

    private String $verificationString;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(String $randomString)
    {
        $this->verificationString = $randomString;
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
            ->markdown('mails.verifcation')
            ->with([
                'name' => 'Subscriber',
                'link' => 'http://localhost:8000/api/users/'. $this->verificationString .'/verify'
            ]);
    }
}

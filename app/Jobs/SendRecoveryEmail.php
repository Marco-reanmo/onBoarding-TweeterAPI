<?php

namespace App\Jobs;

use App\Mail\RecoveryMail;
use App\Mail\VerifyEmail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendRecoveryEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private User $user;
    private String $newPassword;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, String $newPassword)
    {
        $this->user = $user;
        $this->newPassword = $newPassword;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->user->getAttribute('email'))->send(new RecoveryMail($this->user->getAttribute('forename'), $this->newPassword));
        info('Successfully sent RecoveryMail to User ' . $this->user->getAttribute('uuid'));
    }
}

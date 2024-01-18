<?php

namespace App\Core\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{
    protected function verificationUrl($notifiable)
    {
        return URL::signedRoute(
            'verification.verify',
            Carbon::now()->addMinute(2880),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}

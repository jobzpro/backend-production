<?php

namespace App\Core\Auth;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Config;

class VerifyEmail extends \Illuminate\Auth\Notifications\VerifyEmail
{
    protected function verificationUrl($notifiable)
    {
        return URL::signedRoute(
            'verification.verify',
            // Carbon::now()->addMinute(2880),
            Carbon::now()->addMinutes(Config::get('auth.verification.expire', 60)),
            [
                'id' => $notifiable->getKey(),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}

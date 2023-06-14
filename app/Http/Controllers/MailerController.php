<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mailer;
use App\Mail\PasswordReset;
use App\Mail\SuccessEmail;

class MailerController extends Controller
{
    public function sendResetPasswordEmail($user, $link){

        $mailData = [
            'user_first_name' => $user->first_name,
            'link' => $link,
        ];

        Mail::to($user->email)->send(new PasswordReset($mailData));
        
    }

    public function sendSuccessEmail($email){
        
        Mail::to($email)->send(new SuccessEmail());
        return true;
    }
}

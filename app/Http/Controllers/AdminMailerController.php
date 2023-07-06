<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\AdminNotifications;
use Illuminate\Support\Facades\Mail;

class AdminMailerController extends Controller
{
    public function newEmployerSignUp(){
        $email = "admin@jobzpro.com";
        $subject = "New Company Sign Up";

        $mailData = [
            'body' => 'New employer has joined jobzpro. Please review for verification.',
        ];

        Mail::to($email)->send(new AdminNotifications($mailData, $subject));
    }
}

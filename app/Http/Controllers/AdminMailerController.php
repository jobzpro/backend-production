<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\AdminNotifications;
use App\Mail\EmployerSignUpApproved;
use App\Mail\ReportResponse;
use Illuminate\Support\Facades\Mail;

class AdminMailerController extends Controller
{
    public function newEmployerSignUp()
    {
        $email = "admin@jobzpro.com";
        $subject = "New Company Sign Up";

        $mailData = [
            'body' => 'New employer has joined jobzpro. Please review for verification.',
        ];

        Mail::to($email)->send(new AdminNotifications($mailData, $subject));
    }

    public function sendApprovalMail($email)
    {
        $subject = "Welcome to Jobzpro";

        $mailData = [
            'body' => 'Hi! Thank you for signing up for jobzpro. You can now sign-in using the credentials that were sent earlier.'
        ];

        Mail::to($email)->send(new AdminNotifications($mailData, $subject));
    }

    public function employerApproved($email)
    {
        Mail::to($email)->send(new EmployerSignUpApproved());
    }

    public function sendReportResponseMail($email, $subject, $response)
    {
        $mailData = [
            'body' => $response,
        ];
        Mail::to($email)->send(new ReportResponse($mailData, $subject));
    }
}

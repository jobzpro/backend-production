<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationSubmitted;
use App\Mail\EmployerSignUpSuccess;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mailer;
use App\Mail\PasswordReset;
use App\Mail\SuccessEmail;

class MailerController extends Controller
{
    public function sendResetPasswordEmail($user, $link){

        $mailData = [
            'user_first_name' => $user->user->first_name,
            'link' => $link,
        ];

        Mail::to($user->email)->send(new PasswordReset($mailData));
    }

    public function sendSuccessEmail($email){
        
        Mail::to($email)->send(new SuccessEmail());
        return true;
    }

    public function sendEmployerSuccessEmail($company, $user, $password){
        $mailData = [
            'company_name' => $company->name,
            'user_name' => $user->first_name." ".$user->last_name,
            'email' => $user->account->email,
            'temp_password' => $password,
        ];

        Mail::to($user->account->email)->send(new EmployerSignUpSuccess($mailData));
        return true;
    }

    public function sendApplicationSuccess($user, $company, $joblist){
        $mailData = [
            'company_name' => $company->name,
            'user_name' => $user->first_name,
            'job_list' => $joblist->job_title,
        ];

        Mail::to($user->account->email)->send(new ApplicationSubmitted($mailData));
    }

}

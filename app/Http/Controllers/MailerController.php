<?php

namespace App\Http\Controllers;

use App\Mail\ApplicationSubmitted;
use App\Mail\EmployerSignUpApproved;
use App\Mail\EmployerSignUpSuccess;
use App\Mail\JobInterviews;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\Mailer;
use App\Mail\PasswordReset;
use App\Mail\SuccessEmail;
use App\Models\User;

class MailerController extends Controller
{
    public function sendResetPasswordEmail($user, $link)
    {

        $mailData = [
            'user_first_name' => $user->user->first_name,
            'link' => $link,
        ];

        Mail::to($user->email)->send(new PasswordReset($mailData));
    }

    public function sendSuccessEmail($email)
    {

        Mail::to($email)->send(new SuccessEmail());
        return true;
    }

    public function employerApproved($company, $user, $password)
    {
        $mailData = [
            'company_name' => $company->name,
            'user_name' => $user->first_name . " " . $user->last_name,
            'email' => $user->account->email,
            'temp_password' => $password,
        ];

        Mail::to($user->account->email)->send(new EmployerSignUpApproved($mailData));
    }

    public function sendEmployerSuccessEmail($company, $user, $password)
    {
        $mailData = [
            'company_name' => $company->name,
            'user_name' => $user->first_name . " " . $user->last_name,
            'email' => $user->account->email,
            'temp_password' => $password,
        ];

        Mail::to($user->account->email)->send(new EmployerSignUpSuccess($mailData));
        return true;
    }

    public function sendApplicationSuccess($user, $company, $joblist)
    {
        $mailData = [
            'company_name' => $company->name,
            'user_name' => $user->first_name,
            'job_list' => $joblist->job_title,
        ];

        Mail::to($user->account->email)->send(new ApplicationSubmitted($mailData));
    }

    public function sendInterviewInvite($company, $jobInterview)
    {
        $applicant = User::find($jobInterview['applicant_id']);
        $subject = "Job Application Update";

        $mailData = [
            'applicant_name' => $applicant->first_name,
            'company_name' => $company->name,
            'meeting_link' => $jobInterview['meeting_link'],
            'interview_date' => $jobInterview['interview_date'],
        ];

        Mail::to($applicant->account->email)->send(new JobInterviews($mailData, $subject));
    }
}

<?php

namespace App\Http\Controllers;

use App\Mail\JobApplications;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;
use App\Mail\InviteStaff;

class EmployerMailerController extends Controller
{

    public function applicantApplied($applicant, $employer, $company, $job_list){
        return response()->json(['employer' => $employer, 'employer user' => $employer->user ?? '', 'employer user account' => $employer->user->account ?? '', 'employer email' => $employer->user->account->email ?? ''], 200);
        $to = $employer->user->account->email;
        $jobApp = JobApplication::where('user_id', $applicant->id)->first();

        $mailData = [
            'employer_name' => $employer->user->first_name,
            'company_name' => $company->name,
            'applicant' => $applicant->first_name . " ". $applicant->last_name,
            'job_list' => $job_list->job_title,
        ];

        $resume = $jobApp->resume_path;

        if($resume != null){
            $attachment[] = Attachment::fromPath($resume)->as('resume.pdf')->withMime('application/pdf');
        }else{
            $attachment = [];
        }

        Mail::to($to)->send(new JobApplications($mailData,$attachment));

    }

    public function sendEmployerStaffInvite($company, $email, $user, $link){

        $mailData = [
            'company_name' => $company->name,
            'employee_name' => $user->first_name . " ". $user->last_name,
            'link' => $link,
        ];

        Mail::to($email)->send(new InviteStaff($mailData));
    }
}

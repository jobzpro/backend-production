<?php

namespace App\Http\Controllers;

use App\Mail\JobApplications;
use App\Models\JobApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Support\Facades\Storage;

class EmployerMailerController extends Controller
{
    
    public function applicantApplied($applicant, $employer, $company, $job_list){

        $to = $employer->user->account->email;
        $jobApp = JobApplication::where('user_id', $applicant->id)->first();

        $mailData = [
            'company_name' => $company->name,
            'applicant' => $applicant->first_name . " ". $applicant->last_name,
            'job_list' => $job_list,
        ];

        $resume = $jobApp->resume_path;

        if($resume != null){
            $attachment[] = Attachment::fromPath($resume)->as('resume.pdf')->withMime('application/pdf');
        }else{
            $attachment = [];
        }

        Mail::to($to)->send(new JobApplications($mailData,$attachment));
    
    }
}

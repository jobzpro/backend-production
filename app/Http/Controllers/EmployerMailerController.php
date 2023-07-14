<?php

namespace App\Http\Controllers;

use App\Mail\JobApplications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class EmployerMailerController extends Controller
{
    
    public function applicantApplied($applicant, $employer, $company){
        //dd("here");
        $to = $employer->user->account->email;
        $mailData = [
            'company_name' => $company->name,
            'applicant' => $applicant->first_name
        ];

        //$attachment = null;

        Mail::to($to)->send(new JobApplications($mailData));
    
    }
}

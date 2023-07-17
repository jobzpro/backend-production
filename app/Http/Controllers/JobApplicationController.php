<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\FileAttachment;
use App\Models\JobApplication;
use Illuminate\Support\Facades\Storage;
use App\Enums\JobApplicationStatus as application_status;
use App\Models\Company;
use App\Models\JobList;
use App\Models\UserCompany;
use App\Models\UserNotification;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\EmployerMailerController as EmployerMailerController;
class JobApplicationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id){
        $job_list = JobList::find($id);
        $user = User::find($request->user()->id);
        $resume = null;
        $company = Company::find($job_list->company_id)->first();

        $user_companies = UserCompany::where('company_id', $company->id)->get();


        if($request->has('file')){
            $filesValidator = Validator::make($request->all(),[
                'files.*' => 'mimes:pdf,doc,docx,txt|max:2048',
            ]);

            if($filesValidator->fails()){
                return response([
                    'message' => "Invalid file.",
                    'errors' => $filesValidator->errors(),
                ],400);
            }else{
                $path = 'files';
                $file = $request->file('file');

                $fileName = time(). $file->getClientOriginalName();
                $filePath = Storage::disk('s3')->put($path, $file);
                $filePath   = Storage::disk('s3')->url($filePath);
                $file_type  = $file->getClientOriginalExtension();
                $fileSize   = $this->fileSize($file);

                $resume = FileAttachment::create([
                    'name' => $fileName,
                    'user_id' => $user->id,
                    'path' => $filePath,
                    'type' => $file_type,
                    'size' => $fileSize 
                ]);
            }
        }

        if($user_companies){
            //dd($user_companies);
            foreach($user_companies as $employer){
                //dd($employer);
                (new EmployerMailerController)->applicantApplied($user, $employer, $company, $job_list);
            }
        }


        // $job_application = JobApplication::create([
        //     'user_id' => $user->id,
        //     'job_list_id' => $job_list->id,
        //     'status' => application_status::unread,
        //     'applied_at' => Carbon::now(),
        //     'resume_path' => $resume,
        // ]);

        // UserNotification::create([
        //     'job_application_id' => $job_application->id,
        //     'user_id' => $user->id,
        //     'title' => "Job Application Successfully submitted.",
        //     'description' => "Your application to ". $job_list->company->name ." has been successfully submitted. A company representative will reach out you if you got shortlisted.",
        //     'is_Read' => false,
        // ]);


        // return response([
        //     'message' => 'Application Successfully Submitted',
        // ],200);

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    { 
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function retractApplication(Request $request, $id){
        $job_application = JobApplication::find($id);
        $user_id = $request->user()->id;

        $validator = Validator::make($request->all(), [
            'reason' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'errors' => $validator->errors(),
            ],400);
        }

        if($user_id == $job_application->user_id){
            $job_application->update([
                'status' => application_status::user_retracted,
                'reason' => $request['reason'],
            ]);

            return response([
                'message' => 'Application successfully retracted',
            ],200);
        }else{

            return response([
                'message' => 'Unauthorized',
            ],400);
        }

    }
}

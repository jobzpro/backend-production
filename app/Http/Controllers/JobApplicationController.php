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
use App\Helper\FileManager;
use App\Models\CompanyNotification;
use App\Http\Controllers\MailerController as MailerController;

class JobApplicationController extends Controller
{
    use FileManager;
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
        $company = Company::find($job_list->company_id);
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
                $fileName = time().$file->getClientOriginalName();
                $filePath = Storage::disk('s3')->put($path, $file);
                $filePath   = Storage::disk('s3')->url($filePath);
                $file_type  = $file->getClientOriginalExtension();
                $fileSize   = $this->fileSize($file);

                $r = FileAttachment::create([
                    'name' => $fileName,
                    'user_id' => $user->id,
                    'path' => $filePath,
                    'type' => $file_type,
                    'size' => $fileSize 
                ]);

                $resume = $r->path;
            }
        }else{
            $resume = null;
        }

        $job_application = JobApplication::create([
            'user_id' => $user->id,
            'job_list_id' => $job_list->id,
            'status' => application_status::unread,
            'applied_at' => Carbon::now(),
            'resume_path' => $resume,
            'authorized_to_work_in_us' => $request->authorized_to_work_in_us ?? null,
            'vaccinated_with_booster' => $request->vaccinated_with_booster ?? null,
            'able_to_commute' => $request->able_to_commute ?? null
        ]);

        UserNotification::create([
            'job_application_id' => $job_application->id,
            'user_id' => $user->id,
            'title' => "Job Application Successfully submitted.",
            'description' => "Your application to ". $job_list->company->name ." has been successfully submitted. A company representative will reach out you if you got shortlisted.",
            'is_Read' => false,
        ]);

        CompanyNotification::create([
            'company_id' => $company->id,
            'job_list_id' => $job_list->id,
            'title' => "A jobseeker has applied for ". $job_list->job_title,
            'description' => "You can review and see their profile to check if the applicant is qualified.",
            'is_Read' => false,
        ]);


        if($user_companies){
            foreach($user_companies as $employer){
                (new EmployerMailerController)->applicantApplied($user, $employer, $company, $job_list);
            }
        }

        if($user){
            (new MailerController)->sendApplicationSuccess($user, $company, $job_list);
        }

        return response([
            'message' => 'Application Successfully Submitted',
        ],200);

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
        $user = User::find($request->user()->id);
        
        if($user->userRole->first()->role->role_name == "Jobseeker"){
            $job_application = JobApplication::find($id);
            
            $validator = Validator::make($request->all(), [
                'reason' => 'required',
            ]);

            if($validator->fails()){
                return response([
                    'errors' => $validator->errors(),
                ],400);
            }

            if($user->id == $job_application->user_id){
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
        }else{

            return response([
                'messsage' => 'Unauthorized',
            ]);
        }

    }
}

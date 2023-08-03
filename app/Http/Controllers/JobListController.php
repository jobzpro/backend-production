<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobBenefits;
use App\Models\JobList;
use App\Models\JobLocation;
use App\Models\JobType;
use App\Models\User;
use App\Models\CompanyNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Enums\JobListStatusEnum as job_status;
use App\Helper\FileManager;
use App\Models\FileAttachment;
use App\Models\JobIndustryPhysicalSetting;
use App\Models\JobIndustrySpeciality;
use App\Models\JobStandardShift;
use App\Models\JobSupplementalSchedule;
use App\Models\JobWeeklySchedule;
use Illuminate\Support\Facades\Storage;

class JobListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use FileManager;

    public function index()
    {
        $jobLists = JobList::all();

        return response([
            'job_list' => $jobLists->paginate(10),
        ],200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $user = User::find($request->user()->id);
        $userCompany = $user->userCompanies()->first()->companies()->first();

        $fileValidator = Validator::make($request->all(),[
            'files' => 'file|mimes:pdf,doc,docx,txt|max:4000',
        ]);

        if($fileValidator->fails()){
            return response([
                "message" => "Invalid File",
                "errors" => $fileValidator->errors(),
            ]);
        }else{
            $file_attachments = $this->uploadFile($request['files'],$request->user()->id);
        }

        if($request->route('id') == $userCompany->id){
            $validator = Validator::make($request->all(),[
                'job_title' => 'required',
            ]);

            if($validator->fails()){
                return response([
                    'message' => "Job posting unsuccessful.",
                    'errors' => $validator->errors(),
                ],400);
            }

            $job_list = JobList::create([
                'company_id' => $userCompany->id,
                'job_title' => $data['job_title'],               
                'description' => $data['description'] ?? null,
                'show_pay' => $data['show_pay'] ?? null,
                'pay_type' => $data['pay_type'] ?? null,
                'salary' => $data['salary'] ?? null,
                'min_salary' => $data['min_salary'] ?? null,
                'max_salary' => $data['max_salary'] ?? null,
                'experience_level_id' => $data['experience_level_id'] ?? null,
                'number_of_vacancies' => $data['number_of_vacancies'] ?? null,
                'hiring_urgency' => $data['hiring_urgency'] ?? null,
                'status' => job_status::Published,
                'can_applicant_with_criminal_record_apply' => $data['can_applicant_with_criminal_record_apply'] ?? null,
                'can_start_messages' => $data['can_start_messages'] ?? null,
                'send_auto_reject_emails' => $data['send_auto_reject_emails'] ?? null,
                'auto_reject' => $data['auto_reject'] ?? null,
                'time_limit' => $data['time_limit'] ?? null,
                'other_email' => $data['other_email'] ?? null,
                'industry_id' => $data['industry_id'] ?? null,
                'files' => $file_attachments,
            ]);

            
            $job_location = JobLocation::create([
                'job_list_id' => $job_list->id,
                'location' => $data['location'],
                'address' => $data['address'] ?? null,
                'description' => $data['address_description'] ?? ""
            ]);

            if($request->filled('job_types')){
                $job_types = explode(",", $data['job_types']);
                for($i = 0; $i<sizeof($job_types); $i++){
                    JobType::create([
                        'job_list_id' => $job_list->id,
                        'type_id' => $job_types[$i],
                    ]);
                }
            }

            if($request->filled('industry_physical_setting')){
                $job_industry_physical_settings = explode(",", $data['industry_physical_setting']);
                for($i = 0; $i<sizeof($job_industry_physical_settings); $i++){
                    JobIndustryPhysicalSetting::create([
                        'job_list_id' => $job_list->id,
                        'industry_physical_setting_id' => $job_industry_physical_settings[$i],
                    ]);
                }
            }

            if($request->filled('industry_speciality')){
                $job_industry_specialities = explode(",", $data['industry_speciality']);
                for($i = 0; $i<sizeof($job_industry_specialities); $i++){
                    JobIndustrySpeciality::create([
                        'job_list_id' => $job_list->id,
                        'industry_speciality_id' => $job_industry_specialities[$i],
                    ]);
                }
            }


            if($request->filled('benefits')){
                $job_benefits = explode(',', $data['benefits']);
                for($i = 0; $i<sizeof($job_benefits); $i++){
                    JobBenefits::create([
                        'job_list_id' => $job_list->id,
                        'benefit_id' => $job_benefits[$i],
                    ]);
                }
            }

            if($request->filled('standard_shift')){
                $job_standard_shift = explode(',', $data['standard_shift']);
                for($i = 0; $i<sizeof($job_standard_shift); $i++){
                    JobStandardShift::create([
                        'job_list_id' => $job_list->id,
                        'standard_shift_id' => $job_standard_shift[$i],
                    ]);
                }
            }

            if($request->filled('weekly_schedule')){
                $job_weekly_schedule = explode(',', $data['weekly_schedule']);
                for($i = 0; $i<sizeof($job_weekly_schedule); $i++){
                    JobWeeklySchedule::create([
                        'job_list_id' => $job_list->id,
                        'weekely_shift_id' => $job_weekly_schedule[$i],
                    ]);
                }
            }

            if($request->filled('supplementary_schedule')){
                $job_supplementary_schedule = explode(",", $data['supplementary_schedule']);
                for($i = 0; $i<sizeof($job_supplementary_schedule); $i++){
                    JobSupplementalSchedule::create([
                        'job_list_id' => $job_list->id,
                        'supplementary_schedule_id' => $job_supplementary_schedule[$i],
                    ]);
                }
            }


            $notification = CompanyNotification::create([
                'title' => "Job Successfully Posted!",
                'description' => "Job list ".$job_list->job_title." has been successfully posted.",
                'company_id' => $userCompany->id,
                'job_list_id' => $job_list->id,
            ]);

            
            return response([
                'job_list' => $job_list,
                'message' => "Job posted successfully."
            ],200);



        }else{
            return response([
                'message' => "Unauthorized."
            ],401);
        }

    }

    /**
     * Display the specified resource.
     */
    public function show(JobList $jobList)
    {
        return response([
            'job_list' => $jobList->paginate(10),
        ],200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobList $jobList)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobList $jobList)
    {
        //
    }

    public function saveJobListAsDraft(Request $request){
        $data = $request->all();
        $user = User::find($request->user()->id);
        $userCompany = $user->userCompanies()->first()->companies()->first();

        $fileValidator = Validator::make($request->all(),[
            'files' => 'file|mimes:pdf,doc,docx,txt|max:4000',
        ]);

        if($fileValidator->fails()){
            return response([
                "message" => "Invalid File",
                "errors" => $fileValidator->errors(),
            ]);
        }else{
            $file_attachments = $this->uploadFile($request['files'],$request->user()->id);
        }


        if($request->route('id') == $userCompany->id){
            $validator = Validator::make($request->all(),[
                'job_title' => 'required',
            ]);

            if($validator->fails()){
                return response([
                    'message' => "Job posting unsuccessful.",
                    'errors' => $validator->errors(),
                ],400);
            }

            $job_list = JobList::create([
                'company_id' => $userCompany->id,
                'job_title' => $data['job_title'],               
                'description' => $data['description'] ?? null,
                'show_pay' => $data['show_pay'] ?? null,
                'pay_type' => $data['pay_type'] ?? null,
                'salary' => $data['salary'] ?? null,
                'min_salary' => $data['min_salary'] ?? null,
                'max_salary' => $data['max_salary'] ?? null,
                'experience_level_id' => $data['experience_level_id'] ?? null,
                'number_of_vacancies' => $data['number_of_vacancies'] ?? null,
                'hiring_urgency' => $data['hiring_urgency'] ?? null,
                'status' => job_status::Published,
                'can_applicant_with_criminal_record_apply' => $data['can_applicant_with_criminal_record_apply'] ?? null,
                'can_start_messages' => $data['can_start_messages'] ?? null,
                'send_auto_reject_emails' => $data['send_auto_reject_emails'] ?? null,
                'auto_reject' => $data['auto_reject'] ?? null,
                'time_limit' => $data['time_limit'] ?? null,
                'other_email' => $data['other_email'] ?? null,
                'industry_id' => $data['industry_id'] ?? null,
                'files' => $file_attachments,
            ]);

            
            $job_location = JobLocation::create([
                'job_list_id' => $job_list->id,
                'location' => $data['location'],
                'address' => $data['address'] ?? null,
                'description' => $data['address_description'] ?? ""
            ]);

            if($request->filled('job_types')){
                $job_types = explode(",", $data['job_types']);
                for($i = 0; $i<sizeof($job_types); $i++){
                    JobType::create([
                        'job_list_id' => $job_list->id,
                        'type_id' => $job_types[$i],
                    ]);
                }
            }

            if($request->filled('industry_physical_setting')){
                $job_industry_physical_settings = explode(",", $data['industry_physical_setting']);
                for($i = 0; $i<sizeof($job_industry_physical_settings); $i++){
                    JobIndustryPhysicalSetting::create([
                        'job_list_id' => $job_list->id,
                        'industry_physical_setting_id' => $job_industry_physical_settings[$i],
                    ]);
                }
            }

            if($request->filled('industry_speciality')){
                $job_industry_specialities = explode(",", $data['industry_speciality']);
                for($i = 0; $i<sizeof($job_industry_specialities); $i++){
                    JobIndustrySpeciality::create([
                        'job_list_id' => $job_list->id,
                        'industry_speciality_id' => $job_industry_specialities[$i],
                    ]);
                }
            }

            if($request->filled('benefits')){
                $job_benefits = explode(",", $data['benefits']);
                for($i = 0; $i<sizeof($job_benefits); $i++){
                    JobBenefits::create([
                        'job_list_id' => $job_list->id,
                        'benefit_id' => $job_benefits[$i],
                    ]);
                }
            }

            if($request->filled('standard_shift')){
                $job_standard_shift = explode(",", $data['standard_shift']);
                for($i = 0; $i<sizeof($job_standard_shift); $i++){
                    JobStandardShift::create([
                        'job_list_id' => $job_list->id,
                        'standard_shift_id' => $job_standard_shift[$i],
                    ]);
                }
            }

            if($request->filled('weekly_schedule')){
                $job_weekly_schedule = explode(",",$data['weekly_schedule']);
                for($i = 0; $i<sizeof($job_weekly_schedule); $i++){
                    JobWeeklySchedule::create([
                        'job_list_id' => $job_list->id,
                        'weekly_schedule_id' => $job_weekly_schedule[$i],
                    ]);
                }
            }

            if($request->filled('supplementary_schedule')){
                $job_supplementary_schedule = explode(",",$data['supplementary_schedule']);
                for($i = 0; $i<sizeof($job_supplementary_schedule); $i++){
                    JobSupplementalSchedule::create([
                        'job_list_id' => $job_list->id,
                        'supplemental_schedules_id' => $job_supplementary_schedule[$i],
                    ]);
                }
            }

            return response([
                'job_list' => $job_list,
                'message' => "Job list is saved as draft successfully."
            ],200);


        }else{
            return response([
                'message' => "Unauthorized."
            ],401);
        }
    }


    public function showJobsByCompany(Request $request){
        $c_jobs_list = JobList::with('job_type')->where('company_id', $request['company_id'])->get();

        return response([
            'job_list' => $c_jobs_list->paginate(10),
        ],200);
    }

    public function createJobType($id, Request $request){
        $data = $request->all();


        $job_type = JobType::create([
            'type_id' => $data['type_id'],
            'job_id' => $id,
        ]);


        return response([
            'job_type' => $job_type,
            'message' => "success",
        ],200);

    }

    public function createJobBenefits($id, Request $request){
        $data = $request->all();

        $job_benefits = JobBenefits::create([
            'job_id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);

        return response([
            'job_benefits' => $job_benefits,
            'message' => "Success"
        ],200);
    }

    public function getAllApplicantsForJobList($id){
        $company = Company::find($id);
        $job_list_id = request()->job_list_id;
        $job_applications = JobApplication::where('job_list_id', $job_list_id)->get();
        $applicants = [];
        foreach($job_applications as $job_application){
           array_push($applicants, $job_application->user);
        }

        return response([
            'applicants' => collect($applicants)->paginate(10),
        ]);
    }

    public function getJobListings($id){
        $company_id = $id;
        $job_lists = JobList::with('job_types')->where('company_id', $company_id)->get();
        $results = [];
        $types = [];

        foreach($job_lists as $job_list){
            $result = [
                'job_list_title' => $job_list->job_title,
                'hiring' => $job_list->number_of_vacancies,
                'applied' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Unread")->count(),
                'interview' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Interview")->count(),
                'accepted' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Accepted")->count(),
                'status' => $job_list->status,
                'date_created' => Carbon::parse($job_list->created_at)->format('d/m/Y'),
                'job_types' => $types,

            ]; 
            foreach($job_list->job_types as $jtype){
                $type = $jtype->type;
                array_push($types, $type);
            }

            array_push($results, $result);
        }

        return response([
            'job_lists' => collect($results)->paginate(10),
        ]);
    } 

    public function archiveJobList($id){
        $company = Company::find($id);
        $job_list_id = request()->job_list_id;

        //dd($company->JobListings->find(2));

        $job_list = $company->JobListings->find($job_list_id);
        
        $job_list->update([
            'status' => job_status::Archived,
        ]);

        return response([
            'message' => "Job list successfully archived"
        ],200);

    }

    public function publishJobList($id){
        $company = Company::find($id);
        $job_list_id = request()->job_list_id;

        $job_list = $company->JobListings->find($job_list_id);
        
        $job_list->update([
            'status' => job_status::Published,
        ]);

        return response([
            'message' => "Job list successfully published"
        ],200);

    }

    public function getAllApplicants($id){
        $company_id = $id;
        $job_lists_id = JobList::where('company_id', $company_id)->pluck('id');
        $job_applications = JobApplication::whereIn('job_list_id', $job_lists_id)->get();
        $applicants = [];

        foreach($job_applications as $job_application){
            $results = [
                'applicant_id' => $job_application->user->id,
                'applicant' => $job_application->user->first_name ." ".  $job_application->user->last_name,
                'position_applying' => $job_application->jobList->job_title,
                'expericence_level' => $job_application->user->experience_level,
                'date_applied' => Carbon::parse($job_application->created_at)->format('d/m/Y h:m A'),
            ];

            array_push($applicants, $results);
        }

        return response([
            'applicants' => collect($applicants)->paginate(10),
            'message' => "Success"
        ],200);
    }

    public function searchJobs(Request $request){
        $location = $request->query('location');
        $keyword = $request->query('keyword');
        $industry = $request->query('industry');

        dd($location);
    }


    //private functions
    private function uploadFile($file_attachments, $user_id){
        $path = 'files';

        if($file = $file_attachments){
            $fileName = time().$file->getClientOriginalName();
            $filePath = Storage::disk('s3')->put($path,$file);
            $filePath   = Storage::disk('s3')->url($filePath);
            $file_type  = $file->getClientOriginalExtension();
            $fileSize   = $this->fileSize($file);


            $file_attachments = FileAttachment::create([
                'user_id' => $user_id,
                'name' => $fileName,
                'type' => $file_type,
                'path' => $filePath,
                'size' => $fileSize,
            ]);

            return $file_attachments->path;
        }else{
            return $file_attachments = null;
        }
    }
}
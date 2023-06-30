<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobApplication;
use App\Models\JobBenefits;
use App\Models\JobList;
use App\Models\JobLocation;
use App\Models\JobType;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JobListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

            $job_location = JobLocation::create([
                'location' => $data['location'],
                'address' => $data['address'] ?? null,
                'description' => $data['address_description'] ?? ""
            ]);


            $job_list = JobList::create([
                'company_id' => $userCompany->id,
                'job_title' => $data['job_title'],               'description' => $data['description'] ?? "",
                'job_location_id' => $job_location->id,
                'show_pay' => $data['show_pay'] ?? null,
                'pay_type' => $data['pay_type'],
                'salary' => $data['salary'] ?? null,
                'min_salary' => $data['min_salary'] ?? null,
                'max_salary' => $data['max_salary'] ?? null,
                'experience_level_id' => $data['experience_level_id'],
                'number_of_vacancies' => $data['number_of_vacancies'],
                'hiring_urgency' => $data['hiring_urgency'],

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
            'job_list' => $jobList,
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


    public function showJobsByCompany(Request $request){
        $c_jobs_list = JobList::where('company_id', $request['company_id'])->get();

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
        ],200);
    }

    public function getAllApplicants($job_list_id){
        $job_applications = JobApplication::where('job_list_id', $job_list_id)->get();

        foreach($job_applications as $job_application){
            dd($job_application->user);
        }
    }

    public function getJobListings($company_id){
        $job_lists = JobList::where('company_id', $company_id)->get();
        $results = [];

        foreach($job_lists as $job_list){
            $result = [
                'job_list_title' => $job_list->job_title,
                'hiring' => $job_list->number_of_vacancies,
                'applied' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Unread")->count(),
                'interview' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Interview")->count(),
                'accepted' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Accepted")->count(),
                'status' => $job_list->status,
                'date_created' => Carbon::parse($job_list->created_at)->format('d/m/Y'),
            ]; 

            array_push($results, $result);
        }
        //dd($results);

        return response([
            'job_lists' => collect($results)->paginate(10),
        ]);
    }
}

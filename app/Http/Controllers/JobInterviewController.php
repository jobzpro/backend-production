<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobInterview;
use App\Models\JobList;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\MailerController as MailerController;
use App\Models\Company;
use App\Enums\JobApplicationStatus as application_status;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

class JobInterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(request()->user()->id);

        $company_id = $user->userCompanies->first()->companies->first()->id;

        if ($user->userRoles->first()->role->role_name == "Jobseeker") {
            $jobInterviews =  JobInterview::where('applicant_id', $user->id)->with('jobList')->get();


            return response([
                'job_interviews' => $jobInterviews,
                'message' => "Success",
            ]);
        } else {
            $jobInterviews = JobInterview::where('company_id', $company_id)->with('applicant')->get();
            return response([
                'job_interviews' => $jobInterviews,
                'message' => "Success",
            ], 200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $employer = User::find(request()->user()->id);
        $userCompanies = $employer->userCompanies;
        $company = null;
        $jobApplication = JobApplication::find($data['job_application_id']);

        if ($userCompanies && $userCompanies->count() > 0) {
            $company = $userCompanies->first()->companies;
    
            if ($company && $company->count() > 0) {
                $company = $company->first();
            }
        }
    
        if (!$company) {
            return response([
                'message' =>  "No company found for user",
            ], 404);
        }
        $jobInterview = JobInterview::create([
            'employer_id' => $employer->id,
            'applicant_id' => $jobApplication->user_id,
            'job_application_id' => $jobApplication->id,
            'job_list' => $jobApplication->job_list_id,
            'company_id' => $company->id,
            'notes' => $data['notes'],
            'meeting_link' => $data['meeting_link'],
            'interview_date' => Carbon::parse($data['interview_date'])->format("d/m/Y h:m"),
        ]);

        $jobApplication->update([
            'status' => application_status::interview,
        ]);

        $notification = Notification::create([
            'notifiable_id' => $jobApplication->user->id,
            'notifiable_type' => get_class($jobApplication->user),
            'notifier_id' => $jobApplication->id,
            'notifier_type' => get_class($jobApplication),
            'notif_type' => 'interview_scheduled',
            'content' => $company->name . 'has scheduled your interview.',
            'title' => 'Interview Scheduled',
        ]);


        (new MailerController)->sendInterviewInvite($company, $jobInterview);

        return response([
            'message' => "Success",
        ], 200);
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        //
        $jobInterview = JobInterview::find($id);

        if ($jobInterview) {

            return response([
                'interview' => $jobInterview,
                'message' => 'Successful'
            ]);
        } else {
            return response([
                'message' => "Interview not found",
            ], 400);
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(JobInterview $jobInterview)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, JobInterview $jobInterview)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(JobInterview $jobInterview)
    {
        //
    }


    public function cancelInterview(Request $request, JobInterview $jobInterview)
    {
    }

    public function setStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Something went wrong",
                'errors' => $validator->errors()
            ], 400);
        }

        $jobInterview = JobInterview::find($id);

        if ($jobInterview) {
            $jobInterview->update(['status' => $request['status']]);

            return response([
                'interview' => $jobInterview,
                'message' => 'Successful'
            ]);
        } else {
            return response([
                'message' => "Interview not found",
            ], 400);
        }
    }

    public function reschedule(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'interview_date' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Something went wrong",
                'errors' => $validator->errors()
            ], 400);
        }

        $jobInterview = JobInterview::find($id);

        if ($jobInterview) {
            $jobInterview->update([
                'interview_date' => $request['interview_date'],
                'meeting_link' => $request['meeting_link'],
                'notes' => $request['notes'],
            ]);

            return response([
                'interview' => $jobInterview,
                'message' => 'Successful'
            ]);
        } else {
            return response([
                'message' => "Interview not found",
            ], 400);
        }
    }

    public function search(Request $request)
    {
        $keyword = $request->query('keyword');
        $sortFilter = $request->query('sort');

        $jobInterviews = JobInterview::with('jobList');

        if (!$keyword == null) {
            $jobInterviews = $jobInterviews->whereHas('jobList', function ($q) use ($keyword) {
                $q->where('job_title', 'LIKE', '%' . $keyword . '%');
            })
                ->orWhereHas('applicant', function ($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
                });
        }

        if (!$sortFilter == null) {
            if ($sortFilter == "Recent to Oldest") {
                $jobInterviews = $jobInterviews->latest()->get();
            } else if ($sortFilter == "Alphabetical") {
                $jobInterviews = $jobInterviews->with(['applicant' => function ($q) {
                    $q->orderBy('first_name');
                }])->get();
            }
        } else {
            $jobInterviews = $jobInterviews->with('applicant')->get();
        }

        return response([
            'interviews' => $jobInterviews->paginate(10),
            'message' => "Success",
        ], 200);
    }
}

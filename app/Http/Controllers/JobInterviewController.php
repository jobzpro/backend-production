<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobInterview;
use App\Models\JobList;
use App\Models\User;
use App\Models\UserRole;
use App\Models\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\MailerController as MailerController;
use App\Models\Company;
use App\Enums\JobApplicationStatus as application_status;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobInterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $user = User::find($request->query('id'));
        $orderBy = $request->query('orderBy');
        $keyword = $request->query('keyword');
        // dd($user);
        // dd($user->userRoles->first()->role->role_name);
        if ($user->userRoles->first()->role->role_name == "Jobseeker") {
            $jobInterviews =  JobInterview::where('applicant_id', $user->id)
                ->with('jobList')->get();
            return response([
                'job_interviews' => $jobInterviews,
                'message' => "Success",
            ]);
        } else {
            $company_id = $user->userCompanies->first()->companies->first()->id;
            // $jobInterviews = JobInterview::where('company_id', $company_id)
            //     ->where('employer_id', '!=' , $user->employer_id)
            //     ->where('status', 'for_interview')
            //     ->with('applicant', 'jobList', 'userRole', 'user')
            //     ->orderBy('interview_date', $orderBy);
            // ->get();

            $jobInterviews = JobInterview::with('applicant', 'jobList', 'userRole', 'user')
                ->where('employer_id', '!=', $user->employer_id)
                ->where('status', 'for_interview')
                ->whereHas('jobList', function ($query) use ($company_id) {
                    $query->where('company_id', $company_id);
                })
                ->orderBy('interview_date', $orderBy);

            if (!$keyword == null) {
                $jobInterviews = $jobInterviews
                    ->whereHas('applicant', function ($q) use ($keyword) {
                        $q->where('first_name', 'LIKE', '%' . $keyword . '%');
                    })
                    ->orWhereHas('jobList', function ($q) use ($keyword) {
                        $q->where('job_title', 'LIKE', '%' . $keyword . '%');
                    });
            }
            return response([
                'job_interviews' => $jobInterviews->paginate(10),
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
    public function store(Request $request, int $job_application_id)
    {
        $data = $request->all();
        $employer = User::find(request()->user()->id);
        $userRole = UserRole::with('role')->where('user_id', "=", $employer->id)->first();
        $userCompanies = $employer->userCompanies;
        $company = null;
        $jobApplication = JobApplication::find($job_application_id);

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

        // echo(Carbon::parse($data['interview_date'])->format("Y-m-d H:i:s"));

        $jobInterview = JobInterview::create([
            // 'employer_id' => $userRole->role_id,
            'employer_id' => $employer->id,
            'applicant_id' => $jobApplication->user_id,
            'job_application_id' => $jobApplication->id,
            'job_list_id' => $jobApplication->job_list_id,
            'company_id' => $company->id,
            'notes' => $data['notes'],
            'meeting_link' => $data['meeting_link'],
            'interview_date' => Carbon::parse($data['interview_date'])->format("Y-m-d H:i:s"),
        ]);

        $jobApplication->update([
            // 'status' => application_status::interview,
            'status' => 'Scheduled'
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

    public function setStatus(Request $request, int $interview_id, int $job_application_id)
    {
        // $status = $request->query('status');

        $job_application = JobApplication::find($job_application_id);
        // dd($job_application);
        $job_application->update(['status' => $request->query('status')]);
        // dd($status);
        // $validator = Validator::make($request->all(), [
        //     'status' => 'required',
        // ]);

        // if ($validator->fails()) {
        //     return response([
        //         'message' => "Something went wrong",
        //         'errors' => $validator->errors()
        //     ], 400);
        // }

        $jobInterview = JobInterview::find($interview_id);

        if ($jobInterview) {
            $jobInterview->update(['status' => $request->query('status')]);

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

    public function reschedule(Request $request, int $interview_id)
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

        $jobInterview = JobInterview::find($interview_id);

        if ($jobInterview) {
            $jobInterview->update([
                'interview_date' => Carbon::parse($request['interview_date'])->format("Y-m-d H:i:s"),
                'meeting_link' => $request['meeting_link'],
                // 'notes' => $request['notes'],
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

    public function search(Request $request,  $emp_id)
    {
        $keyword = $request->query('keyword');
        $sortFilter = $request->query('sort');
        // $emp_id = $request->query('employer_id');

        if ($emp_id) {
            $jobInterviews = JobInterview::with('jobList')->where('employer_id', $emp_id);

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
        } else {
            return response([
                'message' => "employer id is missing",
            ], 500);
        }
    }

    public function getUserInterviews(Request $request)
    {
        $user = Auth::user();
        $user_id = $user->user->id;
        // $status = $request->query('status');
        $orderBy = $request->query('orderBy');
        $interviewApplications = User::find($user_id)
            ->join('job_applications', 'users.id', '=', 'job_applications.user_id')
            ->join('job_interviews', 'job_applications.id', '=', 'job_interviews.job_application_id')
            ->join('job_lists', 'job_lists.id', '=', 'job_applications.job_list_id')
            ->join('companies', 'companies.id', '=', 'job_lists.company_id')
            ->join('industries', 'industries.id', '=', 'job_lists.industry_id')
            ->select(
                'job_applications.id',
                'job_applications.status',
                'companies.company_logo_path',
                'companies.name as company_name',
                'companies.address_line',
                'job_lists.job_title',
                'industries.name as industry_name',
                'job_interviews.interview_date',
                'job_interviews.meeting_link'
            )
            ->where('users.id', '=', $user_id)
            // ->where('job_applications.status', '=', $status)
            ->orderBy('interview_date', $orderBy)
            ->get();

        return response([
            'interviews' => $interviewApplications->paginate(10),
        ]);
    }
}

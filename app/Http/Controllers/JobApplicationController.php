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
use App\Models\Notification;
use Illuminate\Support\Facades\Auth;

class JobApplicationController extends Controller
{
    use FileManager;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->query('keyword');
        $sortFilter = $request->query('sort');

        $applications = JobApplication::with('jobList', 'jobInterviews');

        if (!$keyword == null) {
            $applications = $applications->whereHas('jobList', function ($q) use ($keyword) {
                $q->where('job_title', 'LIKE', '%' . $keyword . '%');
            })
                ->orWhereHas('user', function ($q) use ($keyword) {
                    $q->where('first_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
                });
        }

        if (!$sortFilter == null) {
            if ($sortFilter == "Recent to Oldest") {
                $applications = $applications->latest()->get();
            } else if ($sortFilter == "Alphabetical") {
                $applications = $applications->with(['user' => function ($q) {
                    $q->orderBy('first_name');
                }])->get();
            }
        } else {
            $applications = $applications->with('user')->get();
        }

        return response([
            'applications' => $applications->paginate(10),
            'message' => "Success",
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {
        $job_list = JobList::find($id);
        dd($job_list);
        $account = Auth::user();
        $user = User::find($account->user->id);
        $company = Company::find($job_list->company_id);
        $user_companies = UserCompany::where('company_id', $company->id)->with('user.account')->get();


        if ($request->has('file')) {
            $filesValidator = Validator::make($request->all(), [
                'files.*' => 'mimes:pdf,doc,docx,txt|max:2048',
            ]);

            if ($filesValidator->fails()) {
                return response([
                    'message' => "Invalid file.",
                    'errors' => $filesValidator->errors(),
                ], 400);
            } else {
                $path = 'files';
                $file = $request->file('file');
                $fileName = time() . $file->getClientOriginalName();
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
        } else {
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
            'description' => "Your application to " . $job_list->company->name . " has been successfully submitted. A company representative will reach out you if you got shortlisted.",
            'is_Read' => false,
        ]);

        CompanyNotification::create([
            'company_id' => $company->id,
            'job_list_id' => $job_list->id,
            'title' => "A jobseeker has applied for " . $job_list->job_title,
            'description' => "You can review and see their profile to check if the applicant is qualified.",
            'is_Read' => false,
        ]);

        if ($user_companies) {
            foreach ($user_companies as $employer) {
                (new EmployerMailerController)->applicantApplied($user, $employer, $company, $job_list);
            }
        }

        if ($user) {
            (new MailerController)->sendApplicationSuccess($user, $company, $job_list);
        }

        return response([
            'message' => 'Application Successfully Submitted',
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $job_application_id)
    {
        $application = JobApplication::with('jobList', 'user', 'jobInterviews')->find($job_application_id);

        if ($application) {
            return response([
                'application' => $application,
                'message' => "Success",
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
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

    public function retractApplication(Request $request, $id)
    {
        $account = Auth::user();
        $user = User::find($account->user->id);

        if ($user->userRole->first()->role->role_name == "Jobseeker") {
            $job_application = JobApplication::find($id);

            $validator = Validator::make($request->all(), [
                'reason' => 'required',
            ]);

            if ($validator->fails()) {
                return response([
                    'errors' => $validator->errors(),
                ], 400);
            }

            if ($user->id == $job_application->user_id) {
                $job_application->update([
                    'status' => application_status::user_retracted,
                    'reason' => $request['reason'],
                ]);

                return response([
                    'message' => 'Application successfully retracted',
                ], 200);
            } else {

                return response([
                    'message' => 'Unauthorized',
                ], 400);
            }
        } else {

            return response([
                'messsage' => 'Unauthorized',
            ]);
        }
    }

    public function searchApplicantion(Request $request, $id)
    {
    }

    public function setStatus(Request $request, string $id)
    {
        $job_application = JobApplication::find($id);

        if ($job_application) {
            $job_application->update(['status' => $request['status']]);
            $company_name = $job_application->jobList->company->name;

            if ($request['status'] == 'reviewed') {
                $notification = Notification::create([
                    'notifiable_id' => $job_application->user->id,
                    'notifiable_type' => get_class($job_application->user),
                    'notifier_id' => $job_application->id,
                    'notifier_type' => get_class($job_application),
                    'notif_type' => 'application_reviewed',
                    'content' => $company_name . ' has reviewed your application.',
                    'title' => 'Application Reviewed',
                ]);
            } else if ($request['status'] == 'rejected') {
                $notification = Notification::create([
                    'notifiable_id' => $job_application->user->id,
                    'notifiable_type' => get_class($job_application->user),
                    'notifier_id' => $job_application->id,
                    'notifier_type' => get_class($job_application),
                    'notif_type' => 'application_rejected',
                    'content' => $company_name . ' has rejected your application.',
                    'title' => 'Application Rejected',
                ]);
            } else if ($request['status'] == 'approved') {
                $notification = Notification::create([
                    'notifiable_id' => $job_application->user->id,
                    'notifiable_type' => get_class($job_application->user),
                    'notifier_id' => $job_application->id,
                    'notifier_type' => get_class($job_application),
                    'notif_type' => 'application_approved',
                    'content' => $company_name . ' has approved your application.',
                    'title' => 'Application Approved',
                ]);
            }

            return response([
                'application' => $job_application,
                'message' => "Success",
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }

    public function jobApplicationHistory() {
        $account = Auth::user();
        $user_id = $account->user->id;

        $jobseeker_applications = JobApplication::where('user_id', $user_id)->with('jobList')->get();

        if($jobseeker_applications)
        {
            foreach ($jobseeker_applications as $application) {
                // dd($application);
                $application->company = Company::find($application->jobList?->company_id);
            }

            return response([
                'application_history' => $jobseeker_applications,
            ], 200);
        }else {
            return response([
                'message' => 'Not found',
            ], 400);
        }

    }

    public function delete()
    {
    }
}

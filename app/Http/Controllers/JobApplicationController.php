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
use App\Models\UserSubscription;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class JobApplicationController extends Controller
{
    use FileManager;
    /**
     * Display a listing of the resource.jobApp
     */
    public function index(Request $request, int $id)
    {
        $keyword = $request->query('keyword');
        $sortFilter = $request->query('sort');
        $company_id = $id;
        $order = $request->query('orderBy');
        // $applications = JobApplication::with('jobList', 'jobInterviews', 'user');
        $user = User::where('account_id', Auth::id())->first();
        $applications = JobApplication::with('jobList', 'jobInterviews', 'jobList.user', 'jobList.experience_level', 'user.user_experience', 'user.account', 'user.references', 'user.certifications')
            ->whereHas('jobList', function ($q) use ($company_id, $keyword) {
                $q->where('company_id', $company_id);
            })
            ->orderBy('created_at', $order);

        if (!$keyword == null) {
            $applications = $applications
                ->whereHas('jobList', function ($query) use ($company_id, $keyword) {
                    $query->where('company_id', $company_id)
                        ->where('job_title', 'LIKE', '%' . $keyword . '%');
                });
            // ->orWhereHas('user', function ($query) use ($keyword){
            //     $query->where('first_name', 'LIKE', '%' . $keyword . '%')
            //     ->orWhere('middle_name', 'LIKE', '%', '%' . $keyword . '%')
            //     ->orWhere('last_name', 'LIKE', '%', '%' . $keyword . '%');
            // });
        }

        // if (!$sortFilter == null) {
        //     if ($sortFilter == "Recent to Oldest") {
        //         $applications = $applications->latest()->get()->paginate(10);
        //     } else if ($sortFilter == "Alphabetical") {
        //         $applications = $applications->with(['user' => function ($q) {
        //             $q->orderBy('first_name');
        //         }])->get()->paginate(10);
        //     }
        // } else {
        //     $applications = $applications->get()->paginate(10);
        // }
        $applications = $applications->get()->paginate(10);

        return response([
            'applications' => $applications,
            'message' => "Success",
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $id)
    {

        $job_list = JobList::find($id);
        // dd($job_list);
        $account = Auth::user();
        $user = User::find($account->user->id);
        $company = Company::find($job_list->company_id);
        $user_companies = UserCompany::where('company_id', $company->id)->with('user.account')->get();
        // $userSubscription = $user->user_subscription()->latest()->first();
        $userSubscription = UserSubscription::displayConnectionCountTotalFirst($user->id);
        if ($user && ($userSubscription->connection_count == (int)$request->input('connection_token'))) {
            return response([
                'message' => 'Oops, looks like you ran out of tokens.',
            ], 400);
        } else {
            if (($userSubscription->connection_count < (int)$request->input('connection_token'))) {
                return response([
                    'message' => 'Oops, looks like you ran out of tokens.',
                ], 400);
            } elseif ($userSubscription->connection_count >= (int)$request->input('connection_token')) {
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

                // UserNotification::create([
                //     'job_application_id' => $job_application->id,
                //     'user_id' => $user->id,
                //     'title' => "Job Application Successfully submitted.",
                //     'description' => "Your application to " . $job_list->company->name . " has been successfully submitted. A company representative will reach out you if you got shortlisted.",
                //     'is_Read' => false,
                // ]);

                $notification = Notification::create([
                    'notifiable_id' => $job_application->user->id,
                    'notifiable_type' => get_class($job_application->user),
                    'notifier_id' => $job_application->id,
                    'notifier_type' => get_class($job_application),
                    'notif_type' => 'interview_scheduled',
                    'photo' => $job_list->company->company_logo_path,
                    'content' => "Your application to " . $job_list->company->name . " has been successfully submitted. A company representative will reach out you if you got shortlisted.",
                    'title' => 'Job Application Successfully submitted.',
                ]);
                // CompanyNotification::create([
                //     'company_id' => $company->id,
                //     'job_list_id' => $job_list->id,
                //     'title' => "A jobseeker has applied for " . $job_list->job_title,
                //     'description' => "You can review and see their profile to check if the applicant is qualified.",
                //     'is_Read' => false,
                // ]);

                $notification = Notification::create([
                    'notifiable_id' => $company->id,
                    'notifiable_type' => get_class($company),
                    'notifier_id' => $company->id,
                    'notifier_type' => get_class($company),
                    'notif_type' => 'interview_scheduled',
                    'photo' => $job_list->company->company_logo_path,
                    'content' => "You can review and see their profile to check if the applicant is qualified.",
                    'title' => "A jobseeker has applied for " . $job_list->job_title,
                ]);
                if ($user_companies) {
                    foreach ($user_companies as $employer) {
                        (new EmployerMailerController)->applicantApplied($user, $employer, $company, $job_list);
                    }
                }

                if ($user) {
                    (new MailerController)->sendApplicationSuccess($user, $company, $job_list);
                }
                $now = Carbon::now();
                $expiryDate = Carbon::parse($userSubscription->expiry_at);
                // if ($now > $expiryDate) {
                //     $user->user_subscription()->create([
                //         'connection_count' => 19,
                //         'post_count' => 0,
                //         'applicant_count' => 0,
                //         'expiry_at' => Carbon::now()->addMonths(1),
                //     ]);
                //     return response([
                //         'message' => 'Application Successfully Submitted',
                //     ], 200);
                // } else 

                $userSubscription->update([
                    'connection_count' => $userSubscription->connection_count - (int)$request->input('connection_token')
                ]);
                return response([
                    'message' => 'Application Successfully Submitted',
                ], 200);
            }
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, string $job_application_id)
    {
        $application = JobApplication::with('jobList', 'user', 'user.account', 'jobInterviews', 'user.references', 'user.certifications')->find($job_application_id);

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

    public function searchApplicantion(Request $request)
    {
        echo ('Test');
    }

    public function setStatus(Request $request, string $id)
    {
        // echo('Test');
        $job_application = JobApplication::with('jobList')->find($request->job_application_id);
        if ($job_application) {
            $job_application->update(['status' => $request->query('status')]);
            if ($job_application->jobList == null) {
                $company_name = 'No company name';
            } else {
                $company_name = $job_application->jobList->company->name;
            }

            if ($request->query('status') == 'Reviewed') {
                $notification = Notification::create([
                    'notifiable_id' => $job_application->user->id,
                    'notifiable_type' => get_class($job_application->user),
                    'notifier_id' => $job_application->id,
                    'notifier_type' => get_class($job_application),
                    'notif_type' => 'application_reviewed',
                    'content' => $company_name . ' has reviewed your application.',
                    'title' => 'Application Reviewed',
                ]);
            } else if ($request->query('status') == 'Rejected') {
                $notification = Notification::create([
                    'notifiable_id' => $job_application->user->id,
                    'notifiable_type' => get_class($job_application->user),
                    'notifier_id' => $job_application->id,
                    'notifier_type' => get_class($job_application),
                    'notif_type' => 'application_rejected',
                    'content' => $company_name . ' has rejected your application.',
                    'title' => 'Application Rejected',
                ]);
            } else if ($request->query('status') == 'Approved') {
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

    public function jobApplicationHistory(Request $request)
    {
        $account = Auth::user();
        $user_id = $account->user->id;
        $status = $request->query('status');
        $orderBy = $request->query('orderBy');
        $jobseeker_applications = JobApplication::where('user_id', $user_id)->with('jobList.company', 'jobList.industry', 'jobInterviews', 'user.certifications')
            // ->orderby('interview_date', $orderBy)
            ->get();

        if (!$status == null) {
            $jobseeker_applications = $jobseeker_applications->where('status', $status);
        }
        if ($jobseeker_applications) {
            return response([
                'applicationHistory' => $jobseeker_applications->paginate(10),
                // 'user_id' => $user_id
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
        return response([
            'message' => 'test',
            // 'user_id' => $user_id
        ], 200);
    }

    public function delete()
    {
    }
}

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
use App\Models\JobIndustryPhysicalSetting;
use App\Models\JobIndustrySpeciality;
use App\Models\JobStandardShift;
use App\Models\JobSupplementalSchedule;
use App\Models\JobWeeklySchedule;
use App\Http\Controllers\UploadController as Uploader;
use Illuminate\Support\Facades\Auth;

class JobListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use FileManager;

    public function index()
    {
        $jobLists = JobList::with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'qualifications', 'job_specialities.industrySpeciality')->get();

        return response([
            'job_list' => $jobLists->paginate(10),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->all();
        $account = Auth::user();
        $user = User::find($account->user->id);
        $userCompany = $user->userCompanies()->first()->companies()->first();

        $fileValidator = Validator::make($request->all(), [
            'files' => 'file|mimes:pdf,doc,docx,txt|max:4000',
        ]);

        if ($fileValidator->fails()) {
            return response([
                "message" => "Invalid File",
                "errors" => $fileValidator->errors(),
            ]);
        } else {

            $file_attachments = (new Uploader)->uploadFile($request->file('files'), $user->id);
        }

        if ($request->route('id') == $userCompany->id) {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',
            ]);

            if ($validator->fails()) {
                return response([
                    'message' => "Job posting unsuccessful.",
                    'errors' => $validator->errors(),
                ], 400);
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
                'time_limit' => Carbon::parse($data['time_limit']) ?? null,
                'other_email' => $data['other_email'] ?? null,
                'industry_id' => $data['industry_id'] ?? null,
                'files' => $file_attachments,
                'authorized_to_work_in_us' => $data['authorized_to_work_in_us'],
                'is_vaccinated' => $data['is_vaccinated'],
                'can_commute' => $data['can_commute'],
                'qualification_id' => $data['qualification_id'],
            ]);


            $job_location = JobLocation::create([
                'job_list_id' => $job_list->id,
                'location' => $data['location'],
                'address' => $data['address'] ?? null,
                'description' => $data['address_description'] ?? ""
            ]);

            if ($request->filled('job_types')) {
                $job_types = explode(",", $data['job_types']);
                for ($i = 0; $i < sizeof($job_types); $i++) {
                    JobType::create([
                        'job_list_id' => $job_list->id,
                        'type_id' => $job_types[$i],
                    ]);
                }
            }

            if ($request->filled('industry_physical_setting')) {
                $job_industry_physical_settings = explode(",", $data['industry_physical_setting']);
                for ($i = 0; $i < sizeof($job_industry_physical_settings); $i++) {
                    JobIndustryPhysicalSetting::create([
                        'job_list_id' => $job_list->id,
                        'industry_physical_setting_id' => $job_industry_physical_settings[$i],
                    ]);
                }
            }

            if ($request->filled('industry_speciality')) {
                $job_industry_specialities = explode(",", $data['industry_speciality']);
                for ($i = 0; $i < sizeof($job_industry_specialities); $i++) {
                    JobIndustrySpeciality::create([
                        'job_list_id' => $job_list->id,
                        'industry_speciality_id' => $job_industry_specialities[$i],
                    ]);
                }
            }

            if ($request->filled('benefits')) {
                $job_benefits = explode(',', $data['benefits']);
                for ($i = 0; $i < sizeof($job_benefits); $i++) {
                    JobBenefits::create([
                        'job_list_id' => $job_list->id,
                        'benefit_id' => $job_benefits[$i],
                    ]);
                }
            }

            if ($request->filled('standard_shift')) {
                $job_standard_shift = explode(',', $data['standard_shift']);
                for ($i = 0; $i < sizeof($job_standard_shift); $i++) {
                    JobStandardShift::create([
                        'job_list_id' => $job_list->id,
                        'standard_shift_id' => $job_standard_shift[$i],
                    ]);
                }
            }

            if ($request->filled('weekly_schedule')) {
                $job_weekly_schedule = explode(',', $data['weekly_schedule']);
                for ($i = 0; $i < sizeof($job_weekly_schedule); $i++) {
                    JobWeeklySchedule::create([
                        'job_list_id' => $job_list->id,
                        'weekly_schedule_id' => $job_weekly_schedule[$i],
                    ]);
                }
            }

            if ($request->filled('supplementary_schedule')) {
                $job_supplementary_schedule = explode(",", $data['supplementary_schedule']);
                for ($i = 0; $i < sizeof($job_supplementary_schedule); $i++) {
                    JobSupplementalSchedule::create([
                        'job_list_id' => $job_list->id,
                        'supplemental_schedules_id' => $job_supplementary_schedule[$i],
                    ]);
                }
            }

            $notification = CompanyNotification::create([
                'title' => "Job Successfully Posted!",
                'description' => "Job list " . $job_list->job_title . " has been successfully posted.",
                'company_id' => $userCompany->id,
                'job_list_id' => $job_list->id,
            ]);


            return response([
                'job_list' => $job_list,
                'message' => "Job posted successfully."
            ], 200);
        } else {
            return response([
                'message' => "Unauthorized."
            ], 401);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request)
    {
        $jobList = JobList::where('id', $request->input('id'))
            ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'qualifications', 'job_specialities.industrySpeciality')->get();

        return response([
            'job_list' => $jobList,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $jobList = JobList::find($id);
        $data = $request->all();
        $account = Auth::user();
        $user = User::find($account->user->id);
        $userCompany = $user->userCompanies->first()->companies()->first();

        if ($userCompany->id == $jobList->company_id) {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',
            ]);

            if ($validator->fails()) {
                return response([
                    'message' => "Updating the Job was unsuccessful.",
                    'errors' => $validator->errors(),
                ], 400);
            }

            $jobList->update([
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
                'authorized_to_work_in_us' => $data['authorized_to_work_in_us'],
                'is_vaccinated' => $data['is_vaccinated'],
                'can_commute' => $data['can_commute'],
                'qualification_id' => $data['qualification_id'],
            ]);

            return response([
                'message' => "Success",
            ], 200);
        } else {
            return response([
                'message' => "Unauthorized",
            ], 401);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, $job_list_id)
    {
        $jobList = JobList::find($job_list_id);

        if ($jobList) {
            $jobList->delete();

            return response([
                'message' => "Success",
            ], 200);
        } else {
            return response([
                'message' => "Not found",
            ], 401);
        }
    }

    public function saveJobListAsDraft(Request $request)
    {
        $data = $request->all();
        $account = Auth::user();
        $user = User::find($account->user->id);
        $userCompany = $user->userCompanies()->first()->companies()->first();

        $fileValidator = Validator::make($request->all(), [
            'files' => 'file|mimes:pdf,doc,docx,txt|max:4000',
        ]);

        if ($fileValidator->fails()) {
            return response([
                "message" => "Invalid File",
                "errors" => $fileValidator->errors(),
            ]);
        } else {
            $file_attachments = (new Uploader)->uploadFile($request['files'], $user->id);
        }


        if ($request->route('id') == $userCompany->id) {
            $validator = Validator::make($request->all(), [
                'job_title' => 'required',
            ]);

            if ($validator->fails()) {
                return response([
                    'message' => "Job posting unsuccessful.",
                    'errors' => $validator->errors(),
                ], 400);
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
                'status' => job_status::Draft,
                'can_applicant_with_criminal_record_apply' => $data['can_applicant_with_criminal_record_apply'] ?? null,
                'can_start_messages' => $data['can_start_messages'] ?? null,
                'send_auto_reject_emails' => $data['send_auto_reject_emails'] ?? null,
                'auto_reject' => $data['auto_reject'] ?? null,
                'time_limit' => Carbon::parse($data['time_limit']) ?? null,
                'other_email' => $data['other_email'] ?? null,
                'industry_id' => $data['industry_id'] ?? null,
                'files' => $file_attachments,
                'authorized_to_work_in_us' => $data['authorized_to_work_in_us'],
                'is_vaccinated' => $data['is_vaccinated'],
                'can_commute' => $data['can_commute'],
            ]);

            $job_location = JobLocation::create([
                'job_list_id' => $job_list->id,
                'location' => $data['location'],
                'address' => $data['address'] ?? null,
                'description' => $data['address_description'] ?? ""
            ]);

            if ($request->filled('job_types')) {
                $job_types = explode(",", $data['job_types']);
                for ($i = 0; $i < sizeof($job_types); $i++) {
                    JobType::create([
                        'job_list_id' => $job_list->id,
                        'type_id' => $job_types[$i],
                    ]);
                }
            }

            if ($request->filled('industry_physical_setting')) {
                $job_industry_physical_settings = explode(",", $data['industry_physical_setting']);
                for ($i = 0; $i < sizeof($job_industry_physical_settings); $i++) {
                    JobIndustryPhysicalSetting::create([
                        'job_list_id' => $job_list->id,
                        'industry_physical_setting_id' => $job_industry_physical_settings[$i],
                    ]);
                }
            }

            if ($request->filled('industry_speciality')) {
                $job_industry_specialities = explode(",", $data['industry_speciality']);
                for ($i = 0; $i < sizeof($job_industry_specialities); $i++) {
                    JobIndustrySpeciality::create([
                        'job_list_id' => $job_list->id,
                        'industry_speciality_id' => $job_industry_specialities[$i],
                    ]);
                }
            }

            if ($request->filled('benefits')) {
                $job_benefits = explode(",", $data['benefits']);
                for ($i = 0; $i < sizeof($job_benefits); $i++) {
                    JobBenefits::create([
                        'job_list_id' => $job_list->id,
                        'benefit_id' => $job_benefits[$i],
                    ]);
                }
            }

            if ($request->filled('standard_shift')) {
                $job_standard_shift = explode(",", $data['standard_shift']);
                for ($i = 0; $i < sizeof($job_standard_shift); $i++) {
                    JobStandardShift::create([
                        'job_list_id' => $job_list->id,
                        'standard_shift_id' => $job_standard_shift[$i],
                    ]);
                }
            }

            if ($request->filled('weekly_schedule')) {
                $job_weekly_schedule = explode(",", $data['weekly_schedule']);
                for ($i = 0; $i < sizeof($job_weekly_schedule); $i++) {
                    JobWeeklySchedule::create([
                        'job_list_id' => $job_list->id,
                        'weekly_schedule_id' => $job_weekly_schedule[$i],
                    ]);
                }
            }

            if ($request->filled('supplementary_schedule')) {
                $job_supplementary_schedule = explode(",", $data['supplementary_schedule']);
                for ($i = 0; $i < sizeof($job_supplementary_schedule); $i++) {
                    JobSupplementalSchedule::create([
                        'job_list_id' => $job_list->id,
                        'supplemental_schedules_id' => $job_supplementary_schedule[$i],
                    ]);
                }
            }

            return response([
                'job_list' => $job_list,
                'message' => "Job list is saved as draft successfully."
            ], 200);
        } else {
            return response([
                'message' => "Unauthorized."
            ], 401);
        }
    }


    public function showJobsByCompany(Request $request)
    {
        $c_jobs_list = JobList::with('job_types')
            ->where('company_id', $request['company_id'])
            ->withCount('jobApplications')
            ->withCount('jobInterviews')
            ->get();

        return response([
            'job_list' => $c_jobs_list->paginate(10),
        ], 200);
    }

    public function createJobType(string $id, Request $request)
    {
        $data = $request->all();


        $job_type = JobType::create([
            'type_id' => $data['type_id'],
            'job_id' => $id,
        ]);


        return response([
            'job_type' => $job_type,
            'message' => "success",
        ], 200);
    }

    public function createJobBenefits(string $id, Request $request)
    {
        $data = $request->all();

        $job_benefits = JobBenefits::create([
            'job_id' => $id,
            'name' => $data['name'],
            'description' => $data['description'] ?? null
        ]);

        return response([
            'job_benefits' => $job_benefits,
            'message' => "Success"
        ], 200);
    }

    public function getAllApplicantsForJobList(string $id)
    {
        $company = Company::find($id);
        $job_list_id = request()->job_list_id;
        $job_applications = JobApplication::where('job_list_id', $job_list_id)->get();
        $applicants = [];
        foreach ($job_applications as $job_application) {
            array_push($applicants, $job_application->user);
        }

        return response([
            'applicants' => collect($applicants)->paginate(10),
        ]);
    }

    public function getJobListings(string $id)
    {
        $company_id = $id;
        $job_lists = JobList::with('job_types')->where('company_id', $company_id)->get();
        $results = [];
        $types = [];

        foreach ($job_lists as $job_list) {
            $result = [
                'job_list_id' => $job_list->id,
                'job_list_title' => $job_list->job_title,
                'hiring' => $job_list->number_of_vacancies,
                'applied' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Unread")->count(),
                'interview' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Interview")->count(),
                'accepted' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Accepted")->count(),
                'status' => $job_list->status,
                'date_created' => Carbon::parse($job_list->created_at)->format('d/m/Y'),
                'job_types' => $types,

            ];
            foreach ($job_list->job_types as $jtype) {
                $type = $jtype->type;
                array_push($types, $type);
            }

            array_push($results, $result);
        }

        return response([
            'job_lists' => collect($results)->paginate(10),
        ]);
    }

    public function archiveJobList(string $id)
    {
        $company = Company::find($id);
        $job_list_id = request()->job_list_id;
        $job_list = $company->JobListings->find($job_list_id);

        $job_list->update([
            'status' => job_status::Archived,
        ]);

        return response([
            'message' => "Job list successfully archived"
        ], 200);
    }

    public function publishJobList(string $id)
    {
        $company = Company::find($id);
        $job_list_id = request()->job_list_id;

        $job_list = $company->JobListings->find($job_list_id);

        $job_list->update([
            'status' => job_status::Published,
        ]);

        return response([
            'message' => "Job list successfully published"
        ], 200);
    }

    public function getAllApplicants(string $id)
    {
        $company_id = $id;
        $job_lists_id = JobList::where('company_id', $company_id)->pluck('id');
        $job_applications = JobApplication::whereIn('job_list_id', $job_lists_id)->get();
        $applicants = [];

        foreach ($job_applications as $job_application) {
            $results = [
                'applicant_id' => $job_application->user->id,
                'applicant' => $job_application->user->first_name . " " .  $job_application->user->last_name,
                'position_applying' => $job_application->jobList->job_title,
                'expericence_level' => $job_application->user->experience_level,
                'date_applied' => Carbon::parse($job_application->created_at)->format('d/m/Y h:m A'),
            ];

            array_push($applicants, $results);
        }

        return response([
            'applicants' => collect($applicants)->paginate(10),
            'message' => "Success"
        ], 200);
    }

    public function searchJobs(Request $request)
    {
        $location = $request->query('location');
        $keyword = $request->query('keyword');
        $industry = $request->query('industry');

        if (!$keyword == null) {
            $job_lists = JobList::where('job_title', 'LIKE', '%' . $keyword . '%')
                ->orWhereHas('company', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%');
                })
                ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality')
                ->get();

            return response([
                'job_lists' => $job_lists->paginate(10),
                'message' => "Success",
            ], 200);
        } elseif (!$location == null) {
            $job_lists = JobList::whereHas('job_location', function ($q) use ($location) {
                $q->where('address', 'LIKE', '%' . $location . '%')
                    ->orWhere('location', 'LIKE', '%' . $location . '%');
            })
                ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality')
                ->get();

            return response([
                'job_lists' => $job_lists->paginate(10),
                'message' => "Success",
            ], 200);
        } elseif (!$industry == null) {
            $job_lists = JobList::whereHas('industry', function ($q) use ($industry) {
                $q->where('name', 'LIKE', '%' . $industry . '%');
            })
                ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality')
                ->get();

            return response([
                'job_lists' => $job_lists->paginate(10),
                'message' => "Success",
            ], 200);
        } elseif (!($keyword == null && $location == null && $industry == null)) {
            $job_lists = JobList::orWhereHas('name', 'LIKE', '%' . $keyword . '%')
                ->orWhereHas('job_location', function ($q) use ($location) {
                    $q->where('name', 'LIKE', '%' . $location . '%');
                })
                ->orWhereHas('industry', function ($q) use ($industry) {
                    $q->where('name', 'LIKE', '%' . $industry . '%');
                })
                ->orWhereHas('company', function ($q) use ($keyword) {
                    $q->where('name', 'LIKE', '%' . $keyword . '%');
                })
                ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality')
                ->get();

            return response([
                'job_lists' => $job_lists->paginate(10),
                'message' => "Success",
            ], 200);
        } else {
            $job_lists = JobList::with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality')->get();

            return response([
                'job_lists' => $job_lists->paginate(10),
                'message' => "Success",
            ], 200);
        }
    }
}

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
use App\Models\Industry;
use App\Http\Controllers\UploadController as Uploader;
use App\Models\JobListDealbreaker;
use Illuminate\Support\Facades\Auth;

class JobListController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    use FileManager;

    public function index()
    {
        $jobLists = JobList::with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'qualifications', 'job_specialities.industrySpeciality', 'jobListDealbreakers', 'jobStandardShifts', 'jobWeeklySchedules.weeklySchedule', 'jobSupplementalSchedules', 'experience_level')->get();

        return response([
            'job_list' => $jobLists->paginate(5),
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
                'authorized_to_work_in_us' => $data['authorized_to_work_in_us'] ?? null,
                'is_vaccinated' => $data['is_vaccinated'] ?? null,
                'can_commute' => $data['can_commute'] ?? null,
                'qualification_id' => $data['qualification_id'] ?? null,
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

            // if ($request->filled('dealbreakers')) {
            //     // $dealbreakers = explode(',', $data['dealbreakers']);
            //     foreach ($request['dealbreakers'] as $dealbreaker) {
            //         // for ($i = 0; $i < sizeof($$dealbreakers); $i++) {
            //         JobListDealbreaker::create([
            //             'job_list_id' => $job_list->id,
            //             'dealbreaker_id' => $dealbreaker['id'],
            //             'required' => $dealbreaker['required'],
            //         ]);
            //     }
            // }

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
    public function show(string $id)
    {
        $job_list_id = $id;
        // dd($job_list_id);
        // dd(JobList::all());
        $jobList = JobList::where('id', $job_list_id)
            ->with('company', 'industry', 'jobStandardShifts', 'jobWeeklySchedules.weeklySchedule', 'jobSupplementalSchedules', 'job_location', 'job_types.type', 'job_benefits.benefits', 'qualifications', 'job_specialities.industrySpeciality', 'job_specialities', 'job_physical_settings', 'jobListDealbreakers.dealbreaker.choices', 'experience_level')
            ->first();
        return response([
            'job_list' => $jobList,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id,  string $job_list_id)
    {
        $jobList = JobList::find($job_list_id);
        $account = Auth::user();
        $data = $request->all();
        $user = User::find($account->user->id);
        $userCompany = $user->userCompanies->first()->companies()->first();
        if ($userCompany->id == $jobList->company_id) {
            // $validator = Validator::make($request->all(), [
            //     'job_title' => 'required',
            // ]);

            // if ($validator->fails()) {
            //     return response([
            //         'message' => "Updating the Job was unsuccessful.",
            //         'errors' => $validator->errors(),
            //     ], 400);
            // }

            $jobList->update([
                'company_id' => $userCompany->id,
                'job_title' => $request->input('job_title') ?? $jobList->job_title,
                'description' => $request->input('description') ?? $jobList->description,
                'show_pay' => $request->input('show_pay') ?? $jobList->show_pay,
                'pay_type' => $request->input('pay_type') ?? $jobList->pay_type,
                'salary' => $request->input('salary') ?? $jobList->salary,
                'require_resume' => $request->input('require_resume') ?? $jobList->require_resume,
                'min_salary' => $request->input('min_salary') ?? $jobList->min_salary,
                'max_salary' => $request->input('max_salary') ?? $jobList->max_salary,
                'experience_level_id' => $request->input('experience_level_id') ?? $jobList->experience_level_id,
                'number_of_vacancies' => $request->input('number_of_vacancies') ?? $jobList->number_of_vacancies,
                'hiring_urgency' => $request->input('hiring_urgency') ?? $jobList->hiring_urgency,
                'job_excempt_from_local_laws' => $request->input('job_excempt_from_local_laws') ?? $jobList->job_excempt_from_local_laws,
                'status' => job_status::Published,
                'can_applicant_with_criminal_record_apply' => $request->input('can_applicant_with_criminal_record_apply') ?? $jobList->can_applicant_with_criminal_record_apply,
                'can_start_messages' => $request->input('can_start_messages') ?? $jobList->can_start_messages,
                'send_auto_reject_emails' => $request->input('send_auto_reject_emails') ?? $jobList->send_auto_reject_emails,
                'auto_reject' => $request->input('auto_reject') ?? $jobList->auto_reject,
                'time_limit' => $request->input('time_limit') ?? $jobList->time_limit,
                'other_email' => $request->input('other_email') ?? '',
                'industry_id' => $request->input('industry_id') ?? $jobList->industry_id,
                'authorized_to_work_in_us' => $request->input('authorized_to_work_in_us') ?? $jobList->authorized_to_work_in_us,
                'is_vaccinated' => $request->input('is_vaccinated') ?? $jobList->is_vaccinated,
                'can_commute' => $request->input('can_commute') ?? $jobList->can_commute,
                'qualification_id' => $request->input('qualification_id') ?? $jobList->qualification_id,
            ]);

            $job_location = JobLocation::where('job_list_id', $job_list_id)->first();
            $job_location->update([
                'location' => $data['location'] ?? "",
                'address' => $data['address'] ?? null,
                'description' => $data['address_description'] ?? ""
            ]);

            if ($request->filled('job_types')) {
                $job_types_request = explode(",", $request->input('job_types'));
                $existingJobTypes = JobType::where('job_list_id', $job_list_id)->pluck('type_id')->toArray();
                foreach ($job_types_request as $type_id) {
                    if (in_array($type_id, $existingJobTypes)) {
                        JobType::where('job_list_id', $job_list_id)
                            ->where('type_id', $type_id)
                            ->update([
                                'job_list_id' => $job_list_id,
                                'type_id' => $type_id,
                            ]);
                    } else {
                        if (in_array($type_id, ['1', '2', '3', '4', '5'])) {
                            JobType::create([
                                'job_list_id' => $job_list_id,
                                'type_id' => $type_id,
                            ]);
                        }
                    }
                }
                JobType::Where('job_list_id', $job_list_id)
                    ->where(function ($query) use ($job_types_request) {
                        $query->whereNotIn('type_id', ['1', '2', '3', '4', '5']);
                        $query->orwhereNotIn('type_id', $job_types_request);
                    })
                    ->delete();
            }
            if ($request->filled('industry_physical_setting')) {
                $industry_physical_settings_request = explode(",", $request->input('industry_physical_setting'));
                $medical_physical_settings = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
                $existingSettings = JobIndustryPhysicalSetting::where('job_list_id', $job_list_id)
                    ->pluck('industry_physical_setting_id')
                    ->toArray();
                foreach ($industry_physical_settings_request as $setting_id) {
                    if (in_array($setting_id, $existingSettings)) {
                        JobIndustryPhysicalSetting::where('job_list_id', $job_list_id)
                            ->where('industry_physical_setting_id', $setting_id)
                            ->update([
                                'job_list_id' => $job_list_id,
                                'industry_physical_setting_id' => $setting_id,
                            ]);
                    } else {
                        if (in_array($setting_id, $medical_physical_settings)) {
                            JobIndustryPhysicalSetting::create([
                                'job_list_id' => $job_list_id,
                                'industry_physical_setting_id' => $setting_id,
                            ]);
                        }
                    }
                }
                JobIndustryPhysicalSetting::Where('job_list_id', $job_list_id)
                    ->where(function ($query) use ($industry_physical_settings_request) {
                        $medical_physical_settings = ['1', '2', '3', '4', '5', '6', '7', '8', '9'];
                        $query->whereNotIn('industry_physical_setting_id', $medical_physical_settings);
                        $query->orwhereNotIn('industry_physical_setting_id', $industry_physical_settings_request);
                    })
                    ->delete();
            }
            if ($request->filled('industry_speciality')) {
                $industry_specialities_request = explode(",", $request->input('industry_speciality'));
                $availableSpecialities = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36'];
                $existingSpecialities = JobIndustrySpeciality::where('job_list_id', $job_list_id)
                    ->pluck('industry_speciality_id')
                    ->toArray();
                foreach ($industry_specialities_request as $speciality_id) {
                    if (in_array($speciality_id, $existingSpecialities)) {
                        JobIndustrySpeciality::where('job_list_id', $job_list_id)
                            ->where('industry_speciality_id', $speciality_id)
                            ->update([
                                'job_list_id' => $job_list_id,
                                'industry_speciality_id' => $speciality_id
                            ]);
                    } else {
                        if (in_array($speciality_id, $availableSpecialities)) {
                            JobIndustrySpeciality::create([
                                'job_list_id' => $job_list_id,
                                'industry_speciality_id' => $speciality_id,
                            ]);
                        }
                    }
                }
                JobIndustrySpeciality::Where('job_list_id', $job_list_id)
                    ->where(function ($query) use ($industry_specialities_request) {
                        $query->whereNotIn('industry_speciality_id', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23', '24', '25', '26', '27', '28', '29', '30', '31', '32', '33', '34', '35', '36']);
                        $query->orwhereNotIn('industry_speciality_id', $industry_specialities_request);
                    })
                    ->delete();
            }

            if ($request->filled('benefits')) {
                $job_benefits_request = explode(',', $request->input('benefits'));
                $existingBenefits = JobBenefits::where('job_list_id', $job_list_id)
                    ->pluck('benefit_id')
                    ->toArray();
                $availableBenefits = ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23'];
                foreach ($job_benefits_request as $benefit_id) {
                    if (in_array($benefit_id, $existingBenefits)) {
                        JobBenefits::where('job_list_id', $job_list_id)
                            ->where('benefit_id', $benefit_id)
                            ->update([
                                'job_list_id' => $job_list_id,
                                'benefit_id' => $benefit_id,
                            ]);
                    } else {
                        if (in_array($benefit_id, $availableBenefits)) {
                            JobBenefits::create([
                                'job_list_id' => $job_list_id,
                                'benefit_id' => $benefit_id,
                            ]);
                        }
                    }
                }
                JobBenefits::Where('job_list_id', $job_list_id)
                    ->where(function ($query) use ($job_benefits_request) {
                        $query->whereNotIn('benefit_id', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12', '13', '14', '15', '16', '17', '18', '19', '20', '21', '22', '23']);
                        $query->orwhereNotIn('benefit_id', $job_benefits_request);
                    })
                    ->delete();
            }

            if ($request->filled('standard_shift')) {
                $job_standard_shift_request = explode(',', $request->input('standard_shift'));
                $existingShifts = JobStandardShift::where('job_list_id', $job_list_id)
                    ->pluck('standard_shift_id')
                    ->toArray();
                foreach ($job_standard_shift_request as $shift_id) {
                    if (in_array($shift_id, $existingShifts)) {
                        JobStandardShift::where('job_list_id', $job_list_id)
                            ->where('standard_shift_id', $shift_id)
                            ->update([
                                'job_list_id' => $job_list_id,
                                'standard_shift_id' => $shift_id,
                            ]);
                    } else {
                        JobStandardShift::create([
                            'job_list_id' => $job_list_id,
                            'standard_shift_id' => $shift_id,
                        ]);
                    }
                }
                JobStandardShift::where('job_list_id', $job_list_id)
                    ->whereNotIn('standard_shift_id', $job_standard_shift_request)
                    ->delete();
            }

            if ($request->filled('weekly_schedule')) {
                $job_weekly_schedule_request = explode(',', $request->input('weekly_schedule'));
                $existingSchedules = JobWeeklySchedule::where('job_list_id', $job_list_id)
                    ->pluck('weekly_schedule_id')
                    ->toArray();
                foreach ($job_weekly_schedule_request as $schedule_id) {
                    if (in_array($schedule_id, $existingSchedules)) {
                        JobWeeklySchedule::where('job_list_id', $job_list_id)
                            ->where('weekly_schedule_id', $schedule_id)
                            ->update([
                                'job_list_id' => $job_list_id,
                                'weekly_schedule_id' => $schedule_id,
                            ]);
                    } else {
                        if (in_array($schedule_id, ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'])) {
                            JobWeeklySchedule::create([
                                'job_list_id' => $job_list_id,
                                'weekly_schedule_id' => $schedule_id,
                            ]);
                        }
                    }
                }
                JobWeeklySchedule::Where('job_list_id', $job_list_id)
                    ->where(function ($query) use ($job_weekly_schedule_request) {
                        $query->whereNotIn('weekly_schedule_id',  ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12']);
                        $query->orwhereNotIn('weekly_schedule_id', $job_weekly_schedule_request);
                    })
                    ->delete();
            }

            if ($request->filled('supplementary_schedule')) {
                $job_supplementary_schedule_request = explode(",", $request->input('supplementary_schedule'));
                $existingSchedules = JobSupplementalSchedule::where('job_list_id', $job_list_id)
                    ->pluck('supplemental_schedules_id')
                    ->toArray();
                foreach ($job_supplementary_schedule_request as $schedule_id) {
                    if (in_array($schedule_id, $existingSchedules)) {
                        JobSupplementalSchedule::where('job_list_id', $job_list_id)
                            ->where('supplemental_schedules_id', $schedule_id)
                            ->update([
                                'job_list_id' => $job_list_id,
                                'supplemental_schedules_id' => $schedule_id,
                            ]);
                    } else {
                        if (in_array($schedule_id, ['1', '2', '3', '4', '5'])) {
                            JobSupplementalSchedule::create([
                                'job_list_id' => $job_list_id,
                                'supplemental_schedules_id' => $schedule_id,
                            ]);
                        }
                    }
                }
                JobSupplementalSchedule::Where('job_list_id', $job_list_id)
                    ->where(function ($query) use ($job_supplementary_schedule_request) {
                        $query->whereNotIn('supplemental_schedules_id',  ['1', '2', '3', '4', '5']);
                        $query->orwhereNotIn('supplemental_schedules_id', $job_supplementary_schedule_request);
                    })
                    ->delete();
            }

            // if ($request->filled('dealbreakers')) {
            //     foreach ($request->input('dealbreakers') as $dealbreaker) {
            //         $dealbreakerId = $dealbreaker['id'];
            //         $dealbreakerData = JobListDealbreaker::Where('dealbreaker_id', $dealbreakerId)->Where('job_list_id', $job_list_id)->first();
            //         if ($dealbreakerData) {
            //             $dealbreakerData->update(['dealbreaker_id' => $dealbreakerId, 'required' => $dealbreaker['required']]);
            //         } else {
            //             JobListDealbreaker::create([
            //                 'job_list_id' => $job_list_id,
            //                 'dealbreaker_id' => $dealbreakerId,
            //                 'required' => $dealbreaker['required']
            //             ]);
            //         }
            //     }
            // }

            $joblist = Joblist::where('id', $job_list_id)->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'qualifications', 'job_specialities', 'job_physical_settings', 'jobListDealbreakers.dealbreaker.choices')->first();
            return response([
                'message' => "Success",
                'data' => $joblist
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
        if (!$jobList) {
            return response([
                'message' => "Not found",
            ], 400);
        } else if ($jobList) {
            $jobList->delete();
            // $jobList->status = "deleted";
            // $jobList->save();
            return response([
                'message' => "Success",
            ], 200);
        } else {
            return response([
                'message' => "Please try again...",
            ], 500);
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

    public function getJobListings(Request $request, string $id)
    {
        $company = Auth::user();
        $company_id = $id;
        $keyword = $request->query('keyword');
        $status = $request->query('status');
        $company = Company::find($company_id);
        $company_industry = Industry::find($company->industry_id);
        $job_lists = JobList::with('job_types', 'jobListDealbreakers')
            ->where('company_id', $company_id)
            ->where('job_title', 'LIKE', '%' . $keyword . '%')
            ->where('status', 'LIKE', '%' . $status . '%')
            ->where('status', '!=', 'deleted')
            ->get();
        $results = [];
        $types = [];
        $dealbreakers = [];


        foreach ($job_lists as $job_list) {
            $result = [
                'company_industry' => $company_industry->name,
                'job_list_id' => $job_list->id,
                'job_list_title' => $job_list->job_title,
                'hiring' => $job_list->number_of_vacancies,
                'applied' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Unread")->count(),
                'interview' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Interview")->count(),
                'accepted' => JobApplication::where('job_list_id', $job_list->id)->where('status', "Accepted")->count(),
                'status' => $job_list->status,
                'date_created' => $job_list->created_at,
                'job_types' => $types,
                'dealbreakers' => $job_list->jobListDealbreakers,
                // 'dealbreakers' => $job_list->jobListDealbreakers->with('dealbreakers')->get(),
            ];
            foreach ($job_list->job_types as $jtype) {
                $type = $jtype->type;
                array_push($types, $type);
            }
            // foreach ($job_list->jobListDealbreakers as $jdealbreaker) {
            //     $dealbreaker = $jdealbreaker->dealbreaker;
            //     array_push($dealbreakers, $dealbreaker);
            // }

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
        $job_type = $request->query('job_type');
        $qualifications = $request->query('qualifications');
        $date = $request->query('date');

        // ->startOfDay()
        // ->endOfDay()
        $date_now = Carbon::today();
        $date_selected = Carbon::parse($date);

        // $job_lists = JobList::whereBetween('created_at', [$date_now . ' 00:00:00',  $date_selected . ' 23:59:59'])->get();
        $job_lists = JobList::whereDate('created_at', '>=', $date_now . ' 00:00:00')
            ->whereDate('created_at', '<=',  $date_selected . ' 23:59:59')
            ->get();
        // dd($date_now);
        // dd($date_selected);
        dd($job_lists);
        // if (!$keyword == null) {
        //     $job_lists = JobList::where('job_title', 'LIKE', '%' . $keyword . '%')
        //         ->orWhereHas('company', function ($q) use ($keyword) {
        //             $q->where('name', 'LIKE', '%' . $keyword . '%');
        //         })
        //         ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality', 'jobListDealbreakers.dealbreaker.choices')
        //         ->orderBy('updated_at', 'DESC')
        //         ->get();

        //     return response([
        //         'job_lists' => $job_lists->paginate(10),
        //         'message' => "Success",
        //     ], 200);
        // } elseif (!$location == null) {
        //     $job_lists = JobList::whereHas('job_location', function ($q) use ($location) {
        //         $q->where('address', 'LIKE', '%' . $location . '%')
        //             ->orWhere('location', 'LIKE', '%' . $location . '%');
        //     })
        //         ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality', 'jobListDealbreakers.dealbreaker.choices')
        //         ->orderBy('updated_at', 'DESC')
        //         ->get();

        //     return response([
        //         'job_lists' => $job_lists->paginate(10),
        //         'message' => "Success",
        //     ], 200);
        // } elseif (!$industry == null) {
        //     $job_lists = JobList::whereHas('industry', function ($q) use ($industry) {
        //         $q->where('name', 'LIKE', '%' . $industry . '%');
        //     })
        //         ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality', 'jobListDealbreakers.dealbreaker.choices')
        //         ->orderBy('updated_at', 'DESC')
        //         ->get();

        //     return response([
        //         'job_lists' => $job_lists->paginate(10),
        //         'message' => "Success",
        //     ], 200);
        // } elseif (!$job_type == null) {
        //     $job_lists = JobList::whereHas('job_types.type', function ($q) use ($job_type) {
        //         $q->where('name', 'LIKE', '%' . $job_type . '%');
        //     })
        //         ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality', 'jobListDealbreakers.dealbreaker.choices')
        //         ->orderBy('updated_at', 'DESC')
        //         ->get();

        //     return response([
        //         'job_lists' => $job_lists->paginate(10),
        //         'message' => "Success",
        //     ], 200);
        // } elseif (!$qualifications == null) {
        //     $job_lists = JobList::where('qualification_id', 'LIKE', '%' . $qualifications . '%')
        //         ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality', 'jobListDealbreakers.dealbreaker.choices')
        //         ->orderBy('updated_at', 'DESC')
        //         ->get();

        //     return response([
        //         'job_lists' => $job_lists->paginate(10),
        //         'message' => "Success",
        //     ], 200);
        // } elseif (!$date == null) {
        //     $job_lists = JobList::whereBetween('created_at', [$date_now,  $date_selected])
        //         ->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality', 'jobListDealbreakers.dealbreaker.choices')
        //         ->orderBy('updated_at', 'DESC')
        //         ->get();
        //     return response([
        //         'job_lists' => $job_lists->paginate(10),
        //         'message' => "Success",
        //     ], 200);
        // } elseif (!($keyword == null && $location == null && $industry == null && $job_type == null && $qualifications == null && $date == null)) {
        //     // $date_selected = Carbon::parse($date);

        //     $job_lists = JobList::orWhereHas('name', 'LIKE', '%' . $keyword . '%')
        //         ->orWhereHas('job_location', function ($q) use ($location) {
        //             $q->where('name', 'LIKE', '%' . $location . '%');
        //         })
        //         ->orWhereHas('industry', function ($q) use ($industry) {
        //             $q->where('name', 'LIKE', '%' . $industry . '%');
        //         })
        //         ->orWhereHas('company', function ($q) use ($keyword) {
        //             $q->where('name', 'LIKE', '%' . $keyword . '%');
        //         })
        //         ->orWhereHas('job_types.type', function ($q) use ($job_type) {
        //             $q->where('name', 'LIKE', '%' . $job_type . '%');
        //         })
        //         ->orWhereHas('qualification_id', 'LIKE', '%' . $qualifications . '%');

        //     if ($date_now && $date_selected) {
        //         $job_lists->whereBetween('created_at', [$date_now, $date_selected]);
        //     }

        //     $res = $job_lists->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality', 'jobListDealbreakers.dealbreaker.choices')
        //         ->orderBy('updated_at', 'DESC')
        //         ->get();

        //     return response([
        //         'job_lists' => $res->paginate(10),
        //         'message' => "Success",
        //     ], 200);
        // } else {
        //     $job_lists = JobList::with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'job_specialities.industrySpeciality', 'jobListDealbreakers.dealbreaker.choices')
        //         ->orderBy('updated_at', 'DESC')
        //         ->get();

        //     return response([
        //         'job_lists' => $job_lists->paginate(10),
        //         'message' => "Success",
        //     ], 200);
        // }
    }

    public function edit(string $id,  string $job_list_id)
    {
        $jobList = JobList::find($job_list_id);
        $jobList = JobList::where('id', $job_list_id)
            ->with('company', 'industry', 'jobStandardShifts', 'jobWeeklySchedules.weeklySchedule', 'jobSupplementalSchedules', 'job_location', 'job_types.type', 'job_benefits.benefits', 'qualifications', 'job_specialities.industrySpeciality', 'job_specialities', 'job_physical_settings', 'jobListDealbreakers.dealbreaker.choices')->first();
        return response([
            'job_list' => $jobList,
        ], 200);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\JobList;
use App\Models\Report;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ReportController extends Controller
{
    public function userReports(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            return response([
                'reports' => $user->reportedEntities(),
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "User not found.",
            ], 400);
        }
    }

    public function companyReports(Request $request, $id)
    {
        $company = Company::find($id);

        if ($company) {
            return response([
                'reports' => $company->reportedEntities(),
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "Company not found",
            ], 400);
        }
    }

    //create a report for a Company by a Jobseeker/User
    public function reportCompanyOrJobList(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'company_id' => 'required_without:job_listing_id',
            'job_listing_id' => 'required_without:company_id',
            'reason' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Parameters not found.",
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::find($id);

        if ($user) {
            if ($request->filled('company_id')) {
                $company = Company::find($request['company_id']);

                if ($company) {
                    $company->reports()->create([
                        'reportable_id' => $company->id,
                        'reportable_type' => get_class($company),
                        'reporter_id' => $user->id,
                        'reason' => $request['reason'],
                        'type' => $request['type'],
                    ]);

                    return response([
                        'reports' => $user->reportedEntities(),
                        'message' => "Successfully reported Company",
                    ], 200);
                } else {
                    return response([
                        'message' => "Company Not found.",
                    ], 400);
                }
            } else if ($request->filled('job_listing_id')) {
                $jobListing = JobList::find($request['job_listing_id']);

                if ($jobListing) {
                    $report = new Report([
                        'reporter_id' => $user->id,
                        'reason' => $request['reason'],
                        'type' => $request['type'],
                    ]);
                    $jobListing->reports()->save($report);

                    return response([
                        'reports' => $user->reportedEntities(),
                        'message' => "Successfully reported Job Listing",
                    ], 200);
                } else {
                    return response([
                        'message' => "Job Listing Not found.",
                    ], 400);
                }
            }
        } else {
            return response([
                'message' => "User Not found.",
            ], 400);
        }
    }

    //create a report for a Jobseeker/User by a Company
    public function reportJobSeeker(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'reason' => 'required',
            'type' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Parameters not found.",
                'errors' => $validator->errors()
            ], 400);
        }

        $company = Company::find($id);

        if ($company) {
            $user = User::find($request['user_id']);

            if ($user) {
                $report = new Report([
                    'reporter_id' => $company->id,
                    'reason' => $request['reason'],
                    'type' => $request['type'],
                ]);
                $user->reports()->save($report);

                return response([
                    'reports' => $company->reportedEntities(),
                    'message' => "Successfully reported Job Listing",
                ], 200);
            } else {
                return response([
                    'message' => "User Not found.",
                ], 400);
            }
        } else {
            return response([
                'message' => "Company Not found.",
            ], 400);
        }
    }

    public function delete($id)
    {
        $report = Report::find($id);

        if ($report) {
            $report->delete();

            return response([
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "Report not found",
            ], 400);
        }
    }

    public function show($id)
    {
        $report = Report::with('reportable', 'reporter')->find($id);

        if ($report) {

            return response([
                'report' => $report,
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "Report not found",
            ], 400);
        }
    }

    public function setStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Parameters not found.",
                'errors' => $validator->errors()
            ], 400);
        }

        $report = Report::with('reportable', 'reporter')->find($id);

        if ($report) {

            $report->update([
                'status' => $request['status'],
            ]);

            return response([
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "Report not found",
            ], 400);
        }
    }
}

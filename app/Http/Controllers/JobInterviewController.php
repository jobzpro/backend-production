<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobInterview;
use App\Models\JobList;
use App\Models\User;
use Illuminate\Http\Request;

class JobInterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = User::find(request()->user()->id);
        $company_id = $user->userCompanies->first()->companies->first()->id;
        $jobLists_id = JobList::where('company_id', $company_id)->pluck('id');
        $jobInterviews = JobInterview::where('company_id', $company_id)->get();

        dd($jobInterviews);
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
        $employer_id = request()->user()->id;
        //dd($employer_id);

        $jobInterview = JobInterview::create([
            'employer_id' => $employer_id,
            'applicant_id' => $data['applicant_id'],
            'job_application_id' => $data['job_application_id'],
            'company_id' => $user->userCompanies->first()->companies->first()->id,
            'notes' => $data['notes'],
            'meeting_link' => $data['meeting_link'],
        ]);

        return response([
            'message' => "Success",
        ],200);

    }

    /**
     * Display the specified resource.
     */
    public function show(JobInterview $jobInterview)
    {
        //
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
}

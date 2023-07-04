<?php

namespace App\Http\Controllers;

use App\Models\JobInterview;
use Illuminate\Http\Request;

class JobInterviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
        $user_id = request()->user()->id;
        dd();

        $jobInterview = JobInterview::create([
            'user_id' => $user_id,
            'job_application_id' => $data['job_application_id'],
            'notes' => $data['notes'],
            'meeting_link' => $data['meeting_link'],
        ]);



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

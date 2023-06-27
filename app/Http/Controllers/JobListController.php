<?php

namespace App\Http\Controllers;

use App\Models\JobList;
use Illuminate\Http\Request;

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
        
        
    }

    /**
     * Display the specified resource.
     */
    public function show(JobList $jobList)
    {
        return response([
            'job_list' => $jobList->paginate(10),
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
}

<?php

namespace App\Http\Controllers;

use App\Models\JobListDealbreaker;
use Illuminate\Http\Request;

class JobListDealbreakerController extends Controller
{
    public function softDeleteDealbreakerAnswer(Request $request)
    {
        if ($request->input('job_list_id')) {
            $jLDealbreaker = JobListDealbreaker::where('job_list_id', '=', $request->input('job_list_id'));
            $count = $jLDealbreaker->count();
            if ($count > 0) {
                $jLDealbreaker->forceDelete();
                return response()->json([
                    'message' => 'success',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No records found for soft deletion',
                ], 400);
            }
        } else {
            return response([
                'message' => "job list id is missing, try again"
            ], 500);
        }
    }

    public function selectForceDeleteAnswer(Request $request)
    {
        $jLDealbreaker = JobListDealbreaker::where('job_list_id', '=', $request->input('job_list_id'))
            ->where('dealbreaker_id', '=', $request->input('dealbreaker_id'));
        $count = $jLDealbreaker->count();
        if ($request->input('job_list_id') && $request->input('dealbreaker_id')) {
            if ($count > 0) {
                $jLDealbreaker->forceDelete();
                return response()->json([
                    'message' => 'success',
                ], 200);
            } else {
                return response()->json([
                    'message' => 'No records found for soft deletion',
                ], 400);
            }
        } else {
            return response([
                'message' => "job list id is missing, try again"
            ], 500);
        }
    }
}

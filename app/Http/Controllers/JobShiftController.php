<?php

namespace App\Http\Controllers;

use App\Models\StandardShift;
use App\Models\SupplementalSchedule;
use App\Models\WeeklySchedule;
use Illuminate\Http\Request;

class JobShiftController extends Controller
{
    public function index(){

    }

    public function getStandardShifts(){
        $job_standard_shifts = StandardShift::all();

        return response([
            'standard_shifts' => $job_standard_shifts,
            'message' => "Success",
        ],200);
    }

    public function getWeeklyShifts(){
        $job_weekly_shifts = WeeklySchedule::all();

        return response([
            'weekly_schedules' => $job_weekly_shifts,
            'message' => "Success",
        ]);
    }

    public function getSupplementalShifts(){
        $job_supplemental_shifts = SupplementalSchedule::all();

        return response([
            'supplemental_shifts' => $job_supplemental_shifts,
            'message' => "Success",
        ]);
    }
}

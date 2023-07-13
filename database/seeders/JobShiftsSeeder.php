<?php

namespace Database\Seeders;

use App\Models\StandardShift;
use App\Models\SupplementalSchedule;
use App\Models\WeeklySchedule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JobShiftsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $standard_shifts = ["Dayshift", "Night Shift", "Evening Shift", "Overnight Shift", "Other", "None"];
        $weekly_schedules = ["Weekend Availability", "Monday to Friday", "Rotating Weekends", "Every Weekend", "No Weekends","Choose your own hours", "3x12", "5x8", "4x10", "4x12", "Other", "None"];
        $supplemental_schedules = ["Overtime", "Holidays", "On Call", "Extended Hours", "Other"];

        for($i = 0; $i < sizeof($standard_shifts); $i++){
            StandardShift::create([
                "name" => $standard_shifts[$i],
            ]);
        }

        for($j = 0; $j < sizeof($weekly_schedules); $j++){
            WeeklySchedule::create([
                "name" => $weekly_schedules[$j],
            ]);
        }

        for($k = 0; $k < sizeof($supplemental_schedules); $k++){
            SupplementalSchedule::create([
                "name" => $supplemental_schedules[$k],
            ]);
        }
    }
}

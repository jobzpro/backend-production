<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\IndustryPhysicalSetting;

class IndustryPhysicalSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medical_physical_settings = ["Long Term Care", "Nursing Home", "Clinic", "Rehabilitation Center", "Corrections", "Outpatient", "School", "Acute Care", "Other"];

        for($i = 0; $i < sizeof($medical_physical_settings); $i++){
            IndustryPhysicalSetting::create([
                'industry_id' => 14, 
                'physical_setting' => $medical_physical_settings[$i],
            ]);
        }
    }
}

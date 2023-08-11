<?php

namespace Database\Seeders;

use App\Models\IndustrySpeciality;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class IndustrySpecialitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $medical_specialities = ["Home Health", "Geriatics", "Pediatrics", "Medical Surgical", "Wound Care", "Primary Care", "Hospice & Pallative Medicine", "Psychiatry", "Surgery", "Cardiology", "Critical & Intensive Care", "Urgent Care", "Addiction Medicine", "Ob/Gyn", "Dialysis", "Neurolgy", "Pain Medicine", "Hollistic Medicine", "Internal Medicine", "Infectious Disease", "Home Care/Caregiving", "Other"
        ];

        $technical_specialities = ["Application Development", "Software Developer / Engineer (Programming)", "Website Design and or / Developer", "Database Adminstrator", "IT Support / Helpdesk", "IT Project Manager", "Product Manager", "Information Security / Cybersecurity Analyst", "Information Technology Auditor", "Data Scientist", "Network and Computer Systems Administrators", "Configuration / QA Testing", "User Experience Testing", "Scrum Master"
        ];

        for($i = 0; $i < sizeof($technical_specialities); $i++){
            IndustrySpeciality::create([
                'industry_id' => 1,
                'specialities' => $technical_specialities[$i],
            ]);
        }

        for($j = 0; $j < sizeof($medical_specialities); $j++){
            IndustrySpeciality::create([
                'industry_id' => 14,
                'specialities' => $medical_specialities[$j],
            ]);
        }

    }
}

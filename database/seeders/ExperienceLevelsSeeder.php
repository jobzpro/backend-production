<?php

namespace Database\Seeders;

use App\Models\ExperienceLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExperienceLevelsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $experience_levels = ["Internship", "Entry Level", "Associate", "Mid-Senior Level", "Director", "Executive"];

        for($i = 0; $i<sizeof($experience_levels); $i++){
            ExperienceLevel::create([
                'name' => $experience_levels[$i],
            ]);
        }
    }
}

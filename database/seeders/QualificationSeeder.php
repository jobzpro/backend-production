<?php

namespace Database\Seeders;

use App\Models\Qualification;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class QualificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $qualifications = ["Highschool Graduate", "SHS Graduate", "College Level", "Bachelors Degree"];

        for($i = 0; $i < sizeof($qualifications); $i++){
            Qualification::create([
                'name' => $qualifications[$i],
            ]);
        }
    }
}

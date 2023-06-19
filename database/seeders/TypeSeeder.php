<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Type;
use Illuminate\Database\Seeder;

class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ["Full-time", "Part-time", "Contract", "Internship", "Temporary"];

        for($i = 0; $i < sizeof($types); $i++){
            Type::create([
                'name' => $types[$i],
            ]);
        }

    }
}

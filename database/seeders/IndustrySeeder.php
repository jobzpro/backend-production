<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Industry;

class IndustrySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $industries = ["Technology", "Renewable Energy", "E-commerce and Digital Marketing", "Financial Services", "Sustainability and Environmental Protection", "Education and Online Learning", "Supply Chain and Logistics", "Biotechnology and Pharmaceuticals", "Construction and Infrastracture", "Creative Industries", "Hospitality", "Retail", "Transportation"];

        for($i = 0; $i < sizeof($industries); $i++){
            Industry::create([
                'name' => $industries[$i],
            ]);
        }
    }
}

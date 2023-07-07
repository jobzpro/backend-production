<?php

namespace Database\Seeders;

use App\Models\BusinessType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BusinessTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $business_types = ["Sole Proprietorship", "Partnership", "Limited Liability Company (LLC)", "Corporation", "Cooperative", "Franchise", "Nonprofit Organization", "Social Enterprise", "Limited Partnership (LP)", "Professional Corporation (PC)"];

        for($i = 0; $i < sizeof($business_types); $i++){
            BusinessType::create([
                'name' => $business_types[$i],
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Benefits;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BenefitsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $benefits = ["Health Insurance", "401(k)", "Dental Insurance","Vision Insurance", "Paid Time Off", "Life Insurance", "401(k) matching", "Flexible Schedule", "Tuition Reimbursement", "Disability Insurance", "Continuing Education Credits", "Flexible Schedule", "Referral Program", "Employee Assitance", "Flexible Spending Account", "Free Parking", "Travel Reimbursement", "Malpractice Insurance", "Paid Sick Time", "Employee Discount", "Health Savings Account", "Paid Training", "Other"];


        for($i=0; $i < sizeof($benefits); $i++){
            Benefits::create([
                'name' => $benefits[$i],
            ]);
        }
    }
}

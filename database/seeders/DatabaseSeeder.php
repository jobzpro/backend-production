<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Benefits;
use App\Models\JobShift;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        if(env('APP_ENV') == 'production'){
            $this->call([
                RolesSeeder::class,
                BusinessTypesSeeder::class,
                ExperienceLevelsSeeder::class,
                TypeSeeder::class,
                IndustrySeeder::class,
                UserSeeder::class,
                BenefitsSeeder::class,
                JobShiftsSeeder::class,
                IndustrySpecialitiesSeeder::class,
                IndustryPhysicalSettingsSeeder::class,
                QualificationSeeder::class,
                
            ]);
        }else{
            $this->call([
                RolesSeeder::class,
                BusinessTypesSeeder::class,
                ExperienceLevelsSeeder::class,
                TypeSeeder::class,
                IndustrySeeder::class,
                UserSeeder::class,
                BenefitsSeeder::class,
                JobShiftsSeeder::class,
                CompanySeeder::class,
                IndustrySpecialitiesSeeder::class,
                IndustryPhysicalSettingsSeeder::class,
                QualificationSeeder::class,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = ["Super Admin", "Employer Admin", "Jobseeker", "Employer Staff"];

        for($i = 0; $i < sizeof($roles); $i++){
            Role::create([
                'role_name' => $roles[$i],
            ]);
        }
    }
}

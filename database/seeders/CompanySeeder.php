<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\Company;
use App\Models\UserCompany;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account1 = Account::create([
            'name' => 'Company 1 Admin',
            'email' => 'admin@company1.com',
            'password' => Hash::make('t3stCompany1'),
            'email_verified_at' => Carbon::now(), 
        ]);

        $account1->user()->create([
            'account_id' => $account1->id,
            'first_name' => 'Company 1',
            'last_name' => 'Admin',
        ]);

        $user_role1 = UserRole::create([
            'user_id' => $account1->user->id,
            'role_id' => 2,
        ]);

        $company1 = Company::create([
            'status' => "approved",
            'name' => "Test Company 1",
            'company_email' => "contact@company1.com",
            'business_type_id' => 2,
            'industry_id' => 4,
            'years_of_operation' => "2008 - 2023",
            'owner_full_name' => "Mark Appleseed",
            'owner_contact_no' => "+1234567890",
            'referral_code' => "JOBZPRO2023",
        ]);

        $userCompany1 = UserCompany::create([
            'user_id' => $account1->user->id,
            'company_id' => $company1->id,
        ]);

        $account2 = Account::create([
            'name' => 'Company 2 Admin',
            'email' => 'admin@company2.com',
            'password' => Hash::make('t3stCompany2'),
            'email_verified_at' => Carbon::now(), 
        ]);

        $account2->user()->create([
            'account_id' => $account2->id,
            'first_name' => 'Company 2',
            'last_name' => 'Admin'
        ]);

        $user_role2 = UserRole::create([
            'user_id' => $account2->user->id,
            'role_id' => 2,
        ]);

        $company2 = Company::create([
            'status' => "approved",
            'name' => "Test Company 2",
            'company_email' => "contact@company2.com",
            'business_type_id' => 4,
            'industry_id' => 6,
            'years_of_operation' => "2022 - 2023",
            'owner_full_name' => "Jay White",
            'owner_contact_no' => "+1234567890",
            'referral_code' => "JOBZPRO2023",
        ]);

        $userCompany2 = UserCompany::create([
            'user_id' => $account2->user->id,
            'company_id' => $company2->id,
        ]);
    }
}

<?php

namespace Database\Seeders;

use App\Models\Account;
use App\Models\User;
use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $account = Account::create([
            'name' => 'Super Admin',
            'email' => 'admin@jobzpro.com',
            'password' => Hash::make('Sup3r@adm!n'),
            'login_type' => 'nova',
            'email_verified_at' => Carbon::now(),
        ]);

        $user = User::create([
            'account_id' => $account->id,
            'first_name' => 'Super',
            'last_name' => 'Admin',
        ]);

        $user_role = UserRole::create([
            'user_id' => $user->id,
            'role_id' => 1,
        ]);
    }
}

//hdh

<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function jobSeekerNotifications($id)
    {
        $jobseeker = User::with('notifications')->find($id);

        if ($jobseeker) {
            return response([
                'notifications' => $jobseeker->notifications,
                'message' => "Success",
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }

    public function companyNotifications($id)
    {
        $company = Company::with('notifications')->find($id);

        if ($company) {
            return response([
                'notifications' => $company->notifications,
                'message' => "Success",
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }
}

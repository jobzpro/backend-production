<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function jobSeekerNotifications($id)
    {
        $jobseeker = User::with(['notifications' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($id);

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
    public function readAllJobSeekerNotifications($id)
    {
        $company = User::with('notifications')->find($id);
        if ($company) {
            $company->notifications()->update(['read' => 1]);
            $updatedCompany = User::with(['notifications' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])->find($id);
            return response([
                'notifications' => $updatedCompany->orderBy('created_at', 'desc'),
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
        $company = Company::with(['notifications' => function ($query) {
            $query->orderBy('created_at', 'desc');
        }])->find($id);

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

    public function readAllCompanyNotifications($id)
    {
        $company = Company::with('notifications')->find($id);
        if ($company) {
            $company->notifications()->update(['read' => 1]);
            $updatedCompany = Company::with(['notifications' => function ($query) {
                $query->orderBy('created_at', 'desc');
            }])->find($id);
            return response([
                'notifications' => $updatedCompany,
                'message' => "Success",
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }
}

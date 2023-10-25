<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\JobList;
use App\Models\JobApplication;
use App\Models\User;
use App\Models\Company;
use App\Models\AppReview;
use App\Models\CompanyReview;
use App\Models\Industry;

class JobStatusDataController extends Controller
{
    public function get_job_status_data(){
        $job_posting = JobList::count();
        $hired = JobApplication::where('status','hired')->count();;
        $employers = Company::count();
        $users = User::count();

        return response([
            'job_posting' => $job_posting,
            'hired'=>$hired,
            'employers' =>$employers,
            'users'=>$users,
            'message' => "Success",
        ], 200);
    }
    
    public function get_trending_categories() {
        $industry = Industry::get();
        
        $industry->each(function ($industry) {
            $countData = JobList::where('industry_id', $industry->industry_id)->count();
            $industry->count = $countData??0;
        });
        
        return response([
            'industry' =>$industry,
            'message' => "Success",
        ], 200);
    }
    
    public function get_top_rated_companies() {
        $company = Company::limit(9)->get();

        $company->each(function ($company) {
            $averageRating = CompanyReview::where('company_id', $company->id)->avg('rating');
            $company->average_rating = $averageRating??0;
        });

        return response([
            'company' => $company,
            'message' => "Success",
        ], 200);
    }

    public function get_users_review() {
       $reviews =  AppReview::where('pin',true)->limit(9)->get();
        return response([
            'reviews'=> $reviews,
            'message' => "Success",
        ], 200);
    }
}

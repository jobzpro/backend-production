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
use App\Models\IndustrySpeciality;

class JobStatusDataController extends Controller
{
    public function get_job_status_data(){
        $job_posting = JobList::count();
        $hired = JobApplication::where('status','hired')->count();
        $employers = Company::distinct('name')->get()->pluck('name')->count();
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

        // Accounting /Finance = financial services,
        // Health = Medical = Biotechnology, 
        // Health Human Resources = supply chain = human resources,
        // Design, Art & Multimedia = E-commerce and Digital marketing = creative industry,
        // Restaurant & Food Services = hospitality, 
        // Telecommunications = Technology,
        // Other = Renewable Energy, Sustainability, education, construction and infras,  transportion
        
        $industry = Industry::get();

        $industry->each(function ($item) {
            $countData = IndustrySpeciality::where('industry_id', $item->id)->count();
            $item->count = $countData;
        });
        
        $af = $industry->whereIn('name', ['Financial Services'])->count();
        $health = $industry->whereIn('name', ['Medical', 'Biotechnology'])->count();
        $hr = $industry->whereIn('name', ['Supply Chain and Logistics'])->count();
        $dam = $industry->whereIn('name', ['E-commerce and Digital Marketing','Creative Industries'])->count();
        $hrm = $industry->whereIn('name', ['Hospitality'])->count();
        $tel = $industry->whereIn('name', ['Technology'])->count();
        $others = $industry->whereIn('name', ['Renewable Energy','Sustainability and Environmental Protection','Education and Online Learning','Construction and Infrastracture','Transportation'])->count();
        return response([
            'af' =>[
                'count'=>$af,
                'name'=>['Financial Services'],
            ],
            'health'=>[
                'count'=>$health,
                'name'=>['Medical', 'Biotechnology'],
            ],
            'hr'=>[
                'count'=>$hr,
                'name'=>['Supply Chain and Logistics'],
            ],
            'dam'=>[
                'count'=>$dam,
                'name'=>['E-commerce and Digital Marketing','Creative Industries'],
            ],
            'hrm'=>[
                'count'=>$hrm,
                'name'=>['Hospitality'],
            ],
            'tel'=>[
                'count'=>$tel,
                'name'=>['Technology'],
            ],
            'others'=>[
                'count'=>$others,
                'name'=>['Renewable Energy','Sustainability and Environmental Protection','Education and Online Learning','Construction and Infrastracture','Transportation'],
            ],
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
       $reviews =  AppReview::where('pin',true)->with('user')->limit(7)->get();
        return response([
            'reviews'=> $reviews,
            'message' => "Success",
        ], 200);
    }
}

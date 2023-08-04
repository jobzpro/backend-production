<?php

namespace App\Http\Controllers;

use App\Models\Benefits;
use Illuminate\Http\Request;

class JobBenefitsController extends Controller
{
    public function index(){
        $job_benefits = Benefits::all();

        return response([
            'job_benefits' => $job_benefits,
            'message' => 'Success', 
        ],200);
    }


    public function show(Request $request){

    }
}

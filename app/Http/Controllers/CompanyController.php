<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;

class CompanyController extends Controller
{
    
    public function index(){
        $companies = Company::all();

        return response([
            'company' => $companies->paginate(10),
            'message' => 'Successful'
        ],200);
    }

    public function show(Company $company){
        return response([
            'company' => $company,
            'message' => 'Successful'
        ],200);
    }


    public function update(Company $company, Request $request){

    }

    public function destroy(Company $company){
        
    }


    public function inviteStaff(Request $request){

    }


}

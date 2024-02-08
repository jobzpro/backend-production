<?php

namespace App\Http\Controllers;

use App\Models\ExperienceLevel;
use App\Models\JobListDealbreaker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ExperienceLevelController extends Controller
{
    public function index()
    {
        $experience_level = ExperienceLevel::all();

        if ($experience_level) {
            return response([
                'experience_level' => $experience_level,
                'message' => "Successful."
            ], 200);
        } else {
            return response([
                'message' => "experience level empty."
            ], 500);
        }
    }
    public function show($id)
    {
        $company = ExperienceLevel::find($id);
        return response([
            'experience_level' => $company,
            'message' => "Successful."
        ], 200);
    }
}

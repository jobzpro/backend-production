<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;

use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showJobseekerProfile($id){
        $result = User::find($id);

        return response([
            'user' => $result,
            'message' => 'Successful'
        ],200);

    }

    public function updateJobseekerProfile(Request $request, $id){

    }
}

<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Helper\ImageManager;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    use ImageManager;

    public function showJobseekerProfile($id){
        $result = User::find($id);

        return response([
            'user' => $result,
            'message' => 'Successful'
        ],200);

    }

    public function updateJobseekerProfile(Request $request, $id){
        $this->uploadAvatar($request['image']);
    }


    private function uploadAvatar($image){
        $validator = Validator::make($image, [
            'image' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($validator->fails()){
            return false;
        }
    }
}

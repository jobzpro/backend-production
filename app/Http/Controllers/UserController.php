<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\UserRole;
use App\Models\Image;
use App\Helper\ImageManager;

use App\Models\UserReference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    use ImageManager;

    public function showJobseekerProfile($id){
        $result = User::where('id', $id)->with('references')->first();

        return response([
            'user' => $result,
            'message' => 'Successful'
        ],200);

    }

    public function updateJobseekerProfile(Request $request, $id){
        $imageValidator = Validator::make($request->all(),[
            'avatar' => 'image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if($imageValidator->fails()){
            return response([
                'message' => "Invalid file.",
                'errors' => $imageValidator->errors(),
            ],400);
        }else{
           $avatar =  $this->uploadAvatar($request['avatar']);
        }

        $validator = Validator::make($request->all(),[
            'first_name' => 'required',
            'last_name' => 'required',
        ]);

        if($validator->fails()){
            return response([
                'message' => 'Something is wrong. Please try again.',
                'errors' => $validator->errors(),
            ],400);
        }

        $user = User::where('id', $id)->with('references')->first();

        if($avatar == null){
            $fileName = $user->avatar_path;
        }else{
            $fileName = Storage::url($avatar->path);
        }

        $user->update([
            'first_name' => $request['first_name'],
            'middle_name' => $request['middle_name'],
            'last_name' => $request['last_name'],
            'phone_number' => $request['phone_number'],
            'profession' => $request['profession'],
            'address_line' => $request['address_line'],
            'city' => $request['city'],
            'province' => $request['province'],
            'avatar_path' => $fileName,
            'elementary_school' => $request['elementary_school'],
            'high_school' => $request['high_school'],
            'college' => $request['college'],
            'description' => $request['description'],
            'certifications' => $request['certifications'],
            'skills' => $request['skills'],
        ]);


        if($request->filled("references")){
            $references = json_decode($request['references']);
            //dd($references);
            for($i = 0; $i < sizeof($references); $i++){
                UserReference::create([
                    'user_id' => $user->id,
                    'name' => $references[$i]->name,
                    'phone_number' => $references[$i]->phone_number,
                ]);
            }
        }

        return response([
            'user' => $user,
            'message' => 'Profile details successfully updated'
        ],200);
        
    }


    private function uploadAvatar($image){
        $path = 'avatars/';
        
        !is_dir($path) && mkdir($path, 0777, true);

        if($file = $image){
            $fileData = $this->uploads($file, $path);
            $avatar = Image::create([
                'name' => $fileData['fileName'],
                'type' => $fileData['fileType'],
                'path' => $fileData['filePath'],
                'size' => $fileData['fileSize']
            ]);

            return $avatar;
        }else{
            return $avatar = null;
        }
    }

}

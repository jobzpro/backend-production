<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Favorite;
use App\Models\JobList;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FavoriteController extends Controller
{
    //

    public function userFavorites($id)
    {
        $user = User::find($id);

        if ($user) {
            return response([
                'favorites' => $user->favoritedJobListings(),
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "User not found.",
            ], 400);
        }
    }

    public function companyFavorites($id)
    {
        $company = Company::find($id);

        if ($company) {
            return response([
                'favorites' => $company->favoritedJobseekers(),
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "User not found.",
            ], 400);
        }
    }

    public function addUserFavorites(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'job_list_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Parameters not found.",
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::find($id);

        if ($user) {
            $jobList = JobList::find($request['job_list_id']);

            if ($jobList) {
                $user->favorites()->create([
                    'favoritable_id' => $jobList->id,
                    'favoritable_type' => get_class($jobList),
                    'favoriter_id' => $user->id,
                    'favoriter_type' => get_class($user),
                ]);
                // $user->favorites()->create([
                //     'favoritable_id' => $jobList->id,
                //     'favorite_type' => 'App\Models\JobList'
                // ]);

                return response([
                    'favorites' => $user->favoritedJobListings(),
                    'message' => "Successfully added Favorite",
                ], 200);
            } else {
                return response([
                    'message' => "Job List not found.",
                ], 400);
            }
        } else {
            return response([
                'message' => "User not found.",
            ], 400);
        }
    }

    public function addCompanyFavorites(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Parameters not found.",
                'errors' => $validator->errors()
            ], 400);
        }

        $company = Company::find($id);

        if ($company) {
            $user = User::find($request['user_id']);

            if ($user) {
                $company->favorites()->create([
                    'favoritable_id' => $user->id,
                    'favoritable_type' => get_class($user),
                    'favoriter_id' => $company->id,
                    'favoriter_type' => get_class($company),
                ]);
                // $company->favorites()->create([
                //     'favoritable_id' => $user->id,
                //     'favorite_type' => 'App\Models\User'
                // ]);

                return response([
                    'favorites' => $company->favoritedJobseekers(),
                    'message' => "Successfully added Favorite",
                ], 200);
            } else {
                return response([
                    'message' => "User not found.",
                ], 400);
            }
        } else {
            return response([
                'message' => "Company not found.",
            ], 400);
        }
    }

    public function delete($id)
    {
        $favorite = Favorite::find($id);

        if ($favorite) {
            $favorite->delete();

            return response([
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "Favorite not found",
            ], 400);
        }
    }

    public function show($id)
    {
        $favorite = Favorite::with('favoriter', 'favoritable')->find($id);

        if ($favorite) {
            return response([
                'favorite' => $favorite,
                'message' => "Successful",
            ], 200);
        } else {
            return response([
                'message' => "Favorite not found",
            ], 400);
        }
    }
}

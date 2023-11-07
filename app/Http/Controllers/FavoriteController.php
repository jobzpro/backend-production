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
            'company_id' => 'required_without:job_list_id',
            'job_list_id' => 'required_without:company_id',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Parameters not found.",
                'errors' => $validator->errors()
            ], 400);
        }

        $user = User::find($id);

        if ($user) {
            if ($request->filled('company_id')) {
                $company = Company::find($request['company_id']);

                if ($company) {
                    $user->favorites()->create([
                        'favoritable_id' => $company->id,
                        'favoritable_type' => get_class($company),
                        'favoriter_id' => $user->id,
                        'favoriter_type' => get_class($user),
                    ]);

                    return response([
                        'favorites' => $user->favoritedJobListings(),
                        'message' => "Successfully added Favorite",
                    ], 200);
                } else {
                    return response([
                        'message' => "Company not found.",
                    ], 400);
                }
            } else if ($request->filled('job_list_id')) {
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
            if ($favorite->favoritable instanceof JobList) {
                $favJobList = Favorite::with('favoriter', 'favoritable', 'jobListCompany')->find($id);
                return response([
                    'favorite' => $favJobList,
                    'message' => "Successful",
                ], 200);
            } else {
                return response([
                    'favorite' => $favorite,
                    'message' => "Successful",
                ], 200);
            }
        } else {
            return response([
                'message' => "Favorite not found",
            ], 400);
        }
    }
}

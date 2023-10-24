<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyReview;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompanyReviewController extends Controller
{
    public function postAReview(Request $request, $id)
    {
        $company = Company::find($id);

        if ($company) {
            $company->companyReviews()->create([
                'user_id' => $request['user_id'],
                'company_id' => $company->id,
                'rating' => $request['rating'],
                'currently_employed' => $request['currently_employed'],
                'employment_status' => $request['employment_status'],
                'position' => $request['position'],
                'content' => $request['content'],
                'anonymous' => $request['anonymous'] == 'true' ? 1 : 0,
            ]);

            return response([
                'reviews' => $company->companyReviews,
                'message' => "Review added successfully."
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }

    public function reviewsForCompany($id)
    {
        $company = Company::find($id);
        return response([
            'reviews' => $company->companyReviews,
            'message' => "Successful."
        ], 200);
    }

    public function reviewsOfJobseeker($id)
    {
        $jobseeker = User::find($id);
        return response([
            'reviews' => $jobseeker->companyReviews,
            'message' => "Successful."
        ], 200);
    }

    public function pinReview(Request $request, $id, $review_id)
    {
        $review = CompanyReview::find($review_id);

        if ($review) {
            $review->update(['pin' => $request['pin']]);

            return response([
                'review' => $review,
                'message' => "Review updated successfully."
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }

    public function deleteReview($id, $review_id)
    {
        $review = CompanyReview::find($review_id);

        if ($review) {
            if ($review->pin == false) {
                $review->delete();

                return response([
                    'message' => "Review deleted successfully."
                ], 200);
            } else {
                return response([
                    'message' => 'Unable to perform deletion. Company has pinned your Review.',
                ], 400);
            }
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }
}

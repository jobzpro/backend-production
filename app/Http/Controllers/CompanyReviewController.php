<?php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;

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
}

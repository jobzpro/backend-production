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
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\AppReview;
use App\Models\User;
use Illuminate\Http\Request;

class AppReviewController extends Controller
{
    public function index()
    {
        $reviews = AppReview::with('user')->where('pin', 'true')->get();

        return response([
            'reviews' => $reviews,
            'message' => "Successful"
        ], 200);
    }

    public function addReview(Request $request, $id)
    {
        $user = User::find($id);

        if ($user) {
            $user->appReviews()->create([
                'user_id' => $user->id,
                'rating' => $request['rating'],
                'content' => $request['content'],
                'anonymous' => $request['anonymous'] == 'true' ? 1 : 0,
            ]);

            return response([
                'message' => "Review added successfully."
            ], 200);
        } else {
            return response([
                'message' => 'Not found',
            ], 400);
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use Illuminate\Http\Request;
use App\Models\User;

class FollowerController extends Controller
{
    public function follow(Request $request)
    {
        $user_id = $request->input('user_id');
        $following_id = $request->input('following_id');
        $current_user = User::find($user_id);
        $checker = $current_user->isFollowing($following_id);
        if ($current_user) {
            if (!$checker) {
                Follower::create([
                    'user_id' => $request->input('user_id'),
                    'following_id' => $request->input('following_id')
                ]);
                // $current_user->follow($following_id);
                return response()->json($current_user, 200);
            } else {
                Follower::where('user_id', $current_user)->where('following_id', $following_id)->delete();
            }
        } else {
            return response([
                'message' => "Error",
            ], 400);
        }
    }
}

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
                $current_user->follow($following_id);
                return response([
                    'message' => "followed!",
                ], 200);
            } else if ($checker) {
                $current_user->unfollow($following_id);
                return response([
                    'message' => "unfollowed!",
                ], 200);
            }
        } else {
            return response([
                'message' => "Error",
            ], 400);
        }
    }

    public function allFollowing(Request $request, $id, $following_id)
    {
        $keyword = $request->query('keyword');
        $sortFilter = $request->query('sort');

        $current_user = User::with('experiences', 'certifications', 'account', 'references')->find($id);

        if (!empty($keyword)) {
            $current_user->where(function ($query) use ($keyword) {
                $query->whereHas('currentExperience', function ($q) use ($keyword) {
                    $q->where('position', 'LIKE', '%' . $keyword . '%');
                })
                    ->orWhere('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
            });
        }

        $current_user->whereHas('userRoles', function ($q) {
            $q->where('role_id', 3);
        });

        $users = $this->applySortFilter($current_user, $sortFilter);

        return response([
            'users' => $users->following()->paginate(10),
            'message' => 'Success',
        ], 200);
    }
}

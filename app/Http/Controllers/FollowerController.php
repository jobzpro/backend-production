<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Collection;

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

    public function addFriend(Request $request)
    {
        $user_id = $request->input('user_id');
        $following_id = $request->input('following_id');
        $current_user = User::find($user_id);
        $checker = $current_user->isFollowing($following_id);
        if ($current_user) {
            if ($checker) {
                $current_user->follow($following_id);
                return response([
                    'message' => "followed!",
                ], 200);
            } else if ($checker) {
                $current_user->declineFollow($following_id);
                return response([
                    'message' => "add!",
                ], 200);
            }
        } else {
            return response([
                'message' => "Error",
            ], 400);
        }
    }

    public function declineFriend(Request $request)
    {
        $user_id = $request->input('user_id');
        $following_id = $request->input('following_id');
        $current_user = User::find($user_id);
        $checker = $current_user->isFollowing($following_id);
        if ($current_user) {
            if ($checker) {
                $current_user->declineFollow($following_id);
                return response([
                    'message' => "decline!",
                ], 200);
            }
        } else {
            return response([
                'message' => "Error",
            ], 400);
        }
    }
    public function isFollowChecker($id, $following_id)
    {
        $current_user = User::find($id);

        if (!$current_user) {
            return response([
                'message' => 'User not found.',
            ], 404);
        }

        $isFollowing = $current_user->isFollowing($following_id);
        if (!$isFollowing) {
            return response([
                'user_id' => $following_id,
                'is_following' => false,
            ], 200);
        } else {
            return response([
                'user_id' => $following_id,
                'is_following' => true,
            ], 200);
        }
    }

    public function allUser(Request $request, $id)
    {
        $keyword = $request->query('keyword');
        $sortFilter = $request->query('sort');
        $filter = $request->query('filter');

        if (empty($filter)) {
            $current_user = User::with('experiences', 'certifications', 'account', 'references', 'followers');

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

            $current_user = $this->applySortFilter($current_user, $sortFilter, $id);

            return response([
                'users' => $current_user->paginate(10),
                'message' => 'Success',
            ], 200);
        } else if ($filter == "following") {
            $following = Follower::where('user_id', $id);
            $followingUser = $following->with('followingUser');
            // $followingUser = $following::with('followingUser.experiences', 'followingUser.certifications', 'followingUser.account', 'followingUser.references');

            if (!empty($keyword)) {
                $followingUser->whereHas('followingUser', function ($query) use ($keyword) {
                    $query->where('first_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
                        ->orWhereHas('currentExperience', function ($q) use ($keyword) {
                            $q->where('position', 'LIKE', '%' . $keyword . '%');
                        });
                });
            }
            $followingUser->whereHas('followingUser.userRoles', function ($q) {
                $q->where('role_id', 3);
            });

            $followingPaginated = $followingUser->paginate(10);

            // $followingUsers = $followingPaginated->map(function ($follower) {
            //     return $follower->followingUser;
            // });

            $followingUsers = $followingPaginated->map(function ($follower) {
                return [
                    'user' => array_merge(
                        $follower->followingUser->toArray(),
                        [
                            'follower' => [
                                'id' => $follower->id,
                                'user_id' => $follower->user_id,
                                'following_id' => $follower->following_id,
                            ],
                        ]
                    ),
                ];
            });

            $current_user = $this->followApplySortFilter($followingUsers, $sortFilter, $id);

            return response([
                'users' => $followingPaginated->setCollection($current_user),
                'message' => 'Success',
            ], 200);
        }
        // else if ($filter == "follower") {
        //     $follower = Follower::where('following_id', $id);
        //     $followerUser = $follower->with('followerUser');
        //     // $followerUser = $follower::with('followerUser.experiences', 'followerUser.certifications', 'followerUser.account', 'followerUser.references');

        //     if (!empty($keyword)) {
        //         $followerUser->whereHas('followerUser', function ($query) use ($keyword) {
        //             $query->where(function ($q) use ($keyword) {
        //                 $q->where('first_name', 'LIKE', '%' . $keyword . '%')
        //                     ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
        //             })->orWhereHas('currentExperience', function ($q) use ($keyword) {
        //                 $q->where('position', 'LIKE', '%' . $keyword . '%');
        //             });
        //         });
        //     }
        //     $followerUser->whereHas('followerUser.userRoles', function ($q) {
        //         $q->where('role_id', 3);
        //     });

        //     $followerPaginated = $followerUser->paginate(10);

        //     $followerUsers = $followerPaginated->map(function ($follower) {
        //         return $follower->followingUser;
        //     });

        //     $current_user = $this->followApplySortFilter($followerUsers, $sortFilter, $id);

        //     return response([
        //         'users' =>  $followerPaginated->setCollection($current_user),
        //         'message' => 'Success',
        //     ], 200);
        // }
    }

    private function applySortFilter($users, $sortFilter, $id)
    {
        switch ($sortFilter) {
            case 'desc':
                return $users->orderBy('first_name', 'DESC')->whereNotNull('first_name')->whereNotNull('last_name')->whereNot('account_id', $id);
            case 'asc':
                return $users->orderBy('first_name', 'ASC')->whereNotNull('first_name')->whereNotNull('last_name')->whereNot('account_id', $id);
            case 'Profile Completion':
                return $users->get()->sortByDesc('profile_completion')->whereNotNull('first_name')->whereNotNull('last_name')->whereNot('account_id', $id);
            default:
                return $users->orderBy('first_name', 'ASC')->whereNotNull('first_name')->whereNotNull('last_name')->whereNot('account_id', $id);
        }
    }

    private function followApplySortFilter(Collection $users, $sortFilter, $id)
    {
        // Filter out users with null first_name, last_name and exclude current user
        $filteredUsers = $users->filter(function ($user) use ($id) {
            return !is_null($user->first_name) && !is_null($user->last_name) && $user->account_id != $id;
        });

        // Apply sorting based on the sort filter
        switch ($sortFilter) {
            case 'desc':
                return $filteredUsers->sortByDesc('first_name')->values();
            case 'asc':
                return $filteredUsers->sortBy('first_name')->values();
            case 'Profile Completion':
                return $filteredUsers->sortByDesc('profile_completion')->values();
            default:
                return $filteredUsers->sortBy('first_name')->values();
        }
    }
}

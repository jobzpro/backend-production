<?php

namespace App\Http\Controllers;

use App\Models\Follower;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
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
				$current_user->acceptFriendRequest($following_id);
				return response([
					'message' => "success",
				], 200);
			}
			// else if ($checker) {
			//     $current_user->declineFollow($following_id);
			//     return response([
			//         'message' => "decline",
			//     ], 200);
			// }
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

		if (empty($user_id) && empty($following_id)) {
			Follower::where(function ($query) use ($user_id, $following_id) {
				$query->where('user_id', $user_id)
					->where('following_id', $following_id);
			})->orWhere(function ($query) use ($user_id, $following_id) {
				$query->where('user_id', $following_id)
					->where('following_id', $user_id);
			})->forceDelete();

			return response([
				'message' => "Success"
			], 200);
		} else {
			return response([
				'message' => "id missing"
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
			$current_user = User::with(['followings', 'followers', 'experiences', 'certifications', 'account', 'references'])
				->whereDoesntHave('followings', function ($query) use ($id) {
					// $query->where('user_id', $id);
					$query->where('following_id', $id);
				})
				->whereDoesntHave('followers', function ($query) use ($id) {
					// $query->where('following_id', $id);
					$query->where('user_id', $id);
				})
				->whereHas('userRoles', function ($q) {
					$q->where('role_id', 3);
				});

			if (!empty($keyword)) {
				$current_user->where(function ($query) use ($keyword) {
					$query->whereHas('currentExperience', function ($q) use ($keyword) {
						$q->where('position', 'LIKE', '%' . $keyword . '%');
					})
						->orWhere('first_name', 'LIKE', '%' . $keyword . '%')
						->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
				});
			}

			$current_user = $this->applySortFilter($current_user, $sortFilter, $id);

			return response([
				'users' => $current_user->paginate(12),
				'message' => 'Success',
			], 200);
		} else if ($filter == "request") {
			$following = Follower::where('following_id', $id)->where("status", 1);
			$followingUser = $following->with('followerUser');
			if (!empty($keyword)) {
				$followingUser->whereHas('followerUser', function ($query) use ($keyword) {
					$query->where('first_name', 'LIKE', '%' . $keyword . '%')
						->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
						->orWhereHas('currentExperience', function ($q) use ($keyword) {
							$q->where('position', 'LIKE', '%' . $keyword . '%');
						});
				});
			}

			$followingPaginated = $followingUser->paginate(12);

			$followingUsers = $followingPaginated->getCollection()->map(function ($follower) {
				return array_merge(
					$follower->followerUser->toArray(),
					[
						'follower' => [
							'id' => $follower->id,
							'user_id' => $follower->user_id,
							'following_id' => $follower->following_id,
						],
					]
				);
			});

			$current_user = $this->followApplySortFilter($followingUsers, $sortFilter, $id);

			return response([
				'users' => $followingPaginated->setCollection($current_user),
				// 'users' => $followingPaginated,
				'message' => 'Success',
			], 200);
		}
		if ($filter == "pending") {
			$following = Follower::where('user_id', $id)->where("status", 1);
			$followingUser = $following->with('followingUser');
			if (!empty($keyword)) {
				$followingUser->whereHas('followingUser', function ($query) use ($keyword) {
					$query->where('first_name', 'LIKE', '%' . $keyword . '%')
						->orWhere('last_name', 'LIKE', '%' . $keyword . '%')
						->orWhereHas('currentExperience', function ($q) use ($keyword) {
							$q->where('position', 'LIKE', '%' . $keyword . '%');
						});
				});
			}

			$followingPaginated = $followingUser->paginate(12);

			$followingUsers = $followingPaginated->getCollection()->map(function ($follower) {
				return array_merge(
					$follower->followingUser->toArray(),
					[
						'follower' => [
							'id' => $follower->id,
							'user_id' => $follower->user_id,
							'following_id' => $follower->following_id,
						],
					]
				);
			});

			$current_user = $this->followApplySortFilter($followingUsers, $sortFilter, $id);

			return response([
				'users' => $followingPaginated->setCollection($current_user),
				// 'users' => $followingPaginated,
				'message' => 'Success',
			], 200);
		} else if ($filter == "friends") {
			$query = Follower::query()
				->where('status', 0)
				->where(function ($query) use ($id) {
					$query->where('user_id', $id)
						->orWhere('following_id', $id);
				})
				->with(['followingUser', 'followerUser']);

			if (!empty($keyword)) {
				$query->where(function ($query) use ($keyword) {
					$query->whereHas('followingUser', function ($query) use ($keyword) {
						$query->where('first_name', 'LIKE', '%' . $keyword . '%')
							->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
					})->orWhereHas('followerUser', function ($query) use ($keyword) {
						$query->where('first_name', 'LIKE', '%' . $keyword . '%')
							->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
					});
				});
			}

			$combinedFollowers = $query->get();

			$page = Paginator::resolveCurrentPage('page');
			$perPage = 10;
			$total = $combinedFollowers->count();
			$currentPageResults = $combinedFollowers->slice(($page - 1) * $perPage, $perPage)->values();
			$followingPaginated = new LengthAwarePaginator($currentPageResults, $total, $perPage, $page, [
				'path' => Paginator::resolveCurrentPath(),
			]);

			// Transform the paginated collection
			$followingUsers = $followingPaginated->getCollection()->map(function ($follower) use ($id) {
				$relatedUser = $follower->user_id == $id ? $follower->followingUser : $follower->followerUser;

				if ($relatedUser && $relatedUser->id != $id) {
					return array_merge(
						$relatedUser->toArray(),
						[
							'follower' => [
								'id' => $follower->id,
								'user_id' => $follower->user_id,
								'following_id' => $follower->following_id,
							],
						]
					);
				}
				return null;
			})->filter();

			$current_user = $this->followApplySortFilter($followingUsers, $sortFilter, $id);
			$followingPaginated->setCollection($current_user);
			return response([
				// 'users' => $followingPaginated,
				'users' => $followingPaginated->setCollection($current_user),
				'message' => 'Success',
			], 200);
		}
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
			return !is_null($user['first_name']) && !is_null($user['last_name']) && $user['account_id'] != $id;
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

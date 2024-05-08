<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Follower extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'following_id'
    ];

    public function followerUser()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function followingUser()
    {
        return $this->belongsTo(User::class, 'following_id', 'id');
    }

    // public function keywordFollowingSearch($keyword)
    // {
    //     return $this->whereHas('followingUser', function ($query) use ($keyword) {
    //         $query->where(function ($q) use ($keyword) {
    //             $q->where('first_name', 'LIKE', '%' . $keyword . '%')
    //                 ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
    //         })->orWhereHas('followingUser.currentExperience', function ($q) use ($keyword) {
    //             $q->where('position', 'LIKE', '%' . $keyword . '%');
    //         });
    //     });
    // }

    public function keywordFollowingSearch($query, $keyword)
    {
        return $query->whereHas('followingUser', function ($q) use ($keyword) {
            $q->where(function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
            });
        })->orWhereHas('followingUser.currentExperience', function ($q) use ($keyword) {
            $q->where('position', 'LIKE', '%' . $keyword . '%');
        });
    }
    public function keywordFollowerSearch($keyword)
    {
        return $this->whereHas('followerUser', function ($query) use ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('first_name', 'LIKE', '%' . $keyword . '%')
                    ->orWhere('last_name', 'LIKE', '%' . $keyword . '%');
            })->orWhereHas('followingUser.currentExperience', function ($q) use ($keyword) {
                $q->where('position', 'LIKE', '%' . $keyword . '%');
            });
        });
    }
}

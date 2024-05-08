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
}

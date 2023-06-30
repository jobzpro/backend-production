<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserNotification extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'job_application_id',
        'user_id',
        'title',
        'description',
        'is_Read'
    ];


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_Read' => 'boolean',
    ];


    public function user():BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function job_application(): BelongsTo{
        return $this->belongsTo(JobApplication::class);
    }
}



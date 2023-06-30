<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        'user_id',
        'job_list_id',
        'status',
        'applied_at',
    ];

    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }
}

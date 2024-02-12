<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobListDealbreaker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_list_id',
        'dealbreaker_id',
        'dealbreaker_choice_id',
        'required',
    ];

    protected $casts = [
        'required' => 'boolean',
    ];

    public function jobList(): BelongsTo
    {
        return $this->belongsTo(JobList::class);
    }

    public function dealbreaker(): BelongsTo
    {
        return $this->belongsTo(Dealbreaker::class, 'dealbreaker_id');
    }

    // correct answer
    public function choice(): HasOne
    {
        return $this->hasOne(DealbreakerChoice::class, 'dealbreaker_choice_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobApplication extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =[
        'user_id',
        'job_list_id',
        'status',
        'applied_at',
        'resume_path',
        'authorized_to_work_in_us',
        'vaccinated_with_booster',
        'able_to_commute'
    ];

    protected $casts = [
        'applied_at' => 'datetime',
    ];


    public function user(): BelongsTo{
        return $this->belongsTo(User::class);
    }

    public function jobList(): BelongsTo{
        return $this->belongsTo(JobList::class, 'job_list_id');
    }

    public function jobInterviews(): HasMany{
        return $this->hasMany(JobInterview::class);
    }

    
}

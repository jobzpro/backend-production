<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobInterview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'applicant_id',
        'employer_id',
        'job_application_id',
        'notes',
        'meeting_link',
        'company_id',
        'job_list_id',
        'status',
        'interview_date',
    ];

    public function jobList(): BelongsTo
    {
        return $this->belongsTo(JobList::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class, 'id');
    }

    public function applicant(): BelongsTo
    {
        return $this->belongsTo(User::class, 'applicant_id', 'id');
    }

    public function role(): HasOne
    {
        return $this->hasOne(Role::class, 'id', 'employer_id');
    }

    public function userRole(): HasOne
    {
        return $this->hasOne(UserRole::class, 'user_id', 'employer_id' );
    }

    public function User(): HasOne
    {
        return $this->hasOne(User::class, 'id', 'employer_id');
    }
}

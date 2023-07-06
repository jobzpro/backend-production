<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ];

    public function jobList(): BelongsTo{
        return $this->belongsTo(JobList::class);
    }

    public function jobApplications(): HasMany{
        return $this->hasMany(JobApplication::class);
    }
}

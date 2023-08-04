<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class JobList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_title',
        'description',
        'salary',
        'company_id',
        'experience_level_id',
        'min_salary',
        'max_salary',
        'number_of_vacancies',
        'hiring_urgency',
        'pay_type',
        'resume_required',
        'start_conversion',
        'send_auto_rejection',
        'reject_candidates',
        'reject_time_limit',
        'other_email',
        'status',
        'show_pay',
        'can_applicant_with_criminal_record_apply',
        'can_start_messages',
        'send_auto_reject_emails',
        'auto_reject',
        'time_limit',
        'other_enail',
        'industry_id',
        'files',
        'job_excempt_from_local_laws',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'can_start_messages' => 'boolean',
        'send_auto_reject_emails' => 'boolean',
        'auto_reject' => 'boolean',
        'can_applicant_with_criminal_record_apply' => 'boolean',
        'job_excempt_from_local_laws' => 'boolean',
    ];

    public function company(): BelongsTo{
        return $this->belongsTo(Company::class);
    }

    public function job_types(): HasMany{
        return $this->hasMany(JobType::class, 'id');
    }

    public function job_benefits(): HasMany{
        return $this->hasMany(JobBenefits::class);
    }

    public function job_location(): HasOne{
        return $this->hasOne(JobLocation::class);
    }

    public function experience_level(): BelongsTo{
        return $this->belongsTo(ExperienceLevel::class, 'experience_level_id'); 
    }

    public function jobApplications(): HasMany{
        return $this->hasMany(JobApplication::class);
    }

    public function industry(): BelongsTo{
        return $this->belongsTo(Industry::class);
    }

    public function jobInterviews(): HasMany{
        return $this->hasMany(JobInterview::class);
    }
}

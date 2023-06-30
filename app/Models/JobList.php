<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobList extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
       'job_title',
       'description',
       'salary',
       'company_id',
       'experience_level_id',
       'job_location_id',
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
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function company(): BelongsTo{
        return $this->belongsTo(Company::class);
    }
}

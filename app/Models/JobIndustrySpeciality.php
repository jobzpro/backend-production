<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobIndustrySpeciality extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_list_id',
        'industry_speciality_id',
    ];
    public function job_list(): BelongsTo
    {
        return $this->belongsTo(JobList::class);
    }
    public function industrySpeciality(): BelongsTo
    {
        return $this->belongsTo(IndustrySpeciality::class);
    }
}

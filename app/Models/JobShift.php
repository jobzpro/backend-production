<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobShift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'standard_shift',
        'weekly_schedule',
        'supplemental_schedule',
    ];

    public function job_list(): BelongsTo{
        return $this->belongsTo(JobList::class);
    }
}

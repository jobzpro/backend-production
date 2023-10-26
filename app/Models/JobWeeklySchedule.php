<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWeeklySchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_list_id',
        'weekly_schedule_id',
    ];
    public function jobList(): BelongsTo
    {
        return $this->belongsTo(JobList::class);
    }

    public function weeklySchedule(): BelongsTo
    {
        return $this->belongsTo(WeeklySchedule::class);
    }
}

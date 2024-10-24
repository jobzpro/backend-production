<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobStandardShift extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_list_id',
        'standard_shift_id',
    ];
    public function jobList(): BelongsTo
    {
        return $this->belongsTo(JobList::class);
    }

}

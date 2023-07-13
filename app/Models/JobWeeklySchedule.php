<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobWeeklySchedule extends Model
{
    use HasFactory, SoftDeletes;

    protected $filable = [
        'job_list_id',
        'weekely_schedule_id',
    ];
}

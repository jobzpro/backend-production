<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
    ];
}

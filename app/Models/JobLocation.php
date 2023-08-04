<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JobLocation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'job_list_id',
        'location',
        'address',
        'description',
    ];

}

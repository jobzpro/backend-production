<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndustrySpeciality extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'industry_id',
        'description',
    ];
}

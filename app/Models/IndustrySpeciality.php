<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndustrySpeciality extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'industry_id',
        'description',
    ];

    public function industry(): BelongsTo{
        return $this->belongsTo(Industry::class);
    }

    public function jobIndustrySpeciality(): HasOne{
        return $this->hasOne(JobIndustrySpeciality::class);
    }
}

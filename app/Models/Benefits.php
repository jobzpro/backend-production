<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Benefits extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'description',
    ];

    public function job_benfit() :BelongsTo{
        return $this->belongsTo(JobBenefits::class);
    }
}

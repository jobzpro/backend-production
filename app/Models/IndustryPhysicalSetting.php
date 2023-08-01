<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class IndustryPhysicalSetting extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'industry_id',
        'physical_setting',
        'description',
    ];

    public function industry(): BelongsTo{
        return $this->belongsTo(Industry::class);
    }
}

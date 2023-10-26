<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Industry extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
    ];

    public function industrySpeciality(): HasMany
    {
        return $this->hasMany(IndustrySpeciality::class);
    }
    
    public function company():HasOne{
        return $this->hasOne(Company::class);
    }

    public function jobList(): HasOne{
        return $this->hasOne(JobList::class);
    }

}

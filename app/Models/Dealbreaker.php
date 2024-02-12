<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dealbreaker extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'question',
        'question_type',
        'default',
        'company_id',
    ];

    protected $casts = [
        'default' => 'boolean',
    ];

    //display all choices
    public function choices(): HasMany
    {
        return $this->hasMany(DealbreakerChoice::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function jobListDealbreaker(): HasMany
    {
        return $this->hasMany(JobListDealbreaker::class, 'id');
    }
}

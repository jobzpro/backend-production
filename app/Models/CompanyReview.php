<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class CompanyReview extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'user_id',
        'rating',
        'currently_employed',
        'employment_status',
        'position',
        'content',
        'pin',
        'anonymous',
    ];

    protected $casts = [
        'currently_employed' => 'boolean',
        'pin' => 'boolean',
        'anonymous' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class)->with('industry');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

}

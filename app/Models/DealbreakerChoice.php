<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class DealbreakerChoice extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'dealbreaker_id',
        'choice',
        'default',
    ];

    protected $casts = [
        'default' => 'boolean',
    ];

    public function dealbreaker(): BelongsTo
    {
        return $this->belongsTo(Dealbreaker::class, 'dealbreaker_id');
    }
}

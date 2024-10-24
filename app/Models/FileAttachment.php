<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class FileAttachment extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'path',
        'type',
        'size',
        'user_id',
        'is_certification',
    ];

    protected $casts = [
        'is_certification' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

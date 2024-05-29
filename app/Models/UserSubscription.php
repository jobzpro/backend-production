<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'product_plan_id',
        'connection_count',
        'post_count',
        'applicant_count',
        'expiry_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_plan(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_plan_id');
    }
}

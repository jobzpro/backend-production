<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPlanUser extends Model
{
    use HasFactory, SoftDeletes, Notifiable;
    protected $fillable = [
        'user_id',
        'product_id',
        'product_plan_id',
        'purchase_date',
        'purchase_expiry',
        'connection_left',
        'post_left',
        'application_left',
    ];

    public function product_users(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

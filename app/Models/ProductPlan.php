<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductPlan extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'product_id',
        'price',
        'recurring',
        'mode',
        'unit_label',
        'lookup_key',
        'checkout_url',
        'connection_count',
        'post_count',
        'applicant_count',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}

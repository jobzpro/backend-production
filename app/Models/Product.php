<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Product extends Model
{
    use HasFactory, SoftDeletes, Notifiable;

    protected $fillable = [
        'product_code',
        'name'
    ];

    public function product_users(): HasMany
    {
        return $this->hasMany(ProductPlan::class);
    }
}

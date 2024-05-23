<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Stripe\StripeClient;

class Product extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'product_code',
        'name'
    ];

    public function product_plans(): HasMany
    {
        return $this->hasMany(ProductPlan::class);
    }

    protected static function booted()
    {
        static::saved(function ($product) {
            // Check if the product has a Stripe product ID before updating
            if ($product->product_code) {
                $stripe = new StripeClient(env('STRIPE_SECRET'));

                // Update the Stripe product
                $stripe->products->update($product->product_code, [
                    'name' => $product->name,
                    // 'description' => $product->description,
                    // Add any other fields you need to update
                ]);
            }
        });
    }
}

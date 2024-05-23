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
        'name',
        'description'
    ];

    public function product_plans(): HasMany
    {
        return $this->hasMany(ProductPlan::class);
    }

    protected static function booted()
    {
        // static::saved(function ($product) {
        //     if ($product->product_code) {
        //         $stripe = new StripeClient(env('STRIPE_SECRET'));
        //         $stripe->products->update($product->product_code, [
        //             'name' => $product->name,
        //         ]);
        //     }
        // });
        static::created(function ($product) {
            $stripe = new StripeClient(env('STRIPE_SECRET'));
            $stripeProduct = $stripe->products->create([
                'name' => $product->name,
                // 'unit_label' => $product->unit_label,
                'description' => $product->description,
            ]);

            // Save the Stripe product ID to the database
            $product->product_code = $stripeProduct->id;
            $product->save();
        });

        static::updated(function ($product) {
            if ($product->product_code) {
                $stripe = new StripeClient(env('STRIPE_SECRET'));
                $stripe->products->update($product->product_code, [
                    'name' => $product->name,
                    // 'unit_label' => $product->unit_label,
                    'description' => $product->description,
                ]);
            }
        });
    }
}

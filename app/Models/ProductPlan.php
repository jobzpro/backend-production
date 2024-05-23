<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stripe\StripeClient;

class ProductPlan extends Model
{
    use HasFactory;

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

    protected static function booted()
    {
        static::saved(function ($price) {
            $product = $price->product;
            $stripe = new StripeClient(env('STRIPE_SECRET'));

            if ($product && $product->product_code) {
                if (!$price->stripe_price_id) {
                    // Create a new Stripe price
                    $stripePrice = $stripe->prices->create([
                        'unit_amount' => $price->amount * 100, // Stripe expects the amount in cents
                        'currency' => $price->currency,
                        'recurring' => ['interval' => $price->recurring],
                        'product' => $product->product_code,
                        'nickname' => $price->lookup_key,
                        'unit_label' => $product->unit_label,
                        'metadata' => [
                            'lookup_key' => $price->lookup_key,
                        ],
                    ]);
                    $stripeProduct = $stripe->product->create([
                        'unit_label' => $product->unit_label,
                    ]);
                    // Save the Stripe price ID to the database
                    $price->stripe_price_id = $stripePrice->id;
                    $price->save();
                    $stripeProduct->save();
                }
                $checkoutSession = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price' => $price->stripe_price_id,
                        'quantity' => 1,
                    ]],
                    'mode' => $price->mode ?? 'subscription',
                    'success_url' => route('checkout.success'),
                    'cancel_url' => route('checkout.cancel'),
                ]);
                $price->checkout_url = $checkoutSession->url;
                $price->save();
            }
        });
    }
}

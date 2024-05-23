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
        'price_code',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted()
    {
        // static::saved(function ($price) {
        //     $product = $price->product;
        //     $stripe = new StripeClient(env('STRIPE_SECRET'));

        //     if ($product && $product->product_code) {
        //         if (!$price->price_code) {
        //             $stripePrice = $stripe->prices->update([
        //                 'unit_amount' => $price->amount * 100,
        //                 'product' => $product->product_code,
        //                 'lookup_key' => $price->lookup_key,
        //                 'currency' => 'usd',
        //                 'recurring' => ['interval' => $price->recurring],
        //             ]);
        //             $price->price_code = $stripePrice->id;
        //             $price->save();
        //         }
        //         $checkoutSession = $stripe->checkout->sessions->create([
        //             'payment_method_types' => ['card'],
        //             'line_items' => [[
        //                 'price' => $price->price_code,
        //                 'quantity' => 1,
        //             ]],
        //             'mode' => $price->mode ?? 'subscription',
        //             'success_url' => 'http://localhost:3000',,
        //             'cancel_url' => 'http://localhost:3000',,
        //         ]);
        //         $price->checkout_url = $checkoutSession->url;
        //         $price->save();
        //     }
        // });

        static::created(function ($price) {
            $product = $price->product;
            $stripe = new StripeClient(env('STRIPE_SECRET'));

            if (!$price->price_code) {
                $stripePrice = $stripe->prices->update([
                    'unit_amount' => $price->amount * 100,
                    'product' => $product->product_code,
                    'lookup_key' => $price->lookup_key,
                    'currency' => 'usd',
                    'recurring' => ['interval' => $price->recurring],
                ]);
                $price->price_code = $stripePrice->id;
                $price->save();
            }
            $checkoutSession = $stripe->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price' => $price->price_code,
                    'quantity' => 1,
                ]],
                'mode' => 'subscription',
                'success_url' => 'http://localhost:3000',
                'cancel_url' => 'http://localhost:3000',
            ]);
            $price->checkout_url = $checkoutSession->url;
            $price->save();
        });

        static::updated(function ($price) {
            if ($price->price_code) {
                $product = $price->product;
                $stripe = new StripeClient(env('STRIPE_SECRET'));

                if (!$price->price_code) {
                    $stripe->prices->update($price->price_code, [
                        'unit_amount' => $price->amount * 100,
                        'product' => $product->product_code,
                        'lookup_key' => $price->lookup_key,
                        'currency' => 'usd',
                        'recurring' => ['interval' => $price->recurring],
                    ]);
                }

                $checkoutSession = $stripe->checkout->sessions->create([
                    'payment_method_types' => ['card'],
                    'line_items' => [[
                        'price' => $price->price_code,
                        'quantity' => 1,
                    ]],
                    'mode' => 'subscription',
                    'success_url' => 'http://localhost:3000',
                    'cancel_url' => 'http://localhost:3000',
                ]);

                $price->checkout_url = $checkoutSession->url;
                $price->save();
            }
        });
    }
}

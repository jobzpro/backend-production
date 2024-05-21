<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class ProductController extends Controller
{
    public function index()
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $products = $stripe->products->all();

            return response($products, 200);
        } catch (\Exception $e) {
            // Handle errors
            return response([
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    // public function jobseekerSubscription()
    // {
    //     $stripe = new StripeClient(env('STRIPE_SECRET'));

    //     try {
    //         $allProducts = $stripe->products->all();
    //         $productsWithPrices = collect($allProducts->data)->filter(function ($product) {
    //             return $product->active && $product->unit_label === 'jobseeker';
    //         })->map(function ($product) use ($stripe) {
    //             $prices = $stripe->prices->all(['product' => $product->id]);
    //             $price = $prices->data[0]->unit_amount_decimal;

    //             return [
    //                 // 'name' => $product->name,
    //                 // 'description' => $product->description,
    //                 // 'price' => $price,
    //                 'product' => $product,
    //                 'prices' => $prices,
    //             ];
    //         });
    //         return response($productsWithPrices, 200);
    //     } catch (\Exception $e) {
    //         // Handle errors
    //         return response([
    //             'message' => $e->getMessage(),
    //         ], 400);
    //     }
    // }


    public function jobseekerSubscription()
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $allProducts = $stripe->products->all();
            $productsWithPaymentLinks = [];
            foreach ($allProducts->data as $product) {
                if ($product->active && $product->metadata->unit_label === 'jobseeker') {
                    $prices = $stripe->prices->all(['product' => $product->id]);
                    $price = $prices->data[0]->unit_amount_decimal;
                    $session = $stripe->checkout->sessions->create([
                        'payment_method_types' => ['card'],
                        'line_items' => [
                            [
                                'price' => $prices->data[0]->id,
                                'quantity' => 1,
                            ],
                        ],
                        'mode' => 'payment',
                        'success_url' => 'http://localhost:3000/success',
                        'cancel_url' => 'http://localhost:3000/cancel',
                    ]);
                    $paymentLink = 'https://checkout.stripe.com/pay/' . $session->id;
                    $productsWithPaymentLinks[] = [
                        'name' => $product->name,
                        'description' => $product->description,
                        'price' => $price,
                        'payment_link' => $paymentLink,
                    ];
                }
            }

            return response()->json(['products' => $productsWithPaymentLinks], 200);
        } catch (\Exception $e) {
            // Handle errors
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
    public function employerSubscription()
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $allProducts = $stripe->products->all();
            $products = collect($allProducts->data)->filter(function ($product) {
                return $product->active && $product->unit_label === 'employer';
            });
            return response($products, 200);
        } catch (\Exception $e) {
            // Handle errors
            return response([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

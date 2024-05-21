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

    public function jobseekerSubscription()
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $paymentLinks = $stripe->paymentLinks->all();
            $productsWithPrices = collect($paymentLinks->data)->map(function ($paymentLink) use ($stripe) {
                // $products = $stripe->products->all(['product' => $product->id]);
                // $prices = $stripe->prices->all(['product' => $product->id]);
                $lineItem = $stripe->paymentLinks->allLineItems($paymentLink->id, []);
                // $price = $prices->data[0]->unit_amount_decimal;

                return [
                    // 'name' => $product->name,
                    // 'description' => $product->description,
                    // 'price' => $price,
                    // 'lineItem' => $lineItem,
                    'description' => $lineItem->description,
                    'price' => $lineItem,
                    'url' => $paymentLink->url,
                ];
            });

            return response($productsWithPrices, 200);
        } catch (\Exception $e) {
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
    //             return $product->active && $product->unit_label ===  'jobseeker';
    //         })->map(function ($product) use ($stripe) {
    //             $prices = $stripe->prices->all(['product' => $product->id]);
    //             $price = $prices->data[0]->unit_amount_decimal;

    //             return [
    //                 // 'name' => $product->name,
    //                 // 'description' => $product->description,
    //                 // 'price' => $price,
    //                 'product' => $product,
    //                 'price' => $prices,
    //             ];
    //         });

    //         return response($productsWithPrices, 200);
    //     } catch (\Exception $e) {
    //         return response([
    //             'message' => $e->getMessage(),
    //         ], 400);
    //     }
    // }

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

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
            $allProducts = $stripe->products->all();
            $productsWithPricesAndLinks = collect($allProducts->data)->filter(function ($product) {
                return $product->active && $product->metadata->unit_label === 'jobseeker';
            })->map(function ($product) use ($stripe) {
                $prices = $stripe->prices->all(['product' => $product->id]);
                // $paymentLinks = $stripe->paymentLinks->all(['product' => $product->id]);
                $price = $prices->data[0]->unit_amount_decimal;
                return [
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $price,
                    // 'payment_link' => isset($paymentLinks->data[0]) ? $paymentLinks->data[0]->url : null,
                ];
            });

            return response()->json($productsWithPricesAndLinks, 200);
        } catch (\Exception $e) {
            // Handle errors
            return response([
                'message' => $e->getMessage(),
            ], 400);
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

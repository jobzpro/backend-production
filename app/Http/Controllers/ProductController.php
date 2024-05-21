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
            $productsWithPrices = collect($allProducts->data)->filter(function ($product) {
                return $product->active && $product->metadata->unit_label === 'jobseeker';
            })->map(function ($product) use ($stripe) {
                $prices = $stripe->prices->all(['product' => $product->id]);
                $price = $prices->data[0]->unit_amount_decimal;

                return [
                    'name' => $product->name,
                    'description' => $product->description,
                    'price' => $price,
                ];
            });

            return response($productsWithPrices, 200);
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
            // Fetch all products from Stripe
            $allProducts = $stripe->products->all();

            // Filter products by active status and unit label
            $productsWithPrices = collect($allProducts->data)->filter(function ($product) {
                return $product->active && $product->metadata->unit_label === 'employer';
            })->map(function ($product) use ($stripe) {
                // Fetch prices for the product for both monthly and yearly billing intervals
                $monthlyPrices = $stripe->prices->all(['product' => $product->id, 'lookup_keys' => ['monthly']]);
                $yearlyPrices = $stripe->prices->all(['product' => $product->id, 'lookup_keys' => ['yearly']]);

                // Extract the price information for monthly and yearly billing intervals
                $monthlyPrice = $monthlyPrices->data[0]->unit_amount_decimal ?? null;
                $yearlyPrice = $yearlyPrices->data[0]->unit_amount_decimal ?? null;

                return [
                    'name' => $product->name,
                    'description' => $product->description,
                    'monthly_price' => $monthlyPrice,
                    'yearly_price' => $yearlyPrice,
                ];
            });

            return response($productsWithPrices, 200);
        } catch (\Exception $e) {
            // Handle errors
            return response([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

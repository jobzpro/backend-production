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
            // $products = $stripe->products->search([
            //     'query' => 'active:\'true\' AND unit_label:\'jobseeker\'',
            // ]);
            $allProducts = $stripe->products->all();
            $products = collect($allProducts->data)->filter(function ($product) {
                return $product->active && $product->unit_label === 'jobseeker';
            });
            return response($products, 200);
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

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\StripeClient;

class ProductController extends Controller
{
    public function index()
    {
        // Initialize Stripe client with your secret key
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            // Fetch a list of products from Stripe
            $products = $stripe->products->all();

            return response($products, 200);
        } catch (\Exception $e) {
            // Handle errors
            return response([
                'message' => $e->getMessage(),
            ], 400);
        }
    }
}

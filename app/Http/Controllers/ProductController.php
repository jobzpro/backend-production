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
            $products = $stripe->products->all();
            $productDetails = [];

            foreach ($products->data as $product) {
                $prices = $stripe->prices->all(['product' => $product->id]);
                if (count($prices->data) > 0) {
                    $price = $prices->data[0];
                    foreach ($prices->data as $price) {
                        if ($price->active && $product->unit_label === "jobseeker") {
                            $mode = $price->recurring ? 'subscription' : 'payment';
                            $session = $stripe->checkout->sessions->create([
                                'payment_method_types' => ['card'],
                                'line_items' => [[
                                    'price' => $price->id,
                                    'quantity' => 1,
                                ]],
                                'mode' => $mode,
                                'success_url' => "http://localhost:3000",
                                'cancel_url' => "http://localhost:3000",
                            ]);
                            $productDetails[] = [
                                'product_name' => $product->name,
                                'price' => number_format($price->unit_amount / 100, 2),
                                'mode' => $mode,
                                'unit_label' => $product->unit_label,
                                'lookup_key' => $price->lookup_key,
                                'recurring' => $price->recurring->interval,
                                'checkout_url' => $session->url,
                            ];
                        }
                    }
                }
            }
            return response()->json($productDetails, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }

    public function employerSubscription()
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));

        try {
            $products = $stripe->products->all();
            $productDetails = [];

            foreach ($products->data as $product) {
                $prices = $stripe->prices->all(['product' => $product->id]);
                $productPrices = [];
                if (count($prices->data) > 0) {
                    $price = $prices->data[0];
                    foreach ($prices->data as $price) {
                        if ($price->active && $product->unit_label === "employer") {
                            $mode = $price->recurring ? 'subscription' : 'payment';
                            $session = $stripe->checkout->sessions->create([
                                'payment_method_types' => ['card'],
                                'line_items' => [[
                                    'price' => $price->id,
                                    'quantity' => 1,
                                ]],
                                'mode' => $mode,
                                'success_url' => "http://localhost:3000",
                                'cancel_url' => "http://localhost:3000",
                            ]);
                            $productPrices[] = [
                                // 'product_name' => $product->name,
                                'price' => number_format($price->unit_amount / 100, 2),
                                'mode' => $mode,
                                'unit_label' => $product->unit_label,
                                'lookup_key' => $price->lookup_key,
                                'recurring' => $price->recurring->interval,
                                'checkout_url' => $session->url,
                            ];
                        }
                    }
                    // if (!empty($productPrices)) {
                    //     $productDetails[$product->name] = $productPrices;
                    // }
                    $groupedProductDetails = [];
                    foreach ($productDetails as $detail) {
                        $name = $detail['name'];
                        unset($detail['name']);
                        $groupedProductDetails[$name][] = $detail['plans'][0];
                    }
                }
            }
            // return response()->json($productDetails, 200);
            return response()->json($groupedProductDetails, 200);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 400);
        }
    }
    // public function jobseekerSubscription()
    // {
    //     $stripe = new StripeClient(env('STRIPE_SECRET'));

    //     try {
    //         $paymentLinks = $stripe->paymentLinks->all();
    //         $productsWithPrices = collect($paymentLinks->data)->map(function ($paymentLink) use ($stripe) {
    //             $lineItem = $stripe->paymentLinks->allLineItems($paymentLink->id, []);

    //             return [
    //                 'description' => $lineItem->data[0]->description,
    //                 'price' => number_format($lineItem->data[0]->amount_total, 2),
    //                 'lookup_key' => $lineItem->data[0]->price->lookup_key,
    //                 'url' => $paymentLink->url,
    //             ];
    //         });

    //         return response($productsWithPrices, 200);
    //     } catch (\Exception $e) {
    //         return response([
    //             'message' => $e->getMessage(),
    //         ], 400);
    //     }
    // }


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

    // public function employerSubscription()
    // {
    //     $stripe = new StripeClient(env('STRIPE_SECRET'));

    //     try {
    //         $allProducts = $stripe->products->all();
    //         $products = collect($allProducts->data)->filter(function ($product) {
    //             return $product->active && $product->unit_label === 'employer';
    //         });
    //         return response($products, 200);
    //     } catch (\Exception $e) {
    //         // Handle errors
    //         return response([
    //             'message' => $e->getMessage(),
    //         ], 400);
    //     }
    // }
}

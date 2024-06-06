<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
        $productDetails = [];

        try {
            $lastProductId = null;

            do {
                $productParams = ['limit' => 100];
                if ($lastProductId) {
                    $productParams['starting_after'] = $lastProductId;
                }

                $products = $stripe->products->all($productParams);

                foreach ($products->data as $product) {
                    if ($product->unit_label === "jobseeker") {
                        $productPrices = [];
                        $lastPriceId = null;

                        do {
                            $priceParams = [
                                'product' => $product->id,
                                'limit' => 100,
                            ];
                            if ($lastPriceId) {
                                $priceParams['starting_after'] = $lastPriceId;
                            }

                            $prices = $stripe->prices->all($priceParams);

                            foreach ($prices->data as $price) {
                                if ($price->active) {
                                    $mode = $price->recurring ? 'subscription' : 'payment';

                                    $session = $stripe->checkout->sessions->create([
                                        'payment_method_types' => ['card'],
                                        'line_items' => [[
                                            'price' => $price->id,
                                            'quantity' => 1,
                                        ]],
                                        'mode' => $mode,
                                        'success_url' => "http://localhost:3000/",
                                        'cancel_url' => "http://localhost:3000/",
                                    ]);

                                    $productPrices[] = [
                                        'price' => number_format($price->unit_amount / 100, 2),
                                        'mode' => $mode,
                                        'unit_label' => $product->unit_label,
                                        'lookup_key' => $price->lookup_key,
                                        'recurring' => $price->recurring ? $price->recurring->interval : 'one-time',
                                        'checkout_url' => $session->url,
                                    ];
                                }
                            }

                            if (!empty($prices->data)) {
                                $lastPriceId = end($prices->data)->id;
                            }
                        } while ($prices->has_more);

                        $productDetails[] = [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'description' => $product->description,
                            'plan' => $productPrices,
                        ];
                    }
                }

                if (!empty($products->data)) {
                    $lastProductId = end($products->data)->id;
                }
            } while ($products->has_more);

            return response()->json($productDetails, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function employerSubscription()
    {
        $stripe = new StripeClient(env('STRIPE_SECRET'));
        $productDetails = [];
        try {
            $lastProductId = null;
            do {
                $productParams = ['limit' => 100];
                if ($lastProductId) {
                    $productParams['starting_after'] = $lastProductId;
                }
                $products = $stripe->products->all($productParams);


                foreach ($products->data as $product) {
                    if ($product->unit_label === "employer") {
                        $productPrices = [];
                        $lastPriceId = null;

                        do {
                            $priceParams = [
                                'product' => $product->id,
                                'limit' => 100,
                            ];
                            if ($lastPriceId) {
                                $priceParams['starting_after'] = $lastPriceId;
                            }
                            $prices = $stripe->prices->all(['product' => $product->id]);
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
                            if (!empty($prices->data)) {
                                $lastPriceId = end($prices->data)->id;
                            }
                        } while ($prices->has_more);
                        $productDetails[] = [
                            'product_id' => $product->id,
                            'product_name' => $product->name,
                            'description' => $product->description,
                            'plan' => $productPrices,
                        ];
                    }
                }
                if (!empty($products->data)) {
                    $lastProductId = end($products->data)->id;
                }
            } while ($products->has_more);

            return response()->json($productDetails, 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }

    public function insertSubscription(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
            'product_plan_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Registration Unsuccessful",
                'errors' => $validator->errors(),
            ], 400);
        }

        if (!isset($id)) {
            return response([
                'message' => "id is missing",
                'errors' => $validator->errors(),
            ], 400);
        } else {
            $user = User::find($request->input('user_id'));
            $product = Product::where('product_code', $request->input('product_id'))->first();
            $productPlan = ProductPlan::where('product_code', $request->input('price_id'))->first();
            $expiryMonths = $productPlan->recurring == "month" ? 1 : 6;
            $expiryAt = Carbon::now()->addMonths($expiryMonths);
            $res = $user->user_subscription()->create([
                'product_id' => $product->product_code,
                'product_plan_id' => $productPlan->price_code,
                'connection_count' => $productPlan->connection_count,
                'post_count' => $productPlan->post_count,
                'applicant_count' => $productPlan->applicant_count,
                'expiry_at' => $expiryAt,
            ]);
            return response([
                'message' => "Success",
                'user_subscription' => $res,
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

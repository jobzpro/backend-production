<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductPlan;
use App\Models\User;
use App\Models\UserSubscription;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function jobseekerSubscription($id)
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
                    if ($product->active && $product->unit_label === "jobseeker") {
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
                                    $user_id = $id;
                                    $session = $stripe->checkout->sessions->create([
                                        'payment_method_types' => ['card'],
                                        'line_items' => [[
                                            'price' => $price->id,
                                            'quantity' => 1,
                                        ]],
                                        'mode' => $mode,
                                        'success_url' => env('BASE_URL'),
                                        'cancel_url' => env('BASE_URL'),
                                        'metadata' => [
                                            'user_id' => $user_id
                                        ]
                                    ]);

                                    $productPrices[] = [
                                        'price_id' => $price->id,
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
                            'created' => $product->created,
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

    public function employerSubscription($id)
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
                    if ($product->active && $product->unit_label === "employer") {
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
                                        'success_url' => env('BASE_URL'),
                                        'cancel_url' => env('BASE_URL'),
                                        'metadata' => [
                                            'user_id' => $id
                                        ]
                                    ]);

                                    $productPrices[] = [
                                        'price_id' => $price->id,
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
                            'created' => $product->created,
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
            'user_id' => 'required',
            'product_id' => 'required',
            'price_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response([
                'message' => "Registration Unsuccessful",
                'errors' => $validator->errors(),
            ], 400);
        }

        $user = User::find($request->input('user_id'));
        $userSubscriptionExist = UserSubscription::displaySubscription($user->id);
        $userSubscriptionFree = UserSubscription::displaySubscriptionFree($user->id);
        $product = Product::where('product_code', $request->input('product_id'))->first();
        $productPlan = ProductPlan::where('price_code', $request->input('price_id'))->first();
        $expiryMonths = $productPlan->recurring == "month" ? 1 : 12;
        $expiryAt = Carbon::now()->addMonths($expiryMonths);

        if (!$product && !$productPlan) {
            return response([
                'message' => "Product or Product Plan not found",
            ], 400);
        }

        if ($userSubscriptionFree) {
            $res = UserSubscription::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'product_plan_id' => $productPlan->id,
                'connection_count' => $productPlan->connection_count,
                'post_count' => $productPlan->post_count,
                'applicant_count' => $productPlan->applicant_count,
                'expiry_at' => $expiryAt,
            ]);
            return response([
                'message' => "Success",
                'user_subscription' => $res,
            ], 200);
        }

        if (!!$userSubscriptionExist) {
            if ($user->userRoles->role_id != 3) {
                $res = UserSubscription::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'product_plan_id' => $productPlan->id,
                    'connection_count' => $productPlan->connection_count,
                    'post_count' => $productPlan->post_count,
                    'applicant_count' => $productPlan->applicant_count,
                    'expiry_at' => $expiryAt,
                ]);
                return response([
                    'message' => "Success",
                    'user_subscription' => $res,
                ], 200);
            } else {
                $existingSubscriptionExpiry = Carbon::parse($userSubscriptionExist->expiry_at)->addMonths($expiryMonths);
                $res = $user->user_subscription()->create([
                    'product_id' => $product->id,
                    'product_plan_id' => $productPlan->id,
                    'connection_count' => $productPlan->connection_count,
                    'post_count' => $productPlan->post_count,
                    'applicant_count' => $productPlan->applicant_count,
                    'expiry_at' => $existingSubscriptionExpiry,
                ]);
                return response([
                    'message' => "Success",
                    'user_subscription' => $res,
                ], 200);
            }
        }
    }

    public function getSubscription($id)
    {
        if (!isset($id)) {
            return response([
                'message' => 'id is missing',
            ], 400);
        }

        $userSubscription = UserSubscription::displaySubscription($id);
        $userSubscriptionCount = UserSubscription::displaySubscriptionFree($id);
        $user = User::find($id);

        if (!$userSubscriptionCount) {
            if ($user->userRoles->role_id === 3) {
                $res = UserSubscription::create([
                    'user_id' => $id,
                    'connection_count' => 3,
                    'post_count' => 0,
                    'applicant_count' => 0,
                    'expiry_at' => Carbon::now()->addMonths(1),
                ]);
            } else {
                $employer = UserSubscription::where('user_id', $id)->first();
                if (!$employer) {
                    $res = UserSubscription::create([
                        'user_id' => $id,
                        'connection_count' => 0,
                        'post_count' => 3,
                        'applicant_count' => 3,
                        'expiry_at' => Carbon::now()->addMonths(6),
                    ]);
                } else {
                    return response([
                        'message' => 'Subscription over',
                        // 'user_subscription' => $res,
                        // 'user_subscription' => $userSubscriptionArray,
                    ], 400);
                }
            }
            return response([
                'message' => 'free',
                'user_subscription' => $res,
                // 'user_subscription' => $userSubscriptionArray,
            ], 200);
        }

        $now = Carbon::now();

        if ($user->userRoles->role_id === 3) {
            $userSubscriptionArray = $userSubscriptionCount->toArray();
            $connection_count_total = UserSubscription::displayConnectionCountTotal($id);
            $userSubscriptionArray['total_connection_count'] = $connection_count_total;

            // $res = [
            //     'total_post_count' => $userSubscriptionArray->sum('post_count'),
            //     'total_connection_count' => $userSubscriptionArray->sum('connection_count'),
            //     $userSubscriptionArray
            // ];
            return response([
                'message' => "subscribe",
                'user_subscription' => $userSubscriptionArray
            ], 200);
        } else {
            if ($userSubscription->expiry_at) {
                $expiryDate = Carbon::parse($userSubscription->expiry_at);
                if ($now > $expiryDate) {
                    $userSubscriptionArray['is_subscribe'] = false;
                    return response([
                        'message' => 'free',
                        'user_subscription' => $userSubscriptionArray,
                    ], 200);
                } else if ($now <= $expiryDate) {
                    $userSubscriptionArray = $userSubscription->toArray();
                    $userSubscriptionArray['is_subscribe'] = true;

                    // $res = [
                    //     'total_post_count' => $userSubscriptionArray->sum('post_count'),
                    //     'total_connection_count' => $userSubscriptionArray->sum('connection_count'),
                    //     $userSubscriptionArray
                    // ];
                    return response([
                        'message' => "subscribe",
                        'user_subscription' => $userSubscriptionArray
                    ], 200);
                } else {
                    return response([
                        'message' => "something wrong, try again later"
                    ], 400);
                }
            }
        }
    }

    public function getSubscriptionEmployer($id)
    {
        if (!isset($id)) {
            return response([
                'message' => 'id is missing',
            ], 400);
        }

        $userSubscriptions = UserSubscription::with("product", "product_plan")
            ->where("user_id", $id)
            ->orderBy('created_at', 'DESC')
            ->where('expiry_at', '>', now())
            ->get();

        if ($userSubscriptions->isEmpty()) {
            return response([
                'message' => 'No active subscriptions found',
                'is_subscribe' => false,
            ], 200);
        }

        $userSubscriptionArray = [];
        $isSubscribed = false;

        foreach ($userSubscriptions as $subscription) {
            $now = Carbon::now();
            $expiryDate = Carbon::parse($subscription->expiry_at);
            if ($now > $expiryDate) {
                $subscriptionArray = $subscription->toArray();
                $subscriptionArray['is_subscribe'] = false;
                $userSubscriptionArray[] = $subscriptionArray;
            } else {
                $subscriptionArray = $subscription->toArray();
                $subscriptionArray['is_subscribe'] = true;
                $userSubscriptionArray[] = $subscriptionArray;
                $isSubscribed = true;
            }
        }

        $totalPostCount = $userSubscriptions->sum('post_count');
        $totalApplicantCount = $userSubscriptions->sum('applicant_count');

        $response = [
            'message' => $isSubscribed ? "Subscribe" : "Free",
            'user_subscription' => [
                'subscriptions' => $userSubscriptionArray,
                'applicant_count' => $totalApplicantCount,
                'total_post_count' => $totalPostCount,
            ],
        ];

        return response($response, 200);
    }
}

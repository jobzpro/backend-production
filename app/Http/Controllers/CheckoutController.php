<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class CheckoutController extends Controller
{
	/**
	 * GET: /subscription-checkout
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function __invoke(Request $request)
	{

		$user = User::find($request->user()->id);
		$price_id = $request->query('price_id');
		$plan_name = $request->query('plan_name');

		if ($user->subscribed($plan_name)) {
			// If the user is subscribed but wants to change/upgrade their plan
			if (!$user->subscribedToPrice($price_id, $plan_name)) {
				// Swap the subscription to the new price plan
				$user->subscription($plan_name)->swap($price_id);
			}
		} else {
			// If the user is not subscribed, create a new subscription
			$checkout = $user
				->newSubscription($plan_name, $price_id)
				->checkout([
					'success_url' => env('FRONTEND_URL'),
					'cancel_url' => env('FRONTEND_URL') . 'monization',
				]);

			return response()->json([
				'checkout_url' => $checkout->url,
			]);
		}

		return response()->json([
			'message' => 'Subscription updated successfully',
		]);
	}
}

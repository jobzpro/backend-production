<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Subscription;

class SubscriptionController extends Controller
{


	public function subscriptions(Request $request) { 
		$user = User::findOrFail($request->user()->id);
		$subscriptions =  $user->subscriptions()->active()->get();


		foreach ($subscriptions as $subscription) {
			$items = $subscription->items; 

			foreach ($items as $item) {
				$product = Cashier::stripe()->products->retrieve($item->stripe_product);
				$price = Cashier::stripe()->prices->retrieve($item->stripe_price);
				
				$subscriptions_data[] = [
					'subscription_id' => $subscription->id,
					'stripe_subscription_id' => $subscription->stripe_id,
					'status' => $subscription->stripe_status,
					'product_id' => $product->id,
					'product_name' => $product->name,
					'total_feature_sponsored' => $product->metadata->total_feature_sponsored,
					'limit_feature_sponsored' => $product->metadata->limit_feature_sponsored,
					'price_id' => $price->id,
					'type' => $price->type,
					'quantity' => $item->quantity,
					'started_at' => $subscription->created_at->toDateTimeString(),
            		'ends_at' => $subscription->ends_at ? $subscription->ends_at->toDateTimeString() : null,
				];
			}
		}

		return response()->json(['data' => $subscriptions_data], 200);

	}


	public function update_subscription(Request $request) { 

		$user = User::findOrFail($request->user()->id);
		$type = $request->type;
		$subscription = collect($user->subscriptions()->active()->get())->first();
		$product = Cashier::stripe()->products->retrieve($subscription->name);


		foreach($subscription->items as $item) { 
			$subscription_data = [
				'id' => $subscription->id,
				'user_id'=> $subscription->user_id,
				'subscription_id' => $subscription->stripe_id,
				'product_id' => $subscription->name,
				'price_id' => $subscription->stripe_price,
				'status' => $subscription->stripe_status,
				'trial_ends_at' => $subscription->trial_ends_at ? $subscription->trial_ends_at->toDateTimeString() : null,
				'ends_at' => $subscription->ends_at ? $subscription->ends_at->toDateTimeString() : null,
				'created_at' => $subscription->created_at->toDateTimeString(),
				'updated_at' => $subscription->updated_at->toDateTimeString(),
				'total_feature_sponsored' => $item->total_featured_sponsor,
				'limit_feature_sponsored' => (int) $product->metadata->limit_feature_sponsored,
			];
		}

		
		$total_feature_sponsored = (int) $subscription_data['total_feature_sponsored'];
		$limit_feature_sponsored = (int) $subscription_data['limit_feature_sponsored'];

		//hey
		if($type === 'add' && $total_feature_sponsored !== $limit_feature_sponsored) {

			$updated_value = max(0, $total_feature_sponsored + 1);
			$item->update(['total_featured_sponsor' => $updated_value]);

		} else if($type === 'remove' && $total_feature_sponsored !== 0) {

			$updated_value = max(0, $total_feature_sponsored - 1);
			$item->update(['total_featured_sponsor' => $updated_value]);

		} else { 

			return response()->json(['message' => 'You already reached your limit!'], 400);

		}

		// $total_feature_sponsored;
	
		return response()->json(['message' => 'Successfully updated!'], 200);
	}

	public function subscribed(Request $request) {
		$user = User::findOrFail($request->user()->id);
		$subscription = collect($user->subscriptions()->active()->get())->first();

		$product = Cashier::stripe()->products->retrieve($subscription->name);

		foreach($subscription->items as $item) { 
			
			$subscription_data = [
				'id' => $subscription->id,
				'user_id'=> $subscription->user_id,
				'subscription_id' => $subscription->stripe_id,
				'product_id' => $subscription->name,
				'price_id' => $subscription->stripe_price,
				'status' => $subscription->stripe_status,
				'trial_ends_at' => $subscription->trial_ends_at ? $subscription->trial_ends_at->toDateTimeString() : null,
				'ends_at' => $subscription->ends_at ? $subscription->ends_at->toDateTimeString() : null,
				'created_at' => $subscription->created_at->toDateTimeString(),
				'updated_at' => $subscription->updated_at->toDateTimeString(),
				'total_feature_sponsored' => $item->total_featured_sponsor,
				'limit_feature_sponsored' => (int) $product->metadata->limit_feature_sponsored,
			];
		}


		return response()->json(['data' => $subscription_data], 200);
	}

	public function cancel_subscription(Request $request) { 

		$user = User::find($request->user()->id);
		$user->subscription($request->id)->cancel();

		return response()->json(['message' => 'Your subscription has been canceled.'], 200);
	}
}

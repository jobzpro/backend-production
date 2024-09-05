<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Laravel\Cashier\Cashier;

class SubscriptionProduct extends Controller 
{
    
	/**
	 * GET: api/subscription-products
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */

	public function index(Request $request) { 

		$products = Cashier::stripe()->products->all();

		return response()->json([
			'data' => array_map(function($product) {
				$price = Cashier::stripe()->prices->retrieve($product->default_price);
				return [
					'id' => $product->id,
					'name' => $product->name,
					'default_price' => $product->default_price,
					'active' => $product->active,
					'metadata' => $product->metadata,
					'description' => $product->description,
					'features' => $product->features,
					'unit_label' => $product->unit_label,
					'unit_amount' => $price->unit_amount,
					'currency' => $price->currency,
					'recurring' => $price->recurring->interval,
				];
			}, $products->data )
		], 200);
	}

 
	public function update(Request $request, string $id) { 

		$product = Cashier::stripe()->products->retrieve($id);
		$type = $request->type;
		$total_feature_sponsored = (int) $product->metadata['total_feature_sponsored'];
		$limit_feature_sponsored = (int) $product->metadata['limit_feature_sponsored'];

		if($type === 'add' && $total_feature_sponsored !== $limit_feature_sponsored) {

			$total = max(0, $total_feature_sponsored + 1);
			
		} else if($type === 'remove' && $total_feature_sponsored !== 0) {

			$total = max(0, $total_feature_sponsored - 1); 

		} else { 
			return response()->json(['message' => 'You already reach your limit!'], 400);
		}
			

		$product = Cashier::stripe()->products->update($id, ['metadata' => ['total_feature_sponsored' => $total]]);

		return response()->json(['message' => 'Successfully updated!'], 200);
	}

}

<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserSubscription extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'product_id',
        'product_plan_id',
        'connection_count',
        'post_count',
        'applicant_count',
        'expiry_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function product_plan(): BelongsTo
    {
        return $this->belongsTo(ProductPlan::class, 'product_plan_id');
    }

    public static function displaySubscription($id)
    {
        return self::with("product", "product_plan")->where("user_id", $id)->orderBy('created_at', 'DESC')->first();
    }

    // public static function displaySubscriptionAsEmployer($id)
    // {
    //     // $currentDate = Carbon::now()->toDateTimeString();
    //     $activeSubs = self::with("product", "product_plan")
    //         ->where("user_id", $id)
    //         ->orderBy('created_at', 'DESC')
    //         ->where('expiry_at', '>', now())
    //         ->get();
    //     // return self::with("product", "product_plan")->where("user_id", $id)->orderBy('created_at', 'DESC')->first();
    // }
}

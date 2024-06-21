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

    public static function displaySubscriptionFree($id)
    {
        return self::with("product", "product_plan")
            ->where("user_id", $id)
            // ->whereNotNull('product_id')
            // ->whereNotNull('product_plan_id')
            ->where('expiry_at', '>', Carbon::now())
            ->first();
    }

    public static function displaySubscriptionTrial($id)
    {
        return self::with("product", "product_plan")
            ->where("user_id", $id)
            ->whereNull('product_id')
            ->whereNull('product_plan_id')
            ->where('expiry_at', '>', Carbon::now())
            ->first();
    }

    public static function displaySubscription($id)
    {
        return self::with("product", "product_plan")
            ->where("user_id", $id)
            ->whereNotNull('product_id')
            ->whereNotNull('product_plan_id')
            ->where('expiry_at', '>', Carbon::now())
            ->first();
    }

    public static function displayConnectionCountTotal($id)
    {
        return self::with("product", "product_plan")
            ->where("user_id", $id)
            ->where('expiry_at', '>', Carbon::now())
            ->where('connection_count', '>', 0)
            ->sum('connection_count');
    }

    public static function displayConnectionCountTotalLimit($id)
    {
        return self::with("product", "product_plan")
            ->where("user_id", $id)
            ->where('expiry_at', '>', Carbon::now())
            ->where('connection_count', '>', 0)
            ->limit(2);
    }

    public static function displayConnectionCountTotalFirst($id)
    {
        return self::with("product", "product_plan")
            ->where("user_id", $id)
            ->where('expiry_at', '>', Carbon::now())
            ->where('connection_count', '>', 0)
            ->first();
    }

    public static function displaySubscriptionAsEmployer($id, $limit)
    {
        // $currentDate = Carbon::now()->toDateTimeString();
        return self::with("product", "product_plan")
            ->where("user_id", $id)
            ->orderBy('created_at', 'ASC')
            ->where('expiry_at', '>', now())
            ->limit($limit);
        // return self::with("product", "product_plan")->where("user_id", $id)->orderBy('created_at', 'DESC')->first();
    }
}

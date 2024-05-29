<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}

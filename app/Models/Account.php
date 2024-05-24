<?php

namespace App\Models;

use App\Http\Controllers\AdminMailerController;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class Account extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, SoftDeletes, Notifiable, HasApiTokens;

    protected $fillable = [
        'email',
        'password',
        'name',
        'login_type',
        'login_type_id',
        'is_otp'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    //public $timestamps = false;

    public function user(): HasOne
    {
        return $this->hasOne(User::class);
    }
    public function account_otp(): HasOne
    {
        return $this->hasOne(AccountOtp::class);
    }
}

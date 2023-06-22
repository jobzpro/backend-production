<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'name',
        'address_line',
        'city', 
        'state',
        'zip_code',
        'email',
        'company_email',
        'business_type_id',
        'owner_full_name',
        'introduction',
        'services',
    ];

    public function userCompany(): BelongsTo{
        return $this->belongsTo(UserCompany::class);
    }
}

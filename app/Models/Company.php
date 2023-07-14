<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        'company_logo_path',
        'owner_contact_no',
        'years_of_operation',
        'industry_id',
        'referral_code',
    ];

    public function userCompany(): HasMany{
        return $this->hasMany(UserCompany::class, 'company_id');
    }

    public function JobListings(): HasMany{
        return $this->hasMany(JobList::class);
    }

    public function businessType():  BelongsTo{
        return $this->belongsTo(BusinessType::class, 'business_type_id');
    }

    public function industry(): BelongsTo{
        return $this->belongsTo(Industry::class, 'industry_id');
    }



}

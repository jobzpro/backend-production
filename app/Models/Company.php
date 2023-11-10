<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'status',
        'business_capacity',
    ];

    public function userCompany(): HasMany
    {
        return $this->hasMany(UserCompany::class, 'company_id');
    }

    public function jobListings(): HasMany
    {
        return $this->hasMany(JobList::class)->with('company', 'industry', 'job_location', 'job_types.type', 'job_benefits.benefits', 'qualifications', 'job_specialities.industrySpeciality', 'jobListDealbreakers', 'jobStandardShifts', 'jobWeeklySchedules', 'jobSupplementalSchedules');
    }

    public function businessType(): BelongsTo
    {
        return $this->belongsTo(BusinessType::class, 'business_type_id');
    }

    public function industry(): BelongsTo
    {
        return $this->belongsTo(Industry::class, 'industry_id');
    }

    public function dealbreakers(): HasMany
    {
        return $this->hasMany(Dealbreaker::class);
    }

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function reportedEntities()
    {
        return Report::with('reportable')->where('reporter_id', $this->id)->get();
    }

    // public function createReportForJobSeeker(User $user)
    // {
    //     $report = new Report(['reportable_id' => $user->id, 'reportable_type' => 'App\Models\User']);
    //     $this->reports()->save($report);
    // }

    // public function favorites()
    // {
    //     return $this->morphMany('App\Models\Favorite', 'favoritable');
    // }

    public function favoritedBy()
    {
        return $this->morphedByMany(User::class, 'favorites', 'favoritable', 'favoritable_id', 'favoritable_id');
    }
    // public function favoritedBy()
    // {
    //     return $this->morphToMany(User::class, 'favoritable')->wherePivot('favoritable_type', User::class);
    // }
    public function favorites()
    {
        return $this->morphMany('App\Models\Favorite', 'favoriter');
    }

    public function favoritedJobseekers()
    {
        return Favorite::with('favoriter', 'favoritable')->where('favoritable_id', $this->id)->get();
        // return $this->morphedByMany('App\Models\User', 'favoritable', 'favorites', 'favoriter_id', 'favoritable_id');
    }

    public function notifications()
    {
        return $this->morphMany('App\Models\Notification', 'notifiable');
    }

    public function companyReviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class)->with('user')->with('company');
    }
}

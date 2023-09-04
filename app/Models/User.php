<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;


class User extends Authenticatable
{
    use HasFactory, HasApiTokens, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'first_name',
        'last_name',
        'middle_name',
        'phone_number',
        'profession',
        'avatar_path',
        'address_line',
        'city',
        'province',
        'elementary_school',
        'high_school',
        'college',
        'description',
        'certifications',
        'skills',
        'experience_level',
        'incorrect_signin_attempts',
        'gender',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = [
        'profile_completion'
    ];


    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function references(): HasMany
    {
        return $this->hasMany(UserReference::class);
    }

    public function userRoles(): HasMany
    {
        return $this->hasMany(UserRole::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(FileAttachment::class);
    }

    public function certifications()
    {
        return $this->files()->where('is_certification', true);
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(UserExperience::class);
    }

    public function userCompanies(): HasMany
    {
        return $this->hasMany(UserCompany::class, 'user_id');
    }

    // public function company(): HasMany{
    //     return $this->hasMany();
    // }

    public function user_notifications(): HasMany
    {
        return $this->hasMany(UserNotification::class);
    }

    public function educational_attainments(): HasMany
    {
        return $this->hasMany(EducationalAttainment::class);
    }

    public function getProfileCompletionAttribute()
    {
        $headline = $this->description;
        $certifications = $this->certifications();
        $skills = $this->skills;
        $experiences = $this->experiences();
        $references = $this->references();

        $ctr = 0;

        if ($headline !== null && $headline !== "") {
            $ctr += 1;
        }

        if ($certifications->count() > 0) {
            $ctr += 1;
        }

        if ($skills !== null && $skills !== "") {
            $ctr += 1;
        }

        if ($experiences->count() > 0) {
            $ctr += 1;
        }

        if ($references->count() > 0) {
            $ctr += 1;
        }

        $avg = ($ctr / 5) * 100;
        return $avg;
    }
}

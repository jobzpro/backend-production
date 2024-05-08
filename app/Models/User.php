<?php

namespace App\Models;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
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
        'profile_completion'
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

    /**
     * Send the email verification notification.
     *
     * @return void
     */
    // public function sendEmailVerificationNotification()
    // {
    //     $this->notify(new \App\Core\Auth\VerifyEmail);
    // }

    // protected $appends = [
    //     'profile_completion'
    // ];


    public function account(): BelongsTo
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function references(): HasMany
    {
        return $this->hasMany(UserReference::class);
    }

    public function userRoles(): HasOne
    {
        return $this->hasOne(UserRole::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(FileAttachment::class);
    }

    public function certifications()
    {
        return $this->files()->where('is_certification', true);
    }

    public function currentExperience()
    {
        return $this->hasMany(UserExperience::class)->where('current', 'true');
    }

    public function experiences(): HasMany
    {
        return $this->hasMany(UserExperience::class);
    }

    public function userCompanies(): HasMany
    {
        return $this->hasMany(UserCompany::class, 'user_id')->with('companies');
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

    public function reports()
    {
        return $this->morphMany(Report::class, 'reportable');
    }

    public function reportedEntities()
    {
        return Report::with('reportable')->where('reporter_id', $this->id)->get();
    }

    // public function favorites()
    // {
    //     return $this->morphMany('App\Models\Favorite', 'favoritable');
    // }

    // public function favoritedBy()
    // {
    //     return $this->morphToMany('App\Models\Company', 'favoritable');
    // }
    public function favorites()
    {
        return $this->morphMany('App\Models\Favorite', 'favoriter');
    }

    public function favoritedJobListings($orderBy)
    {

        // return Favorite::with(['favoriter', 'favoritable' => function (MorphTo $morphTo) {
        //     $morphTo->morphWith([
        //         JobList::class => ['company', 'industry'],
        //     ]);
        // }])->where('favoriter_type', 'App\Models\User')
        //     ->where('favoriter_id', $this->id)
        //     ->orderBy('created_at', $orderBy)
        //     ->get();

        return Favorite::with(['favoriter', 'favoritable' => function ($query) {
            $query->withTrashed();
        }])
            ->where('favoriter_type', 'App\Models\User')
            ->where('favoriter_id', $this->id)
            ->whereHasMorph('favoritable', [JobList::class], function ($query) {
                $query->with('company', 'industry')->whereNull('deleted_at');
            })
            ->orderBy('created_at', $orderBy)
            ->get();
    }

    public function notifications()
    {
        return $this->morphMany('App\Models\Notification', 'notifiable');
    }

    public function companyReviews(): HasMany
    {
        return $this->hasMany(CompanyReview::class)->with('company');
    }

    public function appReviews(): HasMany
    {
        return $this->hasMany(AppReview::class);
    }

    public function jobApplications(): HasMany
    {
        return $this->hasMany(JobApplication::class);
    }

    public function user_experience(): HasMany
    {
        return $this->hasMany(UserExperience::class);
    }

    public function followers()
    {
        return $this->hasMany(Follower::class, 'following_id', 'id');
    }

    public function following()
    {
        return $this->hasMany(Follower::class, 'user_id', 'id');
    }

    public function unfollow($following_id)
    {
        return $this->following()->where('user_id', $this->id)->where('following_id', $following_id)->forceDelete();
    }

    public function follow($following_id)
    {
        if (!$this->isFollowing($following_id)) {
            $this->following()->create([
                'user_id' => $this->id,
                'following_id' => $following_id
            ]);
        }
    }

    public function isFollowing($user_id)
    {
        return $this->following()->where('following_id', $user_id)->exists();
    }
}

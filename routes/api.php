
<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\AccountOtpController;
use App\Http\Controllers\FollowerController;
use App\Http\Controllers\AppReviewController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyReviewController;
use App\Http\Controllers\DealbreakerController;
use App\Http\Controllers\ExperienceLevelController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobListController;
use App\Http\Controllers\JobInterviewController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\IndustryPhysicalSettingsController;
use App\Http\Controllers\IndustrySpecialitiesController;
use App\Http\Controllers\JobBenefitsController;
use App\Http\Controllers\JobListDealbreakerController;
use App\Http\Controllers\JobShiftController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\QualificationsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\JobStatusDataController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SubscriptionProduct;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\SubscriptionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware(['guest'])->group(function () {
    Route::get('/get_job_status_data', [JobStatusDataController::class, 'get_job_status_data']);
    Route::get('/get_trending_categories', [JobStatusDataController::class, 'get_trending_categories']);
    Route::get('/get_top_rated_companies', [JobStatusDataController::class, 'get_top_rated_companies']);
    Route::get('/get_users_review', [JobStatusDataController::class, 'get_users_review']);
});



Route::prefix('auth')->controller(AccountController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::get('/logout', 'logout')->middleware(['auth:api']);
    Route::post('/deactivate', 'accountDeactivation')->middleware(['auth:api']);


    Route::prefix('/google')->group(function () {
        Route::get('/redirect', 'redirectToGoogle');
        Route::post('/callback', 'handleGoogleCallback');
    });

    Route::prefix('/apple')->group(function () {
        Route::get('/redirect', 'redirectToApple');
        Route::get('/callback', 'handleAppleCallback');
    });

    Route::prefix('/facebook')->group(function () {
        Route::get('/redirect', 'redirectToFacebook');
        Route::post('/callback', 'handleFacebookCallback');
    });

    Route::prefix('/linkedin')->group(function () {
        Route::get('/redirect', 'redirectToLinkedIn');
        Route::post('/callback', 'handleLinkedInCallback');
    });

    Route::post('/forget-password', 'resetPasswordRequest')->middleware(['guest'])->name('password.email');
    Route::post('/forget-password-admin', 'resetPasswordRequestAsEmployer')->middleware(['guest'])->name('password.email');
    Route::post('/password-reset', 'resetPassword')->middleware(['guest'])->name('password.reset');
    Route::post('/current-user-password-reset', 'userResetPassword')->middleware(['auth:api'])->name('current-user-password.reset');

    Route::prefix('/employer')->group(function () {
        Route::post('/register', 'signUpAsAnEmployeer');
        Route::post('/login', 'signInAsEmployeer');
        Route::post('/sign-up', 'signUpAsEmployerStaffViaInvite');
        Route::post('/check-if-company-exists', 'checkifCompanyExists');
    });
});

Route::prefix('/email/verify')->controller(VerifyEmailController::class)->group(function () {
    Route::get('/{id}/{hash}', '__invoke')->middleware(['signed', 'throttle:6.1'])->name('verification.verify');
    Route::post('/resend', 'resendEmail')->middleware(['auth:api'])->name('verification.send');
    Route::get('/success', 'successVerified');
    Route::get('/already-success', 'alreadyVerified');
});

Route::middleware(['auth:api'])->group(function () {
    Route::prefix('/jobseeker')->controller(UserController::class)->group(function () {
        Route::get('/{id}/profile', 'showJobseekerProfile');
        Route::patch('/{id}/profile/update', 'updateJobseekerProfile');
        Route::patch('/{id}/profile/references/update', 'updateReferences');
        Route::patch('/{id}/profile/experiences/update', 'updateExperiences');
        Route::patch('/{id}/profile/educational_attainments/update', 'updateEducationalAttainments');
        Route::post('/{id}/profile/certifications/update', 'updateCertifications');
        Route::delete('/{id}/profile/certifications/delete', 'deleteCertificate');

        Route::prefix('{id}/2FA')->controller(AccountOtpController::class)->group(function () {
            Route::get('/get-code', 'create2fa');
            Route::post('/verify-code', 'verfiy2fa');
            Route::post('/otp-set', 'otpToggle');
        });

        Route::prefix('/{id}/reports')->controller(ReportController::class)->group(function () {
            Route::get('/', 'userReports');
            Route::post('/', 'reportCompanyOrJobList');
        });

        Route::prefix('/{id}/favorites')->controller(FavoriteController::class)->group(function () {
            Route::get('/', 'userFavorites');
            Route::get('/all', 'allUserFavorites');
            Route::post('/', 'addUserFavorites');
        });

        Route::prefix('/{id}/notifications')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'jobSeekerNotifications');
            Route::post('/read-all', 'readAllJobSeekerNotifications');
        });

        Route::prefix('/{id}/reviews')->controller(CompanyReviewController::class)->group(function () {
            Route::get('/', 'reviewsOfJobseeker');
            Route::delete('/{review_id}', 'deleteReview');
        });

        Route::prefix('/{id}/app-reviews')->controller(AppReviewController::class)->group(function () {
            Route::post('/', 'addReview');
        });

        Route::prefix('{id}/followers')->controller(FollowerController::class)->group(function () {
            Route::post('/follow', 'follow');
            Route::get('/all', 'allUser');
            Route::post('/add-friend', 'addFriend');
            Route::post('/decline-friend', 'declineFriend');
            Route::get('/follow-checker/{following_id}', 'isFollowChecker');
            Route::get('/follow-list', 'followUsers');
        });
    });

    Route::prefix('/company/{id}')->group(function () {
        Route::prefix('/2FA')->controller(AccountOtpController::class)->group(function () {
            Route::get('/get-code', 'create2fa');
            Route::post('/verify-code', 'verfiy2fa');
            Route::post('/otp-set', 'otpToggle');
            Route::get('/account-display', 'showAccountInformation');
        });
        #all job listing routes
        Route::prefix('/jobs')->controller(JobListController::class)->group(function () {
            Route::get('/', 'getJobListings');
            Route::get('/{job_list_id}/edit', 'edit');
            Route::post('/post-job', 'store');
            Route::get('/{job_list_id}/applicants', 'getAllApplicantsForJobList');
            Route::patch('/{job_list_id}/archived', 'archiveJobList');
            Route::patch('{job_list_id}/publish', 'publishJobList');
            Route::get('/list-all-applicants', 'getAllApplicants');
            Route::post('/save-as-draft', 'saveJobListAsDraft');
            Route::patch('/{job_list_id}/update', 'update');
            Route::delete('/{job_list_id}/destroy', 'destroy');
        });

        #all company settings and updates
        Route::post('/send-invite', [CompanyController::class, 'sendStaffinvite']);
        Route::patch('/updateBasic', [CompanyController::class, 'updateBasicDetails']);
        Route::patch('/update-basic-information', [CompanyController::class, 'updateCompanyBasicDetailSettings']);
        Route::post('/upload-company-logo', [CompanyController::class, 'uploadCompanyLogo']);
        Route::post('/updateAdmin', [CompanyController::class, 'updateAdminDetails']);
        Route::patch('/update', [CompanyController::class, 'updateCompanyDetails']);
        Route::get('/staffs', [CompanyController::class, 'displayStaff']);
        Route::post('/staffs/add', [CompanyController::class, 'addEmployerStaff']);
        Route::post('/staffs/deactivate', [CompanyController::class, 'accountDeactivation']);
        Route::post('/staffs/activate', [CompanyController::class, 'accountReactivation']);

        Route::prefix('/applications')->controller(JobApplicationController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{job_application_id}', 'show');
            Route::post('/{job_application_id}', 'setStatus');
            Route::delete('/{job_application_id}', 'delete');
        });

        Route::prefix('/dealbreakers')->controller(DealbreakerController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/add', 'store');
            Route::post('/answer/{job_list_id}', 'manageDealbreakerAnswerAsCompany');
            Route::delete('/{job_list_id}/remove-answer', 'removeDealbreakerAnswerAsCompany');
            // Route::post('/edit-answer/{job_list_id}', 'editDealbreakerAnswerAsCompany');
            Route::get('/{dealbreaker_id}', 'getDealbreaker');
            Route::post('/{dealbreaker_id}/edit', 'editDealbreakers');
            Route::post('/{dealbreaker_id}/edit-choices', 'editDealbreakerChoices');
            Route::post('/soft-delete/{job_list_id}', 'softDeleteDealbreakerAnswer');
            Route::delete('/permanent-delete-dealbreaker/{dealbreaker_id}', 'deleteDealbreakers');
            Route::delete('/permanent-delete-dealbreaker-choices/{dealbreaker_id}', 'deleteDealbreakerChoices');
        });

        Route::prefix('/job-list-dealbreakers')->controller(JobListDealbreakerController::class)->group(function () {
            Route::delete('/reset/{job_list_id}', 'softDeleteDealbreakerAnswer');
        });

        Route::prefix('/reports')->controller(ReportController::class)->group(function () {
            Route::get('/', 'companyReports');
            Route::post('/', 'reportJobSeeker');
        });

        Route::prefix('/favorites')->controller(FavoriteController::class)->group(function () {
            Route::get('/', 'companyFavorites');
            Route::post('/', 'addCompanyFavorites');
        });

        Route::prefix('/notifications')->controller(NotificationController::class)->group(function () {
            Route::get('/', 'companyNotifications');
            Route::post('/read-all', 'readAllCompanyNotifications');
        });

        Route::prefix('/reviews')->controller(CompanyReviewController::class)->group(function () {
            Route::get('/', 'reviewsForCompany');
            Route::post('/', 'postAReview');
            Route::post('/{review_id}/pin', 'pinReview');
        });
    });

    Route::prefix('/job')->controller(JobApplicationController::class)->group(function () {
        Route::post('/{id}/apply', 'store');
        Route::post('/{id}/retract', 'retractApplication');
    });

    Route::prefix('/interview')->controller(JobInterviewController::class)->group(function () {
        Route::post('/{job_appication_id}/set-interview', 'store');
        Route::get('/all', 'index');
        Route::post('/{interview_id}/{job_application_id}/set-status', 'setStatus');
        Route::post('/{interview_id}/reschedule', 'reschedule');
        Route::get('/search', 'search');
        Route::get('/{id}', 'show');
    });

    Route::prefix('/reports')->controller(ReportController::class)->group(function () {
        Route::delete('/{id}', 'delete');
        Route::get('/{id}', 'show');
        Route::post('/{id}/set-status', 'setStatus');
    });

    Route::prefix('/favorites')->controller(FavoriteController::class)->group(function () {
        Route::delete('/{id}', 'delete');
        Route::get('/{id}', 'show');
    });

    Route::prefix('/resumes')->controller(UserController::class)->group(function () {
        Route::get('/search', 'searchResumes');
    });
    // Route::resource('job-application-history', JobApplicationController::class);

    Route::prefix('/job-application-history')->controller(JobApplicationController::class)->group(function () {
        Route::get('/list', 'jobApplicationHistory');
    });

    Route::prefix('/job-interview-applications')->controller(JobInterviewController::class)->group(function () {
        Route::get('/list', 'getUserInterviews');
    });


    // Route::get('/job-application-history/list', [JobApplicationController::class, 'jobApplicationHistory']);
});

Route::apiResources([
    'company' => CompanyController::class,
    // 'products' => ProductController::class,
    'experience-levels' => ExperienceLevelController::class,
    'jobs' => JobListController::class,
    'industry' => IndustryController::class,
    'business-type' => BusinessTypeController::class,
    'types' => TypeController::class,
    'industry-specialities' => IndustrySpecialitiesController::class,
    'industry-physical-settings' => IndustryPhysicalSettingsController::class,
    'benefits' => JobBenefitsController::class,
    'qualifications' => QualificationsController::class,
    'app-reviews' => AppReviewController::class,
    '{id}/dealbreakers' => DealbreakerController::class,
], ['only' => ['index', 'show']]);



Route::prefix('/products')->controller(ProductController::class)->group(function () {
    Route::get('/', 'index');
    Route::get('/jobseeker-subscription/{id}', 'jobseekerSubscription');
    Route::get('/employer-subscription/{id}', 'employerSubscription'); 
    Route::get('/get-subscription/{id}', 'getSubscription');	
    Route::get('/get-subscription-employer/{id}', 'getSubscriptionEmployer');
    Route::post('/insert-subscription', 'insertSubscription');
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::get('/user', function (Request $request) {
	return $request->user();
});	

Route::apiResource('subscription-products', SubscriptionProduct::class);
Route::get('subscription-checkout', CheckoutController::class)->middleware(['auth:api']);
Route::post('subscriptions/{id}', [SubscriptionController::class, 'cancel_subscription'])->middleware(['auth:api']);
Route::post('subscription', [SubscriptionController::class, 'update_subscription'])->middleware(['auth:api']);

Route::get('subscribed', [SubscriptionController::class, 'subscribed'])->middleware(['auth:api']);
Route::get('subscriptions', [SubscriptionController::class, 'subscriptions'] )->middleware(['auth:api']);
Route::get('subscription', [SubscriptionController::class, 'subscription'] )->middleware(['auth:api']);

Route::prefix('/search')->controller(JobListController::class)->group(function () {
    Route::get('/jobs', 'searchJobs')->name('jobs.search');
});

Route::prefix('/shifts')->controller(JobShiftController::class)->group(function () {
    Route::get('/standard', 'getStandardShifts')->name('jobs.standard');
    Route::get('/weekly', 'getWeeklyShifts')->name('jobs.weekly');
    Route::get('/supplemental', 'getSupplementalShifts')->name('jobs.supplemental');
});

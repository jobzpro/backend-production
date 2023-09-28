
<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\BusinessTypeController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\DealbreakerController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\JobApplicationController;
use App\Http\Controllers\JobListController;
use App\Http\Controllers\JobInterviewController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\IndustryPhysicalSettingsController;
use App\Http\Controllers\IndustrySpecialitiesController;
use App\Http\Controllers\JobBenefitsController;
use App\Http\Controllers\JobShiftController;
use App\Http\Controllers\QualificationsController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TypeController;
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

Route::prefix('auth')->controller(AccountController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::get('/logout', 'logout')->middleware(['auth:api']);


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
    Route::post('/password-reset', 'resetPassword')->middleware(['guest'])->name('password.reset');

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
        Route::patch('/{id}/profile/certifications/update', 'updateCertifications');

        Route::prefix('/{id}/reports')->controller(ReportController::class)->group(function () {
            Route::get('/', 'userReports');
            Route::post('/', 'reportCompanyOrJobList');
        });

        Route::prefix('/{id}/favorites')->controller(FavoriteController::class)->group(function () {
            Route::get('/', 'userFavorites');
            Route::post('/', 'addUserFavorites');
        });
    });

    Route::prefix('/company/{id}')->group(function () {
        #all job listing routes
        Route::prefix('/jobs')->controller(JobListController::class)->group(function () {
            Route::get('/', 'getJobListings');
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
        Route::post('/updateAdmin', [CompanyController::class, 'updateAdminDetails']);
        Route::patch('/update', [CompanyController::class, 'updateCompanyDetails']);
        Route::post('/staffs/add', [CompanyController::class, 'addEmployerStaff']);

        Route::prefix('/applications')->controller(JobApplicationController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/{job_application_id}', 'show');
            Route::post('/{job_application_id}', 'setStatus');
            Route::delete('/{job_application_id}', 'delete');
        });

        Route::prefix('/dealbreakers')->controller(DealbreakerController::class)->group(function () {
            Route::get('/', 'index');
            Route::post('/add', 'store');
        });

        Route::prefix('/reports')->controller(ReportController::class)->group(function () {
            Route::get('/', 'companyReports');
            Route::post('/', 'reportJobSeeker');
        });

        Route::prefix('/favorites')->controller(FavoriteController::class)->group(function () {
            Route::get('/', 'companyFavorites');
            Route::post('/', 'addCompanyFavorites');
        });
    });

    Route::prefix('/job')->controller(JobApplicationController::class)->group(function () {
        Route::post('/{id}/apply', 'store');
        Route::post('/{id}/retract', 'retractApplication');
    });

    Route::prefix('/interview')->controller(JobInterviewController::class)->group(function () {
        Route::post('/set-interview', 'store');
        Route::get('/all', 'index');
        Route::post('/set-status', 'setStatus');
        Route::post('/reschedule', 'reschedule');
        Route::get('/search', 'search');
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
});

Route::apiResources([
    'company' => CompanyController::class,
    'jobs' => JobListController::class,
    'industry' => IndustryController::class,
    'business-type' => BusinessTypeController::class,
    'types' => TypeController::class,
    'industry-specialities' => IndustrySpecialitiesController::class,
    'industry-physical-settings' => IndustryPhysicalSettingsController::class,
    'benefits' => JobBenefitsController::class,
    'qualifications' => QualificationsController::class,
], ['only' => ['index', 'show']]);

Route::prefix('/search')->controller(JobListController::class)->group(function () {
    Route::get('/jobs', 'searchJobs')->name('jobs.search');
});

Route::prefix('/shifts')->controller(JobShiftController::class)->group(function () {
    Route::get('/standard', 'getStandardShifts')->name('jobs.standard');
    Route::get('/weekly', 'getWeeklyShifts')->name('jobs.weekly');
    Route::get('/supplemental', 'getSupplementalShifts')->name('jobs.supplemental');
});

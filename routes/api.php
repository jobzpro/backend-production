<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\VerifyEmailController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\JobListController;

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

Route::prefix('auth')->controller(AccountController::class)->group(function(){
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::get('/logout', 'logout')->middleware(['auth:api']);


    Route::prefix('/google')->group(function(){
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
    Route::get('/password-reset/{token}','resetPasswordView');
    Route::post('/password-reset', 'resetPassword')->middleware(['guest'])->name('password.reset');

    Route::prefix('/employer')->group(function(){
        Route::post('/register', 'signUpAsAnEmployeer');
        Route::post('/login', 'signInAsEmployeer');

    });

});

Route::prefix('/email/verify')->controller(VerifyEmailController::class)->group(function(){
    Route::get('/{id}/{hash}', '__invoke')->middleware(['signed','throttle:6.1'])->name('verification.verify');
    Route::post('/resend', 'resendEmail')->middleware(['auth:api'])->name('verification.send');
    Route::get('/success', 'successVerified');
    Route::get('/already-success', 'alreadyVerified');
});

Route::middleware(['auth:api'])->group(function(){
    Route::prefix('/jobseeker')->controller(UserController::class)->group(function(){
        Route::get('/{id}/profile', 'showJobseekerProfile');
        Route::patch('/{id}/profile/update', 'updateJobseekerProfile');
        Route::patch('/{id}/profile/references/update', 'updateReferences');
        Route::patch('/{id}/profile/experiences/update', 'updateExperiences');
    });

    Route::prefix('/company/jobs')->controller(JobListController::class)->group(function(){
        Route::post('/post-job', 'store');
    });

});

Route::apiResources([
    'company' => CompanyController::class,
    'jobs' => JobListController::class,
],['only' => ['index', 'show']]);
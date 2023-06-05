<?php

use App\Http\Controllers\AccountController;
use App\Models\Account;
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

Route::prefix('auth')->group(function(){
    Route::post('/register', [AccountController::class, 'register']);
    Route::post('/login', [AccountController::class, 'login']);
    Route::get('/logout', [AccountController::class, 'logout'])->middleware('auth:api');


    Route::prefix('/google')->group(function(){
        Route::get('/redirect', [AccountController::class, 'redirectToGoogle']);
        Route::get('/callback', [AccountController::class, 'handleGoogleCallback']);
    });
});

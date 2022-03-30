<?php

use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\RecoveryController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VerificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*
|--------------------------------------------------------------------------
| PUBLIC
|--------------------------------------------------------------------------
 */
Route::middleware('guest')->group(function () {
    Route::post('/login', [LoginController::class, 'store']);
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::put('/verify/{verification_token:token}', [VerificationController::class, 'update']);

/*
|--------------------------------------------------------------------------
| PRIVATE
|--------------------------------------------------------------------------
 */
Route::middleware('auth:sanctum')->group(function () {
    Route::apiResource('users', UserController::class)
        ->except('store');
    Route::put('/users/{user:uuid}/reset-pwd', [RecoveryController::class, 'update']);
    Route::post('/logout', [LogoutController::class, 'destroy']);
});

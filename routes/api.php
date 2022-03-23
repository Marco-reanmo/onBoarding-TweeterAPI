<?php

use App\Http\Controllers\SessionController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::controller(SessionController::class)->group(function() {
    Route::post('/users/login', 'store');
    Route::post('/users/logout', 'destroy');
});

Route::controller(UserController::class)->group(function () {
    Route::get('/users', 'index');
    Route::post('/users/register', 'store');
});

Route::controller(VerificationController::class)->group(function () {
    Route::put('/users/{verification_token:token}/verify', 'update');
});

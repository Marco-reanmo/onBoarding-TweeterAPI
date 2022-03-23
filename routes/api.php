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

Route::post('/users/login', [SessionController::class, 'store']);
Route::post('/users/logout', [SessionController::class, 'destroy']);

Route::post('/users/register', [UserController::class, 'store']);

Route::get('/users', [UserController::class, 'index']);

Route::get('/users/verify', [VerificationController::class, 'index']);
Route::put('/users/{verification_token:token}/verify', [VerificationController::class, 'update']);

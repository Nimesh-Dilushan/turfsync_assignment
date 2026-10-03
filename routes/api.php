<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookingApiController;

// Public Endpoints
Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/facilities', [BookingApiController::class, 'facilities']);
    Route::get('/weather-check', [BookingApiController::class, 'weatherCheck']);

    // Sanctum Authenticated Endpoints
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/my-bookings', [BookingApiController::class, 'myBookings']);
        Route::post('/bookings', [BookingApiController::class, 'store']);
    });
});
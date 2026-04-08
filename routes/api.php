<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Traveler\SearchController;
use App\Http\Controllers\Api\Traveler\BookingController;
use App\Http\Controllers\Api\Traveler\WalletController;
use App\Http\Controllers\Api\Approver\DashboardController;
use App\Http\Controllers\Api\Approver\RankingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned the "api" middleware group.
|
*/

// Auth routes
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::middleware('auth:sanctum')->get('/user', [AuthController::class, 'user']);

// Traveler routes (role: traveler)
Route::middleware(['auth:sanctum', 'role:traveler'])->prefix('traveler')->group(function () {
    Route::get('/search', [SearchController::class, 'index']);
    Route::post('/bookings', [BookingController::class, 'store']);
    Route::get('/wallet', [WalletController::class, 'show']);
    Route::get('/history', [BookingController::class, 'history']);
});

// Approver routes (role: approver)
Route::middleware(['auth:sanctum', 'role:approver'])->prefix('approver')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/ranking', [RankingController::class, 'index']);
});

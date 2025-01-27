<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\v1\BookKeepingController;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);

    // Route untuk admin
    Route::middleware('role:owner')->group(function () {
        // Tambahkan route admin disini
        Route::resources(['book-keepings' => BookKeepingController::class]);
    });

    // Route untuk user
    Route::middleware('role:user')->group(function () {
        // Tambahkan route user disini
    });
});

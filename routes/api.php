<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\BookingController;
use App\Http\Controllers\Api\V1\CustomerController;
use App\Http\Controllers\Api\V1\InspectionController;
use App\Http\Controllers\Api\V1\InvoiceController;
use App\Http\Controllers\Api\V1\JobController;
use App\Http\Controllers\Api\V1\ServiceController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/me', [AuthController::class, 'me']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::middleware('module:crm')->apiResource('customers', CustomerController::class);
        Route::middleware('module:catalog')->apiResource('services', ServiceController::class)->only(['index', 'show']);
        Route::middleware('module:bookings')->apiResource('bookings', BookingController::class);
        Route::middleware('module:jobs')->apiResource('jobs', JobController::class)->only(['index', 'show', 'update']);
        Route::middleware('module:quality')->apiResource('inspections', InspectionController::class)->only(['index', 'show', 'store']);
        Route::middleware('module:invoicing')->apiResource('invoices', InvoiceController::class)->only(['index', 'show']);
    });
});

<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\FindingController;
use App\Http\Controllers\Api\LookupController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/select-role', [AuthController::class, 'setRole']);
    Route::post('/fcm-token', [AuthController::class, 'updateFcmToken']);

    Route::get('/profile', [ProfileController::class, 'show']);
    Route::patch('/profile', [ProfileController::class, 'update']);

    Route::get('/categories', [LookupController::class, 'categories']);
    Route::get('/departments', [LookupController::class, 'departments']);

    Route::get('/findings/summary', [FindingController::class, 'summary']);
    Route::get('/findings', [FindingController::class, 'index']);
    Route::get('/findings/{finding}', [FindingController::class, 'show']);

    // Auditor
    Route::middleware('active.role.api:auditor')->group(function () {
        Route::post('/findings', [FindingController::class, 'store']);
        Route::post('/findings/{finding}/verify', [FindingController::class, 'verify']);
    });

    // Auditee
    Route::middleware('active.role.api:auditee')->group(function () {
        Route::post('/findings/{finding}/respond', [FindingController::class, 'respond']);
    });
});

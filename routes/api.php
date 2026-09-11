<?php

use App\Http\Controllers\Api\V1\AlertController;
use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ConfigController;
use App\Http\Controllers\Api\V1\IspController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {

    // Public
    Route::get('/get-isp', [IspController::class, 'getIsp']);
    Route::get('/alerts', [AlertController::class, 'index']);
    Route::post('/login', [AuthController::class, 'login']);

    // Authenticated
    Route::middleware('auth:sanctum')->prefix('user')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::post('/get-config', [ConfigController::class, 'getConfigs']);
    });

});

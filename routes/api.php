<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RequestController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AdditionalController;
use Illuminate\Support\Facades\Route;

$auth = 'auth:sanctum';

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware([$auth])->group(function () {
    Route::get('/users/{id}', [UserController::class, 'show']);
    Route::put('/users/{id}', [UserController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
});

Route::middleware([$auth, 'role:landlord,admin'])->group(function () {
    Route::apiResource("properties", PropertyController::class);
     Route::apiResource('requests', RequestController::class)->except(['store']);
     Route::get('/my-properties', [AdditionalController::class, 'properties']);
    });
    
    Route::middleware([$auth, 'role:user'])->group(function () {
        Route::post('/requests', [RequestController::class, 'store']);
        Route::get('/my-requests', [AdditionalController::class, 'requests']);
});


Route::middleware([$auth, 'role:admin'])->group(function () {
    Route::get('/users', [UserController::class, 'index']);
    Route::get('/dashboard', [AdditionalController::class, 'statistics']);
    Route::delete('/users/{id}', [UserController::class, 'destroy']);
});
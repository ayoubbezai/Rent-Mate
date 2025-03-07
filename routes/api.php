<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RequestController;
use Illuminate\Support\Facades\Route;

$auth = 'auth:sanctum';

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware($auth);

Route::middleware([$auth, 'role:landlord,admin'])->group(function () {
    Route::apiResource("properties", PropertyController::class);
     Route::apiResource('requests', RequestController::class)->except(['store']);

});

Route::middleware([$auth, 'role:user'])->group(function () {
    Route::post('/requests', [RequestController::class, 'store']); 
});



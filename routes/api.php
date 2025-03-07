<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;

$auth = 'auth:sanctum';

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware($auth);

Route::middleware([ $auth, 'role:admin'])->group(function () {
    Route::get('/admin-dashboard', function () {
        return response()->json(['message' => 'Welcome, Admin!']);
    });
});

Route::middleware([ $auth, 'role:landlord,admin'])->group(function () {
    Route::get('/landlord-dashboard', function () {
        return response()->json(['message' => 'Welcome, Landlord!']);
    });
    Route::apiResource("properties",PropertyController::class);
});

Route::middleware([ $auth, 'role:user'])->group(function () {
    Route::get('/user-dashboard', function () {
        return response()->json(['message' => 'Welcome, User!']);
    });
});
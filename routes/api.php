<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;

// ─── PUBLIC (tidak perlu login) ───────────────────
Route::post('/register',        [AuthController::class, 'register']);
Route::post('/login',           [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password',  [AuthController::class, 'resetPassword']);

// ─── PUBLIC PRODUCTS ──────────────────────────────
Route::get('/products',         [ProductController::class, 'index']);
Route::get('/products/{id}',    [ProductController::class, 'show']);

// ─── PROTECTED (harus login) ──────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user',             [AuthController::class, 'me']);
    Route::post('/logout',          [AuthController::class, 'logout']);

    // Cart
    Route::get('/cart',             [CartController::class, 'index']);
    Route::post('/cart',            [CartController::class, 'store']);
    Route::put('/cart/{id}',        [CartController::class, 'update']);
    Route::delete('/cart/{id}',     [CartController::class, 'destroy']);
    Route::delete('/cart',          [CartController::class, 'clear']);
});
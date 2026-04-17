<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProductController;

// ─── PUBLIC (tidak perlu login) ───────────────────
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

// ─── PUBLIC PRODUCTS ──────────────────────────────
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show'])->name('api.products.show');

Route::post('/admin/products/check-codes', function (\Illuminate\Http\Request $request) {
    $codes = $request->input('codes', []);
    $products = \App\Models\Product::whereIn('code', $codes)->get(['code', 'name']);
    $result = [];
    foreach ($products as $p) {
        $result[$p->code] = $p->name;
    }
    return response()->json($result);
});

// ─── PROTECTED (harus login) ──────────────────────
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Cart
    Route::get('/cart', [CartController::class, 'index']);
    Route::post('/cart', [CartController::class, 'store']);
    Route::put('/cart/{id}', [CartController::class, 'update']);
    Route::delete('/cart/{id}', [CartController::class, 'destroy']);
    Route::delete('/cart', [CartController::class, 'clear']);

    // Admin Auth
    Route::prefix('admin')->group(function () {
        Route::post('/login', [\App\Http\Controllers\Admin\AdminAuthController::class, 'login']);

        Route::middleware('is_admin')->group(function () {
            Route::post('/logout', [\App\Http\Controllers\Admin\AdminAuthController::class, 'logout']);
            Route::get('/me', [\App\Http\Controllers\Admin\AdminAuthController::class, 'me']);

            // Products
            Route::apiResource('products', \App\Http\Controllers\Admin\AdminProductController::class);
        });
    });
});
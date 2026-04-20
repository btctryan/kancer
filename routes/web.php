<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin;
use Inertia\Inertia;

// ─── HALAMAN UTAMA ────────────────────────────────
Route::get('/', function () {
    return Inertia::render('Home');
});

// ============================================
// ADMIN ROUTES
// ============================================
Route::prefix('admin')->name('admin.')->group(function () {

    // Auth (tanpa login)
    Route::get('/login', [Admin\AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [Admin\AuthController::class, 'login'])->name('login.post');

    // Protected (harus login admin)
    Route::middleware('admin.auth')->group(function () {
        Route::post('/logout', [Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

        // ── Route spesifik HARUS di atas resource ──
        Route::get('/products/bulk-upload', [Admin\ProductController::class, 'bulkUploadForm'])->name('products.bulk-upload');
        Route::post('/products/bulk-upload', [Admin\ProductController::class, 'bulkUpload'])->name('products.bulk-upload.post');
        Route::get('/products/bulk-update', [Admin\ProductController::class, 'bulkUpdateForm'])->name('products.bulk-update');
        Route::post('/products/bulk-update', [Admin\ProductController::class, 'bulkUpdate'])->name('products.bulk-update.post');

        // ── Resource di bawah route spesifik ──
        Route::resource('products', Admin\ProductController::class)->except(['show']);

        Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
        Route::get('/orders', [Admin\OrderController::class, 'index'])->name('orders.index');
    });
});

// ─── AUTH PAGES ───────────────────────────────────
Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/register', function () {
    return Inertia::render('Auth/Register');
})->name('register');

Route::get('/forgot-password', function () {
    return Inertia::render('Auth/ForgotPassword');
})->name('forgot-password');

Route::get('/reset-password/{token}', function (string $token) {
    return Inertia::render('Auth/ResetPassword', ['token' => $token]);
})->name('password.reset');

// ─── HALAMAN PRODUK ───────────────────────────────
Route::get('/products', function () {
    return Inertia::render('Products/Index');
})->name('products');

Route::get('/products/{id}', function ($id) {
    return Inertia::render('Products/Show', ['id' => $id]);
})->name('products.detail');

// ─── HALAMAN CART ─────────────────────────────────
Route::get('/cart', function () {
    return Inertia::render('Cart/Index');
})->name('cart');
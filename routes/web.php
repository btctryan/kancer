<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use Inertia\Inertia;

// ─── HALAMAN UTAMA ────────────────────────────────
Route::get('/', function () {
    return Inertia::render('Home');
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
})->name('products.show');

// ─── HALAMAN CART (harus login) ───────────────────
Route::get('/cart', function () {
    return Inertia::render('Cart/Index');
})->name('cart');
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Product;
use App\Models\User;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Dashboard', [
            'stats' => [
                'total_products'   => Product::count(),
                'total_users'      => User::count(),
                'total_cart_items' => Cart::count(),
                'users_with_cart'  => Cart::distinct('user_id')->count('user_id'),
                'out_of_stock'     => Product::where('stock', 0)->count(),
            ],
            'recent_products' => Product::latest()->take(5)->get(),
            'recent_users'    => User::latest()->take(5)->get(),
        ]);
    }
}
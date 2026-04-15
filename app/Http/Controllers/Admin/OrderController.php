<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Inertia\Inertia;

class OrderController extends Controller
{
    public function index()
    {
        // Tampilkan user yang punya item di keranjang sebagai "pesanan"
        $orders = User::has('carts')
            ->with(['carts.product'])
            ->latest()
            ->paginate(10);

        return Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
        ]);
    }
}
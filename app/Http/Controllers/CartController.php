<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // ─── AMBIL SEMUA ISI CART ────────────────────────
    public function index(Request $request)
    {
        $cartItems = Cart::with('product')
            ->where('user_id', $request->user()->id)
            ->get()
            ->map(function ($item) {
                return [
                    'id'         => $item->id,
                    'quantity'   => $item->quantity,
                    'product'    => [
                        'id'    => $item->product->id,
                        'name'  => $item->product->name,
                        'price' => $item->product->price,
                        'image' => $item->product->image,
                        'badge' => $item->product->badge,
                        'stock' => $item->product->stock,
                    ],
                    'subtotal' => $item->product->price * $item->quantity,
                ];
            });

        $total = $cartItems->sum('subtotal');

        return response()->json([
            'items' => $cartItems,
            'total' => $total,
            'count' => $cartItems->sum('quantity'),
        ]);
    }

    // ─── TAMBAH PRODUK KE CART ───────────────────────
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity'   => 'integer|min:1',
        ], [
            'product_id.required' => 'Produk wajib dipilih.',
            'product_id.exists'   => 'Produk tidak ditemukan.',
            'quantity.min'        => 'Jumlah minimal 1.',
        ]);

        $product = Product::findOrFail($request->product_id);

        // Cek stok
        if ($product->stock < 1) {
            return response()->json([
                'message' => 'Stok produk habis.',
            ], 422);
        }

        // Cek apakah produk sudah ada di cart
        $cartItem = Cart::where('user_id', $request->user()->id)
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // Update quantity
            $newQty = $cartItem->quantity + ($request->quantity ?? 1);

            // Cek stok tidak melebihi
            if ($newQty > $product->stock) {
                return response()->json([
                    'message' => 'Jumlah melebihi stok tersedia (' . $product->stock . ').',
                ], 422);
            }

            $cartItem->update(['quantity' => $newQty]);
        } else {
            // Tambah baru
            $cartItem = Cart::create([
                'user_id'    => $request->user()->id,
                'product_id' => $request->product_id,
                'quantity'   => $request->quantity ?? 1,
            ]);
        }

        return response()->json([
            'message' => 'Produk ditambahkan ke keranjang.',
            'item'    => $cartItem,
        ], 201);
    }

    // ─── UPDATE QUANTITY ─────────────────────────────
    public function update(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
        ], [
            'quantity.required' => 'Jumlah wajib diisi.',
            'quantity.min'      => 'Jumlah minimal 1.',
        ]);

        $cartItem = Cart::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        // Cek stok
        if ($request->quantity > $cartItem->product->stock) {
            return response()->json([
                'message' => 'Jumlah melebihi stok tersedia.',
            ], 422);
        }

        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json([
            'message' => 'Keranjang diperbarui.',
            'item'    => $cartItem,
        ]);
    }

    // ─── HAPUS SATU ITEM ─────────────────────────────
    public function destroy(Request $request, $id)
    {
        $cartItem = Cart::where('id', $id)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $cartItem->delete();

        return response()->json([
            'message' => 'Produk dihapus dari keranjang.',
        ]);
    }

    // ─── KOSONGKAN SEMUA CART ────────────────────────
    public function clear(Request $request)
    {
        Cart::where('user_id', $request->user()->id)->delete();

        return response()->json([
            'message' => 'Keranjang dikosongkan.',
        ]);
    }
}
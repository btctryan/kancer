<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // ─── AMBIL SEMUA PRODUK ───────────────────────────
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        // Filter popular (untuk home page)
        if ($request->has('popular')) {
            $query->orderBy('created_at', 'desc');
        }

        // Filter pencarian
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        // Limit
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id'          => $product->id,
                'name'        => $product->name,
                'description' => $product->description,
                'price'       => $product->price,
                'image'       => $product->image,
                'badge'       => $product->badge,
                'stock'       => $product->stock,
                'weight'      => $product->weight,
            ];
        });

        return response()->json($products);
    }

    // ─── AMBIL SATU PRODUK ────────────────────────────
    public function show($id)
    {
        $product = Product::where('is_active', true)->findOrFail($id);

        return response()->json([
            'id'          => $product->id,
            'name'        => $product->name,
            'description' => $product->description,
            'price'       => $product->price,
            'image'       => $product->image,
            'badge'       => $product->badge,
            'stock'       => $product->stock,
            'weight'      => $product->weight,
        ]);
    }
}
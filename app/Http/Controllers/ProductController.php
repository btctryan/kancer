<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // ← tambah ini

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('is_active', true);

        if ($request->has('popular')) {
            $query->orderBy('created_at', 'desc');
        }
        if ($request->has('search')) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }
        if ($request->has('limit')) {
            $query->limit($request->limit);
        }

        $products = $query->get()->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'price' => $product->price,
                'image' => $product->image ? asset($product->image) : null,
                'badge' => $product->badge,
                'stock' => $product->stock,
                'weight' => $product->weight,
            ];
        });

        return response()->json($products);
    }

    public function show($id)
    {
        $product = Product::where('is_active', true)->findOrFail($id);

        return response()->json([
            'id' => $product->id,
            'name' => $product->name,
            'description' => $product->description,
            'price' => $product->price,
            'image' => $product->image ? asset($product->image) : null,
            'badge' => $product->badge,
            'stock' => $product->stock,
            'weight' => $product->weight,
        ]);
    }
}
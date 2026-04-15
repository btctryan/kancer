<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ProductController extends Controller
{
    // ─── HELPER: tentukan kategori dari harga ─────────
    private function getCategory(float $price): string
    {
        if ($price < 3600000) {
            return 'Ekonomis';
        } elseif ($price >= 3720000 && $price < 4200000) {
            return 'Medium';
        } elseif ($price >= 4200000) {
            return 'Premium';
        }
        return 'Ekonomis'; // default
    }

    // ─── HELPER: generate nama produk ─────────────────
    private function generateName(string $code, float $price): string
    {
        $category = $this->getCategory($price);
        return "Domba {$category} {$code}";
    }

    // ─── HELPER: auto generate kode berikutnya ────────
    private function generateCode(): string
    {
        $last = Product::orderBy('id', 'desc')->first();
        if (!$last) {
            return 'KNCR001';
        }
        // Ambil angka dari kode terakhir
        $lastCode = $last->code;
        $number = (int) filter_var($lastCode, FILTER_SANITIZE_NUMBER_INT);
        $nextNumber = str_pad($number + 1, 3, '0', STR_PAD_LEFT);
        return "KNCR{$nextNumber}";
    }

    // ─── INDEX ────────────────────────────────────────
    public function index()
    {
        $products = Product::latest()->paginate(10)->through(function ($product) {
            return [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'category' => $product->category,
                'price' => $product->price,
                'weight' => $product->weight,
                'stock' => $product->stock,
                'image' => $product->image ? asset($product->image) : null,
                'is_active' => $product->is_active,
            ];
        });

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
        ]);
    }

    // ─── BULK UPLOAD FORM ─────────────────────────────
    public function bulkUploadForm()
    {
        return Inertia::render('Admin/Products/BulkUpload');
    }

    // ─── BULK UPLOAD PROSES ───────────────────────────
    public function bulkUpload(Request $request)
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
        ]);

        $results = [
            'success' => [],
            'failed' => [],
        ];

        foreach ($request->file('images') as $file) {
            // Ambil nama file tanpa ekstensi → cocokkan ke kode produk
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $code = strtoupper(trim($originalName));

            $product = Product::where('code', $code)->first();

            if (!$product) {
                $results['failed'][] = $file->getClientOriginalName() . ' (kode tidak ditemukan)';
                continue;
            }

            // Hapus foto lama kalau ada
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }

            // Simpan foto baru
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);

            $product->update(['image' => 'uploads/products/' . $filename]);

            $results['success'][] = $product->name;
        }

        return back()->with('bulk_results', $results);
    }

    // ─── CREATE ───────────────────────────────────────
    public function create()
    {
        return Inertia::render('Admin/Products/Create', [
            'next_code' => $this->generateCode(),
        ]);
    }

    // ─── STORE ────────────────────────────────────────
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:products,code'],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'code.required' => 'Kode produk wajib diisi.',
            'code.unique' => 'Kode produk sudah digunakan.',
            'price.required' => 'Harga wajib diisi.',
            'weight.required' => 'Berat wajib diisi.',
            'stock.required' => 'Stok wajib diisi.',
        ]);

        // Generate nama otomatis
        $validated['name'] = $this->generateName($validated['code'], $validated['price']);
        $validated['category'] = $this->getCategory($validated['price']);

        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $validated['image'] = 'uploads/products/' . $filename;
        }

        Product::create($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil ditambahkan!');
    }

    // ─── EDIT ─────────────────────────────────────────
    public function edit(Product $product)
    {
        return Inertia::render('Admin/Products/Edit', [
            'product' => [
                'id' => $product->id,
                'code' => $product->code,
                'name' => $product->name,
                'category' => $product->category,
                'price' => $product->price,
                'weight' => $product->weight,
                'stock' => $product->stock,
                'image_url' => $product->image ? asset($product->image) : null,
                'image' => $product->image, // untuk delete path asli
                'is_active' => $product->is_active,
            ],
        ]);
    }

    // ─── UPDATE ───────────────────────────────────────
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:products,code,' . $product->id],
            'price' => ['required', 'numeric', 'min:0'],
            'weight' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ], [
            'code.required' => 'Kode produk wajib diisi.',
            'code.unique' => 'Kode produk sudah digunakan.',
            'price.required' => 'Harga wajib diisi.',
            'weight.required' => 'Berat wajib diisi.',
            'stock.required' => 'Stok wajib diisi.',
        ]);

        // Update nama & kategori otomatis
        $validated['name'] = $this->generateName($validated['code'], $validated['price']);
        $validated['category'] = $this->getCategory($validated['price']);

        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path($product->image))) {
                unlink(public_path($product->image));
            }
            $file = $request->file('image');
            $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/products'), $filename);
            $validated['image'] = 'uploads/products/' . $filename;
        } else {
            unset($validated['image']);
        }
        $product->update($validated);

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil diupdate!');
    }

    // ─── DESTROY ──────────────────────────────────────
    public function destroy(Product $product)
    {
        if ($product->image && file_exists(public_path($product->image))) {
            unlink(public_path($product->image));
        }
        $product->delete();

        return redirect()->route('admin.products.index')
            ->with('success', 'Produk berhasil dihapus!');
    }
}
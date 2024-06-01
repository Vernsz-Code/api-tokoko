<?php

namespace App\Http\Controllers;

use App\Models\Products;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Products::with(['category', 'store'])->get();
        return response()->json($products);
    }
    /**
     * Cari produk berdasarkan nama.
     *
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByName($name)
    {
        $products = Products::with(['category', 'store'])
                            ->where('title', 'like', '%' . $name . '%')
                            ->get();

        if ($products->isEmpty()) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }

        return response()->json($products);
    }
    /**
     * 
     *
     * @param int $categoryId
     * @return \Illuminate\Http\JsonResponse
     */
    public function show48()
    {
        // Ambil semua kategori
        $categories = Category::all();
        $products = collect();

        foreach ($categories as $category) {
            // Ambil hingga 12 produk untuk setiap kategori
            $categoryProducts = Products::with(['category', 'store'])
                                        ->where('category_id', $category->id)
                                        ->take(12)
                                        ->get();
            
            $products = $products->merge($categoryProducts);

            // Jika total produk mencapai 48, hentikan pengambilan lebih lanjut
            if ($products->count() >= 48) {
                break;
            }
        }

        // Batasi total produk hingga 48
        $products = $products->take(48);

        if ($products->isEmpty()) {
            return response()->json(['message' => 'Tidak ada produk dalam kategori ini'], 404);
        }

        return response()->json($products);
    }


    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'description' => 'required|string',
                'thumbnail' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'store_id' => 'required|integer',
                'price' => 'required|numeric',
                'stock' => 'required|integer',
                'status' => 'required|boolean',
                'category_id' => 'required|integer|exists:categories,id'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }

        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            $hashName = hash('sha256', $validated['title'] . now()) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('public/imageproduct', $hashName);
            $validated['thumbnail'] = $hashName; // Menggunakan nama hash untuk thumbnail
        }

        $product = Products::create($validated);

        return response()->json(["data" => $product], 201);
    }

    public function show(Products $product)
    {
        $product->load(['category', 'store']);
        return response()->json($product);
    }

    public function showByStore($storeId)
    {
        $products = Products::with(['category', 'store'])
                                        ->where('store_id', $storeId)->get();
        
        if ($products->isEmpty()) {
            return response()->json(['message' => 'Tidak ada produk dalam toko ini'], 404);
        }

        return response()->json($products);
    }

    public function update(Request $request, $id)
    {
        try {
            // Validasi input
            $validated = $request->validate([
                'title' => 'sometimes|string|max:255',
                'description' => 'sometimes|string',
                'thumbnail' => 'sometimes|nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'store_id' => 'sometimes|integer|exists:stores,id',
                'price' => 'sometimes|numeric',
                'stock' => 'sometimes|integer',
                'status' => 'sometimes|boolean',
                'category_id' => 'sometimes|integer|exists:categories,id'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'errors' => $e->errors()
            ], 422);
        }

        // Cari produk berdasarkan ID
        $product = Products::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Handle file upload
        if ($request->hasFile('thumbnail')) {
            $file = $request->file('thumbnail');
            // Memeriksa apakah thumbnail tidak kosong
            if ($file->isValid()) {
                $hashName = hash('sha256', $validated['title'] ?? $product->title) . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('public/imageproduct', $hashName);
                $validated['thumbnail'] = $hashName;
            } else {
                if ($product->thumbnail) {
                    Storage::delete('public/imageproduct/' . $product->thumbnail);
                }
            }
        }
        if(is_null($request->thumbnail)){
            unset($validated['thumbnail']);
        }
        
        $product->update($validated);

        return response()->json($product);
    }

    public function destroy(Products $product)
    {
        $product->delete();
        return response()->json(null, 204);
    }
}

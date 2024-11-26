<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
{
    $keyword = $request->input('keyword');
    $categoryName = $request->input('category_name');
    $brandName = $request->input('brand_name');

    $products = Product::with(['category', 'brand']);

    // Filter berdasarkan nama produk
    if ($keyword) {
        $products = $products->where('name', 'like', "%{$keyword}%");
    }

    // Filter berdasarkan nama kategori
    if ($categoryName) {
        $products = $products->whereHas('category', function ($query) use ($categoryName) {
            $query->where('name', 'like', "%{$categoryName}%");
        });
    }

    // Filter berdasarkan nama merek
    if ($brandName) {
        $products = $products->whereHas('brand', function ($query) use ($brandName) {
            $query->where('name', 'like', "%{$brandName}%");
        });
    }

    $products = $products->orderBy('name', 'desc')->paginate(10);

    return response()->json($products->map(function ($product) {
        return [
            'id' => $product->id,
            'name' => $product->name,
            'price' => $product->price,
            'stock' => $product->stock,
            'category_name' => $product->category->name,
            'brand_name' => $product->brand->name,
        ];
    }));
}


    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ], [
            'name.required' => 'Nama produk wajib diisi.',
            'price.required' => 'Harga produk wajib diisi.',
            'price.numeric' => 'Harga harus berupa angka.',
            'stock.required' => 'Stok produk wajib diisi.',
            'stock.integer' => 'Stok harus berupa bilangan bulat.',
        ]);

    
        $product = Product::create($validatedData);

        // Ambil nama kategori dan merek berdasarkan relasi
        $product->load(['category', 'brand']);

        return response()->json([
            'message' => 'Produk berhasil ditambahkan',
            'product' => [
                'id' => $product->id,
                'name' => $product->name,
                'price' => $product->price,
                'stock' => $product->stock,
                'category_name' => $product->category->name,
                'brand_name' => $product->brand->name,
            ],
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        if ($product) {
        return response()->json(['product' => $product]);
        } else {
        return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'required|exists:brands,id',
        ]);
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Produk tidak ditemukan'], 404);
        }
        $product->update($validatedData);
        return response()->json(['message' => 'Produk berhasil diupdate', 'product' =>$product]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::find($id);

        if (!$product) {
        return response()->json(['message' => 'Produk tidak ditemukan'],404);
        }
        $product->delete();
        return response()->json(['message' => 'Produk berhasil dihapus']);
    }
}

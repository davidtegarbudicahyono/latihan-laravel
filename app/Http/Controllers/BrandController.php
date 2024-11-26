<?php

namespace App\Http\Controllers;

use App\Models\Brand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    //
    public function index(Request $request)
    {
        $keyword = $request->input("keyword");
        $brands = new Brand;

        if ($keyword) {
            $brands = $brands->where("name", "like", "%{$keyword}%");
        }    

        $brands = $brands->orderBy("name", "desc")->paginate(10);
        return response()->json($brands);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required",
        ], [
            "name.required"=> "Nama brand wajib diisi.",
        ]);

        $brand = Brand::create($validatedData);

        return response()->json([
            "message" => "Brand berhasil ditambahkan",
            "brand" => $brand,
        ], 201);
    }

    public function show(string $id)
    {
        $brand = Brand::find($id);
        if ($brand) {
            return response()->json(["brand" => $brand]);
        } else {    
            return response()->json(["message" => "Brand tidak ditemukan"], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            "name" => "required",
        ]);

        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(["message" => "Brand tidak ditemukan"], 404);
        }
        $brand->update($validatedData);
        return response()->json(["message" => "Brand berhasil diupdate", "brand" => $brand]);
    }

    public function destroy(string $id)
    {
        $brand = Brand::find($id);
        if (!$brand) {
            return response()->json(["message" => "Brand tidak ditemukan"], 404);
        }
        $brand->delete();
        return response()->json(["message" => "Brand berhasil dihapus"]);
    }
}

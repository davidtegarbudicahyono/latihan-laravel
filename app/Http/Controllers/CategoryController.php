<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    //
    public function index(Request $request)
    {
        $keyword = $request->input("keyword");
        $categories = new Category;

        if ($keyword) {
            $categories = $categories->where("name", "like", "%{$keyword}%");
        }    

        $categories = $categories->orderBy("name", "desc")->paginate(10);
        return response()->json($categories);
    } 
    
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            "name" => "required",
        ], [
            "name.required"=> "Nama kategori wajib diisi.",
        ]);

        $category = Category::create($validatedData);

        return response()->json([
            "message" => "Category berhasil ditambahkan",
            "category" => $category,
        ], 201);
    }

    public function show(string $id)
    {
        $category = Category::find($id);
        if ($category) {
            return response()->json(["category" => $category]);
        } else {    
            return response()->json(["message" => "Category tidak ditemukan"], 404);
        }
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            "name" => "required",
        ]);

        $category = Category::find($id);
        if (!$category) {
            return response()->json(["message" => "Category tidak ditemukan"], 404);
        }
        $category->update($validatedData);
        return response()->json(["message" => "Category berhasil diupdate", "category" => $category]);
    }

    protected function destroy(string $id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(["message" => "Category tidak ditemukan"], 404);
        }
        $category->delete();
        return response()->json(["message" => "Category berhasil dihapus"]);
    }
}

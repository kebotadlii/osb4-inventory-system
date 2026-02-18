<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // =========================
    // LIST KATEGORI
    // =========================
    public function index()
    {
        $categories = Category::orderBy('name')->get();

        return view('categories.index', compact('categories'));
    }

    // =========================
    // FORM TAMBAH KATEGORI
    // =========================
    public function create()
    {
        return view('categories.create');
    }

    // =========================
    // SIMPAN KATEGORI
    // =========================
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:categories,name'
        ]);

        Category::create([
            'name' => $request->name
        ]);

        return redirect()
            ->route('categories.index')
            ->with('success', 'Kategori berhasil ditambahkan');
    }
}

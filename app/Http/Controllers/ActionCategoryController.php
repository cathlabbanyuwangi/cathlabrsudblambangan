<?php

namespace App\Http\Controllers;

use App\Models\ActionCategory;
use Illuminate\Http\Request;

class ActionCategoryController extends Controller
{
    public function index()
    {
        $items = ActionCategory::all();
        return view('master.categories.index', compact('items'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        ActionCategory::create($validated);

        return back()->with('success', 'Kategori divisi berhasil ditambahkan!');
    }

    public function destroy(ActionCategory $category)
    {
        $category->delete();

        return back()->with('success', 'Kategori divisi berhasil dihapus!');
    }
}
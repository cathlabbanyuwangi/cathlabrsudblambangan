<?php

namespace App\Http\Controllers;

use App\Models\SubDivision;
use App\Models\ActionCategory;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class SubDivisionController extends Controller
{
    public function index()
    {
        $items = SubDivision::with('category')->get();
        $categories = ActionCategory::all();
        return view('master.sub_divisions.index', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'action_category_id' => 'required|exists:action_categories,id',
        ]);

        SubDivision::create($validated);

        return back()->with('success', 'Sub-divisi berhasil ditambahkan!');
    }

    public function destroy(SubDivision $subDivision)
    {
        $subDivision->delete();

        return back()->with('success', 'Sub-divisi berhasil dihapus!');
    }

    public function getByCategory(int $id): JsonResponse
    {
        $subDivisions = SubDivision::where('action_category_id', $id)->get();
        return response()->json($subDivisions);
    }
}
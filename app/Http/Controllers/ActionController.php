<?php

namespace App\Http\Controllers;

use App\Models\Action;
use App\Models\ActionCategory;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;

class ActionController extends Controller
{
    public function index()
    {
        $items = Action::with('category')->get();
        $categories = ActionCategory::all();
        return view('master.actions.index', compact('items', 'categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'action_category_id' => 'required|exists:action_categories,id',
        ]);

        Action::create($validated);

        return back()->with('success', 'Tindakan berhasil ditambahkan!');
    }

    public function destroy(Action $action)
    {
        try {
            $action->delete();

            return redirect()->route('actions.index')->with('success', 'Data tindakan berhasil dihapus.');
            
        } catch (QueryException $e) {
            // Cek jika error karena pelanggaran Foreign Key (kode SQLSTATE 23000)
            if ($e->getCode() == "23000") {
                return redirect()->back()->with('error', 'Tindakan ini tidak dapat dihapus karena masih digunakan atau terikat dengan data rekam medis pasien!');
            }

            // Error database umum lainnya
            return redirect()->back()->with('error', 'Terjadi kesalahan pada sistem database.');
        }
    }
}
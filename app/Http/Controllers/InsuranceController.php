<?php

namespace App\Http\Controllers;

use App\Models\Insurance;
use Illuminate\Http\Request;

class InsuranceController extends Controller
{
    /**
     * Menampilkan daftar semua jaminan.
     */
    public function index()
    {
        $insurances = Insurance::all();
        return view('insurances.index', compact('insurances'));
    }

    /**
     * Menampilkan form untuk menambah jaminan baru.
     */
    public function create()
    {
        return view('insurances.create');
    }

    /**
     * Menyimpan data jaminan baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:insurances,name',
        ]);

        Insurance::create($request->only('name'));

        return redirect()->route('insurances.index')->with('success', 'Data jaminan berhasil ditambahkan!');
    }

    /**
     * Menampilkan form edit jaminan.
     */
    public function edit(Insurance $insurance)
    {
        return view('insurances.edit', compact('insurance'));
    }

    /**
     * Memperbarui data jaminan di database.
     */
    public function update(Request $request, Insurance $insurance)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:insurances,name,' . $insurance->id,
        ]);

        $insurance->update($request->only('name'));

        return redirect()->route('insurances.index')->with('success', 'Data jaminan berhasil diperbarui!');
    }

    /**
     * Menghapus data jaminan.
     */
    public function destroy(Insurance $insurance)
    {
        $insurance->delete();

        return redirect()->route('insurances.index')->with('success', 'Data jaminan berhasil dihapus!');
    }
}
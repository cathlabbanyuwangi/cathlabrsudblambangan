<?php

namespace App\Http\Controllers;

use App\Models\SupportingOption;
use Illuminate\Http\Request;

class SupportingOptionController extends Controller
{
    public function index()
    {
        $options = SupportingOption::all();
        return view('penunjang.index', compact('options'));
    }

    public function create()
    {
        return view('penunjang.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:supporting_options,name',
        ]);

        SupportingOption::create($request->only('name'));

        return redirect()->route('supporting-options.index')->with('success', 'Opsi penunjang berhasil ditambahkan!');
    }

    public function edit(SupportingOption $supportingOption)
    {
        return view('penunjang.edit', compact('supportingOption'));
    }

    public function update(Request $request, SupportingOption $supportingOption)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:supporting_options,name,' . $supportingOption->id,
        ]);

        $supportingOption->update($request->only('name'));

        return redirect()->route('supporting-options.index')->with('success', 'Opsi penunjang berhasil diperbarui!');
    }

    public function destroy(SupportingOption $supportingOption)
    {
        $supportingOption->delete();
        return redirect()->route('supporting-options.index')->with('success', 'Opsi penunjang berhasil dihapus!');
    }
}
<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\ActionCategory;
use App\Models\SubDivision;
use App\Models\Action;
use Illuminate\Http\Request;

class DoctorController extends Controller
{
    public function index()
    {
        $items = Doctor::with(['category', 'subDivision', 'actions'])->get();
        $categories = ActionCategory::all();
        $subDivisions = SubDivision::all();
        $allActions = Action::with('category')->get();
        return view('master.doctors.index', compact('items', 'categories', 'subDivisions', 'allActions'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'action_category_id' => 'required|exists:action_categories,id',
            'sub_division_id' => 'required|exists:sub_divisions,id',
            'actions' => 'nullable|array',
            'actions.*' => 'exists:actions,id',
        ]);

        $doctor = Doctor::create($validated);
        
        // Sinkronisasi tindakan lintas kategori via checkbox
        if ($request->has('actions')) {
            $doctor->actions()->sync($request->actions);
        }

        return back()->with('success', 'Dokter berhasil ditambahkan!');
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'action_category_id' => 'required|exists:action_categories,id',
            'sub_division_id' => 'required|exists:sub_divisions,id',
            'actions' => 'nullable|array',
            'actions.*' => 'exists:actions,id',
        ]);

        $doctor->update($validated);
        
        // Perbarui data checkbox tindakan lintas kategori
        $doctor->actions()->sync($request->actions ?? []);

        return back()->with('success', 'Data dokter berhasil diperbarui!');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->actions()->detach();
        $doctor->delete();

        return back()->with('success', 'Dokter berhasil dihapus!');
    }
}
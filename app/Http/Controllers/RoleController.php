<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission; // <-- Tambahkan import Permission
use Illuminate\Support\Facades\Redirect;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get(); // Ambil role beserta permissions-nya
        return view('roles.index', compact('roles'));
    }

    public function create()
    {
        $permissions = Permission::all(); // Ambil semua daftar permission
        return view('roles.create', compact('permissions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name|max:255',
            'permissions' => 'array', // Validasi array dari checkbox
        ]);

        $role = Role::create(['name' => $request->name, 'guard_name' => 'web']);

        // Sinkronisasi permissions yang dicentang
        if ($request->has('permissions')) {
            $role->syncPermissions($request->permissions);
        }

        return Redirect::route('roles.index')->with('success', 'Role dan hak akses berhasil ditambahkan!');
    }

    public function edit(Role $role)
    {
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray(); // Permission yang sudah dimiliki role ini

        return view('roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id . '|max:255',
            'permissions' => 'array',
        ]);

        $role->update(['name' => $request->name]);

        // Perbarui permissions berdasarkan checkbox yang dicentang
        $role->syncPermissions($request->permissions ?? []);

        return Redirect::route('roles.index')->with('success', 'Role dan hak akses berhasil diperbarui!');
    }

    public function destroy(Role $role)
    {
        if ($role->users()->count() > 0) {
            return Redirect::back()->with('error', 'Role tidak bisa dihapus karena masih digunakan oleh User!');
        }

        $role->delete();

        return Redirect::route('roles.index')->with('success', 'Role berhasil dihapus!');
    }
}
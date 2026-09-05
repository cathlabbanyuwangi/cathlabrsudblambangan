<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * 1. Menampilkan daftar semua user.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        return view('users.index', compact('users'));
    }

    /**
     * 2. Menampilkan form untuk menambah user baru.
     */
    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    /**
     * 3. Menyimpan user baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'exists:roles,name'],
        ]);

        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Berikan role ke user baru
        $user->assignRole($request->role);

        return redirect()->route('users.index')->with('success', 'User baru berhasil ditambahkan!');
    }

    /**
     * 4. Menampilkan form edit user & role.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * 5. Menyimpan perubahan data dan role user.
     */
    public function update(Request $request, User $user)
{
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'username' => ['required', 'string', 'max:255', 'unique:users,username,' . $user->id],
        'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
        'role' => ['required', 'exists:roles,name'],
    ];

    // Jika password diisi, validasi konfirmasinya
    if ($request->filled('password')) {
        $rules['password'] = ['confirmed', \Illuminate\Validation\Rules\Password::defaults()];
    }

    $request->validate($rules);

    // Data yang akan di-update
    $data = [
        'name' => $request->name,
        'username' => $request->username,
        'email' => $request->email,
    ];

    // Update password jika diisi
    if ($request->filled('password')) {
        $data['password'] = \Illuminate\Support\Facades\Hash::make($request->password);
    }

    $user->update($data);

    // Sync role menggunakan Spatie
    $user->syncRoles([$request->role]);

    return redirect()->route('users.index')->with('success', 'Data user berhasil diperbarui!');
}
}
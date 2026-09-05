<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run()
    {
        // 1. Buat role 'creator' jika belum ada
        $role = Role::firstOrCreate(['name' => 'creator', 'guard_name' => 'web']);

        // 2. Buat user 'creator' berdasarkan skema migrasi (UUID, username, email)
        $user = User::firstOrCreate(
            ['username' => 'creator'], // Pengecekan berdasarkan username
            [
                'id' => (string) Str::uuid(), // Menghasilkan UUID untuk primary key
                'name' => 'Creator System',
                'email' => 'creator@cathlab.test', // Email wajib diisi karena tidak nullable pada migrasi
                'password' => Hash::make('p455w0rd'),
            ]
        );

        // 3. Berikan role 'creator' ke user tersebut
        if (!$user->hasRole('creator')) {
            $user->assignRole($role);
        }
    }
}
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    public function run()
    {
        // Daftar seluruh menu dan hak akses lengkap aplikasi Cathlab
        $permissions = [
            // Dashboard
            'akses-dashboard',

            // Data Pasien & Tindakan
            'pendaftaran-pasien',
            'riwayat-tindakan',

            // Laporan & Statistik
            'laporan-ringkasan',
            'laporan-klinis',
            'laporan-operasional',
            'laporan-rekapitulasi',
            'cetak-laporan',
            'backup-laporan',

            // Master Data
            'kelola-kategori-divisi',
            'kelola-sub-divisi',
            'kelola-tindakan',
            'kelola-dokter',
            'kelola-jaminan',
            'kelola-penunjang',
            'kelola-role',
            'kelola-user',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }
    }
}
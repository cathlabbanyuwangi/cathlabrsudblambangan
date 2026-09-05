<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class PatientsTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Berikan 1 contoh baris data dummy/panduan pengisian
        return new Collection([
            [
                'Poli Jantung',            // Source
                'RM-001',                  // Medical Record Number
                'Nama Lengkap Pasien',     // Name
                '1990-05-12',              // Date of Birth (YYYY-MM-DD)
                'L',                       // Gender (L/P)
                'Jl. Contoh Alamat No. 1', // Address
                'Banyuwangi',              // Regency
                'Banyuwangi',              // District
                '081234567890',            // Patient Phone
                '081234567891',            // Family Phone
                1,                         // Insurance ID (Pastikan ID Asuransi valid)
                'Contoh catatan pasien'    // Notes
            ]
        ]);
    }

    public function headings(): array
    {
        return [
            'Source', 
            'Medical Record Number', 
            'Name', 
            'Date of Birth (YYYY-MM-DD)', 
            'Gender (L/P)', 
            'Address', 
            'Regency', 
            'District', 
            'Patient Phone', 
            'Family Phone', 
            'Insurance ID', 
            'Notes'
        ];
    }
}
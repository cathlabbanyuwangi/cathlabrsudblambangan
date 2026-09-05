<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;

class ActionRecordsTemplateExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        // Memberikan 1 contoh baris data agar user tahu formatnya
        return new Collection([
            [
                '10-11-2018',         // TANGGAL TINDAKAN
                '2340',               // NO RM
                'Sabarudin',          // NAMA PASIEN
                'BPJS Kelas 1',       // PENJAMIN
                'UAP, HHF, DM',       // DIAGNOSA
                'DCA',                // TINDAKAN
                '-'                   // JUMLAH RING (STENT)
            ]
        ]);
    }

    public function headings(): array
    {
        // Harus SAMA PERSIS dengan header yang dibaca oleh import logic
        return [
            'TANGGAL TINDAKAN',
            'NO RM',
            'NAMA PASIEN',
            'PENJAMIN',
            'DIAGNOSA',
            'TINDAKAN',
            'JUMLAH RING (STENT)'
        ];
    }
}
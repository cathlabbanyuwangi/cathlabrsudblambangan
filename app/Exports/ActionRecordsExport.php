<?php

namespace App\Exports;

use App\Models\ActionRecord;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ActionRecordsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return ActionRecord::with(['patient', 'doctor', 'action'])->latest()->get();
    }

    public function headings(): array
    {
        return [
            'Tanggal',
            'Nama Pasien',
            'No. RM',
            'Jenis Tindakan',
            'Dokter DPJP',
            'Asal Ruangan',
            'Status Urgensi',
            'Diagnosa Utama',
            'Kesimpulan'
        ];
    }

    public function map($record): array
    {
        return [
            $record->created_at->format('Y-m-d H:i:s'),
            $record->patient->name ?? '-',
            $record->patient->medical_record_number ?? '-',
            $record->action->name ?? '-',
            $record->doctor->name ?? '-',
            $record->origin_ward,
            $record->is_cito ? 'CITO' : 'ELEKTIF',
            $record->diagnosis_1,
            $record->conclusion,
        ];
    }
}
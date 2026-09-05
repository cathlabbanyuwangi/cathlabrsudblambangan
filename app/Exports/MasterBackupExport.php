<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class MasterBackupExport implements FromCollection, WithHeadings, WithMapping
{
    protected Collection $records;

    public function __construct(Collection $records)
    {
        $this->records = $records;
    }

    public function collection()
    {
        return $this->records;
    }

    public function headings(): array
    {
        return [
            'Tanggal Tindakan',
            'No. MR',
            'Nama Pasien',
            'Alamat',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Penjamin',
            'Asal Ruangan',
            'Divisi / Kategori',
            'Jenis Tindakan',
            'Dokter Operator',
            'Diagnosa',
            'Waktu Masuk IGD',
            'Waktu Dilatasi Balon',
            'Status Cito',
            'Status Keberhasilan'
        ];
    }

    public function map($row): array
    {
        $diagnosis = $row->conclusion ?? '-';
        $diagnosis = str_replace('Data Laporan: ', '', $diagnosis);

        return [
            $row->action_date ?? '-',
            $row->medical_record_number ?? '-',
            $row->patient_name ?? '-',
            $row->address ?? '-',
            $row->date_of_birth ?? '-',
            $row->gender ?? '-',
            $row->insurance_name ?? '-',
            $row->origin_ward ?? '-',
            $row->category_name ?? '-',
            $row->action_name ?? '-',
            $row->doctor_name ?? '-',
            trim($diagnosis),
            $row->d2b_igd_time ?? '-',
            $row->d2b_balloon_dilatation_time ?? '-',
            isset($row->is_cito) ? ($row->is_cito == 1 ? 'Ya' : 'Tidak') : '-',
            isset($row->is_successful) ? ($row->is_successful == 1 ? 'Berhasil' : 'Tidak') : '-'
        ];
    }
}
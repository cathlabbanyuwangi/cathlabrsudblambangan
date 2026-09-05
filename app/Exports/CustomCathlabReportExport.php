<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class CustomCathlabReportExport implements FromCollection, WithHeadings, WithMapping
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
            'Tanggal',
            'No MR',
            'Nama Pasien',
            'Jenis Kelamin',
            'Tanggal Lahir',
            'Penjamin',
            'Diagnosa',
            'Tindakan'
        ];
    }

    public function map($row): array
    {
        $diagnosis = $row->diagnosis ?? '-';
        $diagnosis = str_replace('Data Laporan: ', '', $diagnosis);

        $dob = '-';
        if (!empty($row->date_of_birth)) {
            $dob = Carbon::parse($row->date_of_birth)->format('d-m-Y');
        }

        return [
            $row->action_date ?? '-',
            $row->medical_record_number ?? '-',
            $row->patient_name ?? '-',
            $row->gender ?? '-',
            $dob,
            $row->insurance_name ?? '-',
            trim($diagnosis),
            $row->action_name ?? '-'
        ];
    }
}
<?php

namespace App\Exports;

use App\Models\Patient;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PatientsExport implements FromCollection, WithHeadings
{
    public function collection()
    {
        return Patient::all(['medical_record_number', 'name', 'patient_phone', 'gender', 'district', 'status']);
    }

    public function headings(): array
    {
        return ['No RM', 'Nama Pasien', 'No Telepon', 'Gender', 'Kecamatan', 'Status'];
    }
}
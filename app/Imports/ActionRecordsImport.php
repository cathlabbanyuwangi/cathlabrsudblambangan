<?php

namespace App\Imports;

use App\Models\ActionRecord;
use App\Models\Patient;
use App\Models\Action;
use App\Models\Insurance;
use App\Models\Doctor;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ActionRecordsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Normalisasi key array agar aman dari perbedaan huruf besar/kecil pada header Excel
        $row = array_change_key_case($row, CASE_LOWER);
        
        // 1. Validasi: Pastikan No RM tersedia
        $noRm = $row['no_rm'] ?? $row['medical_record_number'] ?? null;
        if (empty($noRm)) {
            return null;
        }

        // 2. Sinkronisasi Penjamin (Aman dari Foreign Key Error)
        $insuranceId = null;
        $penjaminInput = trim($row['penjamin'] ?? $row['insurance_id'] ?? '');

        if (!empty($penjaminInput)) {
            if (is_numeric($penjaminInput)) {
                $insuranceExists = Insurance::find($penjaminInput);
                $insuranceId = $insuranceExists ? $insuranceExists->id : null;
            } else {
                $insurance = Insurance::firstOrCreate(['name' => $penjaminInput]);
                $insuranceId = $insurance->id;
            }
        }

        if (!$insuranceId) {
            $defaultInsurance = Insurance::first();
            $insuranceId = $defaultInsurance ? $defaultInsurance->id : 1;
        }

        // 3. Cari atau Buat Pasien (AMAN: Tidak menimpa alamat/wilayah yang sudah ada)
        $patient = Patient::where('medical_record_number', trim($noRm))->first();

        if (!$patient) {
            // Jika pasien belum terdaftar sama sekali, buat baru dengan data default
            $patient = Patient::create([
                'medical_record_number' => trim($noRm),
                'name'                  => trim($row['nama_pasien'] ?? 'Tanpa Nama'),
                'insurance_id'          => $insuranceId,
                'source'                => 'import_laporan',
                'date_of_birth'         => '2000-01-01',
                'gender'                => 'L',
                'address'               => '-',
                'regency'               => '-',
                'district'              => '-',
            ]);
        } else {
            // Jika pasien sudah ada, HANYA perbarui nama & penjamin jika ada, 
            // kolom address, regency, district, dan date_of_birth DIBIARKAN UTUH.
            $updateData = [];
            if (!empty($row['nama_pasien']) && $row['nama_pasien'] !== 'Tanpa Nama') {
                $updateData['name'] = trim($row['nama_pasien']);
            }
            if ($insuranceId) {
                $updateData['insurance_id'] = $insuranceId;
            }
            
            if (!empty($updateData)) {
                $patient->update($updateData);
            }
        }

        // 4. Cari atau Buat Tindakan (Action)
        $actionName = trim($row['tindakan'] ?? 'Lain-lain');
        if (empty($actionName) || $actionName === '-') {
            $actionName = 'Lain-lain';
        }

        $action = Action::firstOrCreate(
            ['name' => $actionName],
            ['action_category_id' => 1]
        );

        // 5. Penentuan Dokter Otomatis Berdasarkan Nama Tindakan
        $actionText = strtolower($actionName);
        $targetDoctorName = 'dr. Nelly'; // Default

        if (str_contains($actionText, 'dsa') || str_contains($actionText, 'coiling') || str_contains($actionText, 'embolisasi')) {
            $targetDoctorName = 'dr. Firman';
        } elseif (str_contains($actionText, 'arteriografi') || str_contains($actionText, 'angioplasty')) {
            $targetDoctorName = 'dr. Nizam';
        }

        $doctor = Doctor::where('name', 'like', "%{$targetDoctorName}%")->first();
        $doctorId = $doctor ? $doctor->id : 1;

        // 6. Pengolahan Diagnosa
        $rawDiagnosa = trim($row['diagnosa'] ?? '-');
        $diagList = ($rawDiagnosa !== '' && $rawDiagnosa !== '-') 
            ? array_map('trim', explode(',', $rawDiagnosa)) 
            : [];

        $diag1 = $diagList[0] ?? '-';
        $diag2 = $diagList[1] ?? null;
        $diag3 = $diagList[2] ?? null;
        $combinedDiag = implode(', ', $diagList) ?: '-';

        // 7. Jumlah Ring / Stent
        $ringInput = $row['jumlah_ring_stent'] ?? $row['ring_count'] ?? 0;
        $ringCount = is_numeric($ringInput) ? (int)$ringInput : 0;

        // 8. Parsing Tanggal Tindakan dari Excel
        $rawTanggal = $row['tanggal_tindakan'] ?? $row['tanggal'] ?? null;
        $tanggalTindakan = $this->parseExcelDate($rawTanggal);

        // 9. Simpan ActionRecord
        $record = new ActionRecord([
            'patient_id'         => $patient->id,
            'doctor_id'          => $doctorId, 
            'action_id'          => $action->id,
            'action_category_id' => $action->action_category_id,
            'origin_ward'        => $row['asal_ruangan'] ?? 'Laporan Eksternal',
            'diagnosis_1'        => $diag1,
            'diagnosis_2'        => $diag2,
            'diagnosis_3'        => $diag3,
            'ring_count'         => $ringCount,
            'conclusion'         => 'Data Laporan: ' . $combinedDiag,
            'suggestion'         => '-',
            'notes'              => 'Jumlah Ring: ' . $ringInput,
            'action_date'        => $tanggalTindakan,
            'created_at'         => $tanggalTindakan,
            'updated_at'         => now(),
        ]);

        $record->timestamps = false;
        $record->save();

        // 10. Perbarui Status Pasien Menjadi 'pernah_tindakan'
        $latestAction = ActionRecord::where('patient_id', $patient->id)->latest('action_date')->first();
        if ($latestAction) {
            $patient->update([
                'status'      => 'pernah_tindakan',
                'action_date' => $latestAction->action_date ?? $latestAction->created_at,
            ]);
        }

        return $record;
    }

    private function parseExcelDate($value)
    {
        if (empty($value)) {
            return now();
        }

        if (is_numeric($value)) {
            try {
                return Carbon::instance(Date::excelToDateTimeObject($value));
            } catch (\Exception $e) {
                // Lanjutkan ke parser berikutnya jika gagal
            }
        }

        $dateStr = trim((string)$value);

        if (preg_match('/^(\d{1,2})[-\/](\d{1,2})[-\/](\d{4})$/', $dateStr, $matches)) {
            $day = (int)$matches[1];
            $month = (int)$matches[2];
            $year = (int)$matches[3];
            if (checkdate($month, $day, $year)) {
                return Carbon::create($year, $month, $day, 0, 0, 0);
            }
        }

        try {
            return Carbon::parse($dateStr);
        } catch (\Exception $e) {
            return now();
        }
    }
}
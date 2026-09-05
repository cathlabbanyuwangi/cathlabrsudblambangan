<?php

namespace App\Imports;

use App\Models\Patient;
use App\Models\Insurance;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class PatientsImport implements ToCollection
{
    public function collection(Collection $rows)
    {
        $isHeader = true;

        foreach ($rows as $row) {

            // Lewati header
            if ($isHeader) {
                $isHeader = false;
                continue;
            }

            // Ubah row menjadi array
            $data = $row->toArray();

            /*
             * =========================================================
             * NO RM
             * =========================================================
             */
            $noRm = $this->cleanValue($data[1] ?? null);

            if ($noRm === null || $noRm === '-') {
                continue;
            }

            // Excel sering membaca nomor RM sebagai angka
            if (is_numeric($noRm)) {
                $noRm = number_format(
                    (float) $noRm,
                    0,
                    '',
                    ''
                );
            }

            $noRm = trim((string) $noRm);

            /*
             * =========================================================
             * NAMA
             * =========================================================
             */
            $name = $this->cleanValue($data[2] ?? null);

            if ($name === null || $name === '') {
                $name = 'Tanpa Nama';
            }

            /*
             * =========================================================
             * TANGGAL LAHIR
             * =========================================================
             */
            $dobRaw = $data[3] ?? null;

            $dob = $this->transformDate($dobRaw);

            if (!$dob) {
                $dob = '2000-01-01';
            }

            /*
             * =========================================================
             * JENIS KELAMIN
             * =========================================================
             */
            $gender = strtoupper(
                $this->cleanValue($data[4] ?? null) ?? 'L'
            );

            $gender = $gender === 'P' ? 'P' : 'L';

            /*
             * =========================================================
             * ALAMAT
             * =========================================================
             *
             * INI BAGIAN PENTING
             *
             * Jangan langsung menggunakan ?? '-'
             * karena cell Excel bisa berisi string kosong/spasi.
             */
            $address = $this->cleanValue($data[5] ?? null);

            if ($address === null || $address === '') {
                $address = '-';
            }

            /*
             * =========================================================
             * KECAMATAN
             * =========================================================
             */
            $district = $this->cleanValue($data[6] ?? null);

            if ($district === null || $district === '') {
                $district = '-';
            }

            /*
             * =========================================================
             * KABUPATEN
             * =========================================================
             */
            $regency = $this->cleanValue($data[7] ?? null);

            if ($regency === null || $regency === '') {
                $regency = 'Banyuwangi';
            }

            /*
             * =========================================================
             * TELEPON PASIEN
             * =========================================================
             */
            $phone = $this->cleanValue($data[8] ?? null);

            if ($phone !== null) {

                if (is_numeric($phone)) {
                    $phone = number_format(
                        (float) $phone,
                        0,
                        '',
                        ''
                    );
                }

                $phone = trim((string) $phone);

                /*
                 * 823330950059 -> 0823330950059
                 */
                if (
                    str_starts_with($phone, '8')
                ) {
                    $phone = '0' . $phone;
                }

                /*
                 * Jika Excel menyimpan 62xxxxxxxx
                 * ubah menjadi 0xxxxxxxx
                 */
                if (str_starts_with($phone, '62')) {
                    $phone = '0' . substr($phone, 2);
                }
            }

            /*
             * =========================================================
             * TELEPON KELUARGA
             * =========================================================
             */
            $familyPhone = $this->cleanValue($data[9] ?? null);

            if ($familyPhone !== null) {

                if (is_numeric($familyPhone)) {
                    $familyPhone = number_format(
                        (float) $familyPhone,
                        0,
                        '',
                        ''
                    );
                }

                $familyPhone = trim((string) $familyPhone);

                if (str_starts_with($familyPhone, '8')) {
                    $familyPhone = '0' . $familyPhone;
                }

                if (str_starts_with($familyPhone, '62')) {
                    $familyPhone = '0' . substr($familyPhone, 2);
                }
            }

            /*
             * =========================================================
             * ASURANSI / PENJAMIN
             * =========================================================
             */
            $insuranceInput = $this->cleanValue($data[10] ?? null);

            /*
             * Default insurance
             */
            $insurance = null;

            if ($insuranceInput !== null && $insuranceInput !== '') {

                /*
                 * Jika Excel berisi ID angka
                 */
                if (is_numeric($insuranceInput)) {

                    $insurance = Insurance::find(
                        (int) $insuranceInput
                    );

                } else {

                    /*
                     * Jika Excel berisi:
                     *
                     * BPJS 1
                     * BPJS 2
                     * BPJS 3
                     */
                    $insurance = Insurance::firstOrCreate([
                        'name' => trim((string) $insuranceInput)
                    ]);
                }
            }

            /*
             * Jika tidak ditemukan, gunakan ID 1
             */
            $insuranceId = $insurance?->id ?? 1;

            /*
             * =========================================================
             * CATATAN
             * =========================================================
             */
            $notes = $this->cleanValue($data[11] ?? null);

            /*
             * =========================================================
             * SOURCE
             * =========================================================
             *
             * Jika kolom Source Excel kosong, gunakan "import".
             */
            $source = $this->cleanValue($data[0] ?? null);

            if ($source === null || $source === '') {
                $source = 'import';
            }

            /*
             * =========================================================
             * SIMPAN / UPDATE
             * =========================================================
             */
            Patient::updateOrCreate(
                [
                    'medical_record_number' => $noRm,
                ],
                [
                    'source'          => $source,
                    'name'            => $name,
                    'date_of_birth'   => $dob,
                    'gender'          => $gender,
                    'address'         => $address,
                    'district'        => $district,
                    'regency'         => $regency,
                    'patient_phone'   => $phone,
                    'family_phone'    => $familyPhone,
                    'insurance_id'    => $insuranceId,
                    'notes'           => $notes,
                    'status'          => 'terdaftar',
                ]
            );
        }
    }

    /**
     * Membersihkan isi cell Excel.
     */
    private function cleanValue($value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = trim((string) $value);

        if ($value === '') {
            return null;
        }

        return $value;
    }

    /**
     * Konversi tanggal Excel menjadi Y-m-d.
     */
    private function transformDate($value): ?string
    {
        try {

            if ($value === null || $value === '') {
                return null;
            }

            /*
             * Excel serial date
             */
            if (is_numeric($value)) {

                return Carbon::instance(
                    Date::excelToDateTimeObject($value)
                )->format('Y-m-d');
            }

            /*
             * Format tanggal biasa
             */
            return Carbon::parse($value)->format('Y-m-d');

        } catch (\Throwable $e) {

            return null;
        }
    }
}
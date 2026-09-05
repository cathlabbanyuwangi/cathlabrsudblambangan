<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\PublicRegistration;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Schema;

class QueueCheckController extends Controller
{
    public function search(Request $request)
    {
        $request->validate([
            'keyword' => 'required|string|max:100',
        ], [
            'keyword.required' => 'Nomor Tiket, Rekam Medis, atau No. HP wajib diisi.'
        ]);

        $keyword = trim($request->keyword);
        $cleanKeyword = str_starts_with(strtoupper($keyword), 'REG-') 
            ? trim(str_ireplace('REG-', '', $keyword)) 
            : $keyword;

        // Tentukan sapaan waktu otomatis
        $hour = Carbon::now()->hour;
        if ($hour >= 4 && $hour < 11) {
            $greeting = 'selamat pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $greeting = 'selamat siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $greeting = 'selamat sore';
        } else {
            $greeting = 'selamat malam';
        }

        // --- 1. CARI DI TABEL UTAMA (PATIENTS) ---
        $columns = Schema::getColumnListing('patients');

        $patient = Patient::where(function($query) use ($keyword, $cleanKeyword, $columns) {
                // Tambahkan pencarian berdasarkan ticket_number pendek
                if (in_array('ticket_number', $columns)) {
                    $query->orWhere('ticket_number', 'like', "%{$keyword}%")
                          ->orWhere('ticket_number', $keyword);
                }
                if (in_array('registration_number', $columns)) {
                    $query->orWhere('registration_number', 'like', "%{$keyword}%")
                          ->orWhere('registration_number', $cleanKeyword);
                }
                if (in_array('registration_id', $columns)) {
                    $query->orWhere('registration_id', 'like', "%{$keyword}%")
                          ->orWhere('registration_id', $cleanKeyword);
                }
                if (in_array('medical_record_number', $columns)) {
                    $query->orWhere('medical_record_number', 'like', "%{$keyword}%");
                }
                if (in_array('patient_phone', $columns)) {
                    $query->orWhere('patient_phone', 'like', "%{$keyword}%");
                }
                if (in_array('family_phone', $columns)) {
                    $query->orWhere('family_phone', 'like', "%{$keyword}%");
                }
                if (is_numeric($cleanKeyword)) {
                    $query->orWhere('id', $cleanKeyword);
                }
            })
            ->first();

        // Jika data ditemukan di tabel patients
        if ($patient) {
            $genderVal = strtolower($patient->gender ?? '');
            $honorific = (in_array($genderVal, ['l', 'laki-laki', 'male', 'pria', 'm'])) ? 'Bapak' : 'Ibu';

            $currentStatus = strtolower(trim($patient->status ?? 'pending'));
            $estimationRange = null; 

            // Penentuan status type yang lebih akurat
            if (in_array($currentStatus, ['pending', 'verified', 'bersedia'])) {
                $statusType = 'approved'; 
                
                $regDate = $patient->created_at ? Carbon::parse($patient->created_at) : Carbon::now();
                $estStart = $regDate->copy()->addDays(14)->format('d M Y');
                $estEnd = $regDate->copy()->addDays(45)->format('d M Y');
                $estimationRange = "{$estStart} - {$estEnd}";
            } elseif (in_array($currentStatus, ['pernah_tindakan', 'menolak'])) {
                $statusType = 'completed'; 
            } else {
                $statusType = 'approved';
                $regDate = $patient->created_at ? Carbon::parse($patient->created_at) : Carbon::now();
                $estStart = $regDate->copy()->addDays(14)->format('d M Y');
                $estEnd = $regDate->copy()->addDays(45)->format('d M Y');
                $estimationRange = "{$estStart} - {$estEnd}";
            }

            // Gunakan ticket_number jika ada, fallback ke id jika belum ada
            $ticketId = $patient->ticket_number ?? ('REG-' . $patient->id);

            return back()->with([
                'checked_patient' => $patient,
                'estimation_range' => $estimationRange,
                'greeting' => $greeting,
                'honorific' => $honorific,
                'status_type' => $statusType,
                'registration_id' => $ticketId
            ])->withInput();
        }

        // --- 2. JIKA TIDAK ADA DI PATIENTS, CARI DI PUBLIC_REGISTRATIONS ---
        $registration = PublicRegistration::where(function($q) use ($keyword, $cleanKeyword) {
                $q->where('patient_phone', 'like', "%{$keyword}%")
                  ->orWhere('family_phone', 'like', "%{$keyword}%")
                  ->orWhere('medical_record_number', 'like', "%{$keyword}%");
                
                if (is_numeric($cleanKeyword)) {
                    $q->orWhere('id', $cleanKeyword);
                }
            })->first();

        if ($registration) {
            $genderVal = strtolower($registration->gender ?? '');
            $honorific = (in_array($genderVal, ['l', 'laki-laki', 'male', 'pria', 'm'])) ? 'Bapak' : 'Ibu';

            $regStatus = strtolower(trim($registration->status ?? 'pending'));
            $statusType = ($regStatus === 'pending') ? 'pending' : 'approved';

            $regDate = $registration->created_at ? Carbon::parse($registration->created_at) : Carbon::now();
            $estStart = $regDate->copy()->addDays(14)->format('d M Y');
            $estEnd = $regDate->copy()->addDays(45)->format('d M Y');
            $estimationRange = "{$estStart} - {$estEnd}";

            return back()->with([
                'checked_patient' => $registration,
                'estimation_range' => $estimationRange,
                'greeting' => $greeting,
                'honorific' => $honorific,
                'status_type' => $statusType,
                'registration_id' => 'REG-' . $registration->id
            ])->withInput();
        }

        // --- 3. JIKA TIDAK DITEMUKAN DI KEDUANYA ---
        return back()->with('error', 'Data tidak ditemukan. Pastikan Nomor Tiket, Rekam Medis, atau No. HP yang Anda masukkan sudah benar.');
    }

    /**
     * Proses pendaftaran ulang instan bagi pasien lama dengan opsi status prioritas.
     */
    public function reregister(Request $request, Patient $patient)
    {
        if (!in_array($patient->status, ['pernah_tindakan', 'menolak'])) {
            return back()->with('error', 'Pasien tidak memenuhi syarat untuk pendaftaran ulang instan.');
        }

        // Pastikan pasien memiliki ticket_number pendek
        if (empty($patient->ticket_number)) {
            $lastTicket = Patient::whereNotNull('ticket_number')->count() + 1;
            $patient->ticket_number = 'REG-' . date('y') . str_pad($lastTicket, 4, '0', STR_PAD_LEFT);
        }

        $patient->update([
            'status' => 'pending',
            'is_priority' => $request->has('is_priority') && $request->is_priority == '1' ? true : false,
            'willingness' => null,
            'action_date' => null,
            'rejection_date' => null,
            'unwillingness_reason' => null,
            'scheduled_at' => null,
            'called_by' => null,
            'called_at' => null,
            'updated_at' => now(),
        ]);

        return back()->with([
            'success' => 'Pendaftaran ulang berhasil! Data Anda telah ditarik kembali dan dipindahkan ke daftar Belum Dipanggil.',
            'registration_id' => $patient->ticket_number
        ]);
    }
}
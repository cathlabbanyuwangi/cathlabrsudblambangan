<?php

namespace App\Http\Controllers;

use App\Models\PublicRegistration;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PublicRegistrationController extends Controller
{
    /**
     * Tampilkan daftar pendaftaran publik yang masuk di panel admin.
     */
    public function index()
    {
        $registrations = PublicRegistration::latest()->paginate(15);
        
        return view('admin.public-registrations.index', compact('registrations'));
    }

    public function portalDaftar()
{
return view('patients.portal.daftar');
}
    /**
     * Simpan data pengajuan pendaftaran mandiri atau proses daftar ulang dari halaman publik.
     */
    public function store(Request $request)
    {
        // Helper untuk generate nomor tiket BARU (selalu membuat nomor antrean segar)
        $generateNewTicketNumber = function($patient) {
            $lastTicket = Patient::whereNotNull('ticket_number')->count() + 1;
            $patient->ticket_number = 'REG-' . date('y') . str_pad($lastTicket, 4, '0', STR_PAD_LEFT);
            $patient->save();
            return $patient->ticket_number;
        };

        // 1. Tangani proses DAFTAR ULANG / KONFIRMASI di baris PALING ATAS (Sebelum validasi)
        if ($request->input('confirmed') == '1') {
            // A. Jika dari modal pencarian jadwal (membawa patient_id)
            if ($request->has('patient_id')) {
                $patient = Patient::find($request->input('patient_id'));

                if ($patient) {
                    $patient->update([
                        'status' => 'pending',
                        'ticket_number' => null, // Reset agar mendapat nomor tiket baru
                        'willingness' => null,
                        'action_date' => null,
                        'rejection_date' => null,
                        'unwillingness_reason' => null,
                        'scheduled_at' => null,
                        'called_by' => null,
                        'called_at' => null,
                    ]);

                    $ticketNo = $generateNewTicketNumber($patient);

                    return back()->with([
                        'success' => true,
                        'registration_id' => $ticketNo,
                        'is_awaiting_verification' => true,
                        'registration_name' => $patient->name
                    ]);
                }
            }

            // B. Jika dari konfirmasi form publik (berdasarkan patient_phone)
            if ($request->filled('patient_phone')) {
                $phone = $request->patient_phone;
                $mrNumber = $request->medical_record_number;

                $existingPatient = Patient::where(function($q) use ($phone, $mrNumber) {
                    $q->where('patient_phone', $phone)
                      ->orWhere('family_phone', $phone);
                    if (!empty($mrNumber)) {
                        $q->orWhere('medical_record_number', $mrNumber);
                    }
                })->first();

                if ($existingPatient) {
                    $existingPatient->update([
                        'status' => 'pending',
                        'ticket_number' => null, // Reset agar mendapat nomor tiket baru
                        'willingness' => null,
                        'action_date' => null,
                        'rejection_date' => null,
                        'unwillingness_reason' => null,
                        'scheduled_at' => null,
                        'called_by' => null,
                        'called_at' => null,
                    ]);

                    $ticketNo = $generateNewTicketNumber($existingPatient);

                    return back()->with([
                        'success' => true,
                        'registration_id' => $ticketNo,
                        'is_awaiting_verification' => true,
                        'registration_name' => $existingPatient->name
                    ]);
                }
            }
        }

        // 2. Validasi standar untuk form pendaftaran mandiri baru dari halaman publik
        $validated = $request->validate([
            'source' => 'required|string|max:50',
            'medical_record_number' => 'nullable|string|max:50',
            'origin_hospital' => 'nullable|string|max:255',
            'origin_hospital_custom' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|string|max:10',
            'insurance_id' => 'required|exists:insurances,id',
            'patient_phone' => 'required|string|max:20',
            'family_phone' => 'nullable|string|max:20',
            'regency' => 'required|string|max:100',
            'district' => 'required|string|max:100',
            'address' => 'required|string',
            'supporting_options' => 'nullable|array',
            'supporting_options.*' => 'exists:supporting_options,id',
            'notes' => 'nullable|string',
        ]);

        $phone = $request->patient_phone;
        $mrNumber = $request->medical_record_number;

        // 3. Cek apakah pasien sudah ada di tabel utama (patients) berdasarkan No. HP atau No. RM
        $existingPatient = Patient::where(function($q) use ($phone, $mrNumber) {
            $q->where('patient_phone', $phone)
              ->orWhere('family_phone', $phone);
            if (!empty($mrNumber)) {
                $q->orWhere('medical_record_number', $mrNumber);
            }
        })->first();

        if ($existingPatient) {
            // Jika pasien masih aktif dalam antrean, langsung tampilkan modal jadwal dengan nomor tiket yang sudah ada
            if (in_array($existingPatient->status, ['pending', 'verified', 'bersedia'])) {
                $tglMulai = Carbon::now()->addDays(14)->format('d M Y');
                $tglSelesai = Carbon::now()->addDays(45)->format('d M Y');
                $rentangTanggal = $tglMulai . ' - ' . $tglSelesai;
                
                // Jika kebetulan belum punya tiket, buatkan
                if (empty($existingPatient->ticket_number)) {
                    $generateNewTicketNumber($existingPatient);
                }

                return back()->with([
                    'checked_patient' => $existingPatient,
                    'estimation_range' => $rentangTanggal,
                    'status_type' => $existingPatient->status,
                    'registration_id' => $existingPatient->ticket_number
                ]);
            }

            // Jika pasien sudah selesai tindakan ('pernah_tindakan') atau menolak ('menolak')
            if (in_array($existingPatient->status, ['pernah_tindakan', 'menolak'])) {
                if (!$request->has('confirmed') || $request->confirmed != '1') {
                    $lastDate = $existingPatient->updated_at ? Carbon::parse($existingPatient->updated_at)->translatedFormat('d F Y') : 'waktu lalu';
                    
                    return back()->with([
                        'need_confirmation' => true,
                        'confirmation_message' => "Halo " . $existingPatient->name . ", Anda tercatat sudah pernah melakukan tindakan pada tanggal " . $lastDate . ". Apakah Anda ingin mendaftar kembali untuk tindakan baru?"
                    ])->withInput();
                }
            }

            // JIKA SUDAH DI-CONFIRM ('confirmed' == '1'), UPDATE STATUS DAN BERIKAN TIKET BARU
            $existingPatient->update([
                'status' => 'pending',
                'ticket_number' => null, // Reset agar mendapat nomor tiket baru
                'willingness' => null,
                'action_date' => null,
                'rejection_date' => null,
                'unwillingness_reason' => null,
                'scheduled_at' => null,
                'called_by' => null,
                'called_at' => null,
            ]);

            $ticketNo = $generateNewTicketNumber($existingPatient);

            return back()->with([
                'success' => true,
                'registration_id' => $ticketNo,
                'is_awaiting_verification' => true,
                'registration_name' => $existingPatient->name
            ]);
        }

        // 4. Cek apakah nomor telepon sudah ada di antrean pending (public_registrations)
        $existsInPending = PublicRegistration::where('status', 'pending')
            ->where(function ($query) use ($phone) {
                $query->where('patient_phone', $phone)
                      ->orWhere('family_phone', $phone);
            })
            ->exists();

        if ($existsInPending) {
            return back()->with('error', 'Nomor Telepon ini sudah pernah mengajukan pendaftaran dan sedang menunggu verifikasi admin.');
        }

        // 5. Simpan data baru ke tabel public_registrations (pendaftaran publik baru)
        $registration = PublicRegistration::create($validated);

        return back()->with([
            'success' => true,
            'registration_id' => 'REG-' . $registration->id,
            'is_awaiting_verification' => true,
            'registration_name' => $registration->name
        ]);
    }

    /**
     * Setujui pendaftaran dan pindahkan data ke tabel utama patients.
     */
    public function approve(PublicRegistration $publicRegistration)
    {
        if ($publicRegistration->status === 'approved') {
            return back()->with('error', 'Pendaftaran ini sudah disetujui sebelumnya.');
        }

        $hospitalName = $publicRegistration->origin_hospital;
        if ($hospitalName === 'Lainnya' && !empty($publicRegistration->origin_hospital_custom)) {
            $hospitalName = $publicRegistration->origin_hospital_custom;
        }

        // Generate nomor tiket pendek otomatis (misal: REG-260001)
        $lastTicket = Patient::whereNotNull('ticket_number')->count() + 1;
        $ticketNumber = 'REG-' . date('y') . str_pad($lastTicket, 4, '0', STR_PAD_LEFT);

        $patient = Patient::create([
            'source' => $publicRegistration->source,
            'medical_record_number' => $publicRegistration->medical_record_number ?? 'RM-' . strtoupper(Str::random(6)),
            'origin_hospital' => $hospitalName,
            'name' => $publicRegistration->name,
            'date_of_birth' => $publicRegistration->date_of_birth,
            'gender' => $publicRegistration->gender,
            'insurance_id' => $publicRegistration->insurance_id,
            'patient_phone' => $publicRegistration->patient_phone,
            'family_phone' => $publicRegistration->family_phone,
            'regency' => $publicRegistration->regency,
            'district' => $publicRegistration->district,
            'address' => $publicRegistration->address,
            'notes' => $publicRegistration->notes,
            'status' => 'pending', 
            'ticket_number' => $ticketNumber,
        ]);

        if (!empty($publicRegistration->supporting_options)) {
            $patient->supportingOptions()->sync($publicRegistration->supporting_options);
        }

        $publicRegistration->update(['status' => 'approved']);

        return back()->with([
            'success' => 'Pasien berhasil diverifikasi dan masuk ke daftar Belum Dipanggil!',
            'new_patient' => $patient
        ]);
    }

    /**
     * Hapus atau tolak data pendaftaran publik yang masuk.
     */
    public function destroy(PublicRegistration $publicRegistration)
    {
        $publicRegistration->delete();
        
        return back()->with('success', 'Data pengajuan pendaftaran berhasil dihapus.');
    }
}
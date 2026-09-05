<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Action;
use App\Models\Doctor;
use App\Models\ActionCategory;
use App\Models\SubDivision;
use App\Models\ActionRecord;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ActionRecordsExport;
use App\Imports\ActionRecordsImport;

class ActionRecordController extends Controller
{
    public function index(Request $request)
    {
        $query = ActionRecord::with(['patient', 'doctor', 'action', 'category']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->whereHas('patient', function($p) use ($search) {
                    $p->where('name', 'like', "%{$search}%")
                      ->orWhere('medical_record_number', 'like', "%{$search}%");
                })->orWhereHas('doctor', function($d) use ($search) {
                    $d->where('name', 'like', "%{$search}%");
                })->orWhereHas('action', function($a) use ($search) {
                    $a->where('name', 'like', "%{$search}%");
                });
            });
        }

        // Mengurutkan berdasarkan action_date terbaru di atas, cadangan ke created_at
        $query->orderByRaw('COALESCE(action_date, created_at) DESC');

        $records = $query->paginate(20);

        if ($request->ajax()) {
            return view('actions.partials.table', compact('records'))->render();
        }

        return view('actions.index', compact('records'));
    }

    public function create(Patient $patient)
    {
        $categories = ActionCategory::all();
        $allActions = collect(); 

        // AMBIL DOKTER ANESTESI SECARA FLEKSIBEL (Sub-divisi, Kategori Utama, atau Gelar Sp.An)
        $anesthesiaDoctors = Doctor::where(function($q) {
            $q->whereHas('subDivision', function ($sub) {
                $sub->where('name', 'like', '%Anestesi%');
            })->orWhereHas('category', function ($cat) {
                $cat->where('name', 'like', '%Anestesi%');
            })->orWhere('name', 'like', '%Sp.An%');
        })->get();

        return view('patients.actions.create', compact('patient', 'categories', 'allActions', 'anesthesiaDoctors'));
    }

    public function store(Request $request, Patient $patient)
    {
        // Ubah input anesthesia_doctor_id yang kosong/string kosong menjadi null
        if (!$request->filled('anesthesia_doctor_id')) {
            $request->merge(['anesthesia_doctor_id' => null]);
        }

        $validated = $request->validate([
            'action_category_id' => 'required|exists:action_categories,id',
            'sub_division_id' => 'nullable|exists:sub_divisions,id',
            'action_id' => 'required|exists:actions,id',
            'doctor_id' => 'required|exists:doctors,id',
            'anesthesia_doctor_id' => 'nullable|exists:doctors,id',
            'action_date' => 'required|date',
            'origin_ward' => 'required|string|max:255',
            'ring_count' => 'nullable|integer',
            'diagnosis_1' => 'required|string|max:255',
            'diagnosis_2' => 'nullable|string|max:255',
            'diagnosis_3' => 'nullable|string|max:255',
            'conclusion' => 'required|string',
            'suggestion' => 'required|string',
            'notes' => 'nullable|string',
            // Variabel Tambahan Akreditasi & Door-to-Balloon
            'arrived_hospital_at' => 'nullable|date',
            'balloon_inflation_at' => 'nullable|date',
            'complication_notes' => 'nullable|string|max:500',
        ]);

        $validated['is_cito'] = $request->boolean('is_cito');
        $validated['is_successful'] = $request->boolean('is_successful', true); // Default sukses kecuali dicabut

        // Konversi format tanggal datetime-local HTML ke format SQL (Y-m-d H:i:s)
        if ($request->filled('action_date')) {
            $validated['action_date'] = date('Y-m-d H:i:s', strtotime($request->action_date));
        }

        if ($request->filled('arrived_hospital_at')) {
            $validated['arrived_hospital_at'] = date('Y-m-d H:i:s', strtotime($request->arrived_hospital_at));
        }

        if ($request->filled('balloon_inflation_at')) {
            $validated['balloon_inflation_at'] = date('Y-m-d H:i:s', strtotime($request->balloon_inflation_at));
        }

        // 1. Simpan rekam medis tindakan
        $patient->actionRecords()->create($validated);

        // 2. Update status pasien menjadi pernah tindakan dan catat tanggalnya
        $patient->update([
            'status' => 'pernah_tindakan',
            'action_date' => $request->filled('action_date') ? date('Y-m-d', strtotime($request->action_date)) : now()->toDateString(),
            'arrived_hospital_at' => $request->filled('arrived_hospital_at') ? date('Y-m-d H:i:s', strtotime($request->arrived_hospital_at)) : $patient->arrived_hospital_at,
        ]);

        return redirect()
            ->route('patients.actions-history', $patient->id)
            ->with('success', 'Tindakan berhasil dicatat dan status pasien diperbarui!');
    }

    public function show(Patient $patient, ActionRecord $actionRecord)
    {
        $actionRecord->load(['doctor.subDivision', 'category', 'action']);
        
        $anesthesiaDoctor = $actionRecord->anesthesia_doctor_id 
            ? Doctor::find($actionRecord->anesthesia_doctor_id) 
            : null;

        return view('patients.actions.show', compact('patient', 'actionRecord', 'anesthesiaDoctor'));
    }

    public function edit(Patient $patient, ActionRecord $actionRecord)
    {
        $categories = ActionCategory::all();

        $subDivisions = SubDivision::where('action_category_id', $actionRecord->action_category_id)->get();
        $doctors = Doctor::where('sub_division_id', $actionRecord->sub_division_id)->get();

        $doctor = Doctor::find($actionRecord->doctor_id);
        if ($doctor) {
            $nativeActions = Action::where('action_category_id', $doctor->action_category_id)->get();
            $allowedActions = $doctor->actions;
            $allActions = $nativeActions->merge($allowedActions)->unique('id')->values();
        } else {
            $allActions = Action::all();
        }

        // AMBIL DOKTER ANESTESI SECARA FLEKSIBEL UNTUK HALAMAN EDIT
        $anesthesiaDoctors = Doctor::where(function($q) {
            $q->whereHas('subDivision', function ($sub) {
                $sub->where('name', 'like', '%Anestesi%');
            })->orWhereHas('category', function ($cat) {
                $cat->where('name', 'like', '%Anestesi%');
            })->orWhere('name', 'like', '%Sp.An%');
        })->get();

        return view('patients.actions.edit', compact(
            'patient', 
            'actionRecord', 
            'categories', 
            'subDivisions', 
            'doctors', 
            'allActions',
            'anesthesiaDoctors'
        ));
    }

    public function update(Request $request, Patient $patient, ActionRecord $actionRecord)
    {
        // Ubah input anesthesia_doctor_id yang kosong/string kosong menjadi null
        if (!$request->filled('anesthesia_doctor_id')) {
            $request->merge(['anesthesia_doctor_id' => null]);
        }

        $validated = $request->validate([
            'action_category_id' => 'required|exists:action_categories,id',
            'sub_division_id' => 'nullable|exists:sub_divisions,id',
            'action_id' => 'required|exists:actions,id',
            'doctor_id' => 'required|exists:doctors,id',
            'anesthesia_doctor_id' => 'nullable|exists:doctors,id',
            'action_date' => 'required|date',
            'origin_ward' => 'required|string|max:255',
            'ring_count' => 'nullable|integer',
            'diagnosis_1' => 'required|string|max:255',
            'diagnosis_2' => 'nullable|string|max:255',
            'diagnosis_3' => 'nullable|string|max:255',
            'conclusion' => 'required|string',
            'suggestion' => 'required|string',
            'notes' => 'nullable|string',
            // Variabel Tambahan Akreditasi & Door-to-Balloon
            'arrived_hospital_at' => 'nullable|date',
            'balloon_inflation_at' => 'nullable|date',
            'complication_notes' => 'nullable|string|max:500',
        ]);

        $validated['is_cito'] = $request->boolean('is_cito');
        $validated['is_successful'] = $request->boolean('is_successful', true);

        // Konversi format tanggal update
        if ($request->filled('action_date')) {
            $validated['action_date'] = date('Y-m-d H:i:s', strtotime($request->action_date));
        }

        if ($request->filled('arrived_hospital_at')) {
            $validated['arrived_hospital_at'] = date('Y-m-d H:i:s', strtotime($request->arrived_hospital_at));
        }

        if ($request->filled('balloon_inflation_at')) {
            $validated['balloon_inflation_at'] = date('Y-m-d H:i:s', strtotime($request->balloon_inflation_at));
        }

        $actionRecord->update($validated);

        return redirect()
            ->route('patients.actions-history', $patient->id)
            ->with('success', 'Tindakan berhasil diperbarui!');
    }

    public function destroy(Patient $patient, ActionRecord $actionRecord)
    {
        $actionRecord->delete();
        return back()->with('success', 'Tindakan berhasil dihapus!');
    }

    public function editDoorToBalloon(Patient $patient, ActionRecord $actionRecord)
    {
        return view('actions.door-to-balloon', compact('patient', 'actionRecord'));
    }

    public function updateDoorToBalloon(Request $request, Patient $patient, ActionRecord $actionRecord)
    {
        $validated = $request->validate([
            'diagnosis_d2b' => 'nullable|string|max:255',
            'd2b_igd_time' => 'nullable|date',
            'd2b_igd_officer' => 'nullable|string|max:255',
            'd2b_igd_notes' => 'nullable|string',
            'd2b_triage_time' => 'nullable|date',
            'd2b_triage_officer' => 'nullable|string|max:255',
            'd2b_triage_notes' => 'nullable|string',
            'd2b_ecg_time' => 'nullable|date',
            'd2b_ecg_officer' => 'nullable|string|max:255',
            'd2b_ecg_notes' => 'nullable|string',
            'd2b_assessment_time' => 'nullable|date',
            'd2b_assessment_officer' => 'nullable|string|max:255',
            'd2b_assessment_notes' => 'nullable|string',
            'd2b_diagnosis_est_time' => 'nullable|date',
            'd2b_diagnosis_est_officer' => 'nullable|string|max:255',
            'd2b_diagnosis_est_notes' => 'nullable|string',
            'd2b_ppci_consult_time' => 'nullable|date',
            'd2b_ppci_consult_officer' => 'nullable|string|max:255',
            'd2b_ppci_consult_notes' => 'nullable|string',
            'd2b_family_info_time' => 'nullable|date',
            'd2b_family_info_officer' => 'nullable|string|max:255',
            'd2b_family_info_notes' => 'nullable|string',
            'd2b_family_approval_time' => 'nullable|date',
            'd2b_family_approval_officer' => 'nullable|string|max:255',
            'd2b_family_approval_notes' => 'nullable|string',
            'd2b_to_cathlab_time' => 'nullable|date',
            'd2b_to_cathlab_officer' => 'nullable|string|max:255',
            'd2b_to_cathlab_notes' => 'nullable|string',
            'd2b_arrival_cathlab_time' => 'nullable|date',
            'd2b_arrival_cathlab_officer' => 'nullable|string|max:255',
            'd2b_arrival_cathlab_notes' => 'nullable|string',
            'd2b_proc_start_time' => 'nullable|date',
            'd2b_proc_start_officer' => 'nullable|string|max:255',
            'd2b_proc_start_notes' => 'nullable|string',
            'd2b_other_action_time' => 'nullable|date',
            'd2b_other_action_officer' => 'nullable|string|max:255',
            'd2b_other_action_notes' => 'nullable|string',
            'd2b_balloon_dilatation_time' => 'nullable|date',
            'd2b_balloon_dilatation_officer' => 'nullable|string|max:255',
            'd2b_balloon_dilatation_notes' => 'nullable|string',
            'd2b_proc_finish_time' => 'nullable|date',
            'd2b_proc_finish_officer' => 'nullable|string|max:255',
            'd2b_proc_finish_notes' => 'nullable|string',
            'd2b_room_transfer_time' => 'nullable|date',
            'd2b_room_transfer_officer' => 'nullable|string|max:255',
            'd2b_room_transfer_notes' => 'nullable|string',
            'd2b_general_notes' => 'nullable|string',
            'd2b_verified_name' => 'nullable|string|max:255',
            'd2b_verified_nip' => 'nullable|string|max:100',
        ]);

        $actionRecord->update($validated);

        return redirect()
            ->route('patients.actions-history', $patient->id)
            ->with('success', 'Lembar Audit Medik Door-to-Balloon berhasil disimpan!');
    }

    public function printDoorToBalloon(Patient $patient, ActionRecord $actionRecord)
    {
        $action = $actionRecord->load('patient');
        return view('actions.print-d2b', compact('action'));
    }

    public function getSubDivisionsByCategory(int $id): JsonResponse
    {
        return response()->json(SubDivision::where('action_category_id', $id)->get());
    }

    public function getDoctorsBySubDivision(int $id): JsonResponse
    {
        return response()->json(Doctor::where('sub_division_id', $id)->get());
    }

    public function getActionsByDoctor(Doctor $doctor): JsonResponse
    {
        $nativeActions = Action::where('action_category_id', $doctor->action_category_id)->get();
        $allowedActions = $doctor->actions;
        $combinedActions = $nativeActions->merge($allowedActions)->unique('id')->values();

        return response()->json($combinedActions);
    }

    public function export()
    {
        return Excel::download(new ActionRecordsExport, 'riwayat-tindakan-' . date('Y-m-d') . '.xlsx');
    }

    public function downloadTemplate()
    {
        return Excel::download(new \App\Exports\ActionRecordsTemplateExport, 'template-import-riwayat-tindakan.xlsx');
    }

    // =======================================================================
    // INI ADALAH FUNGSI IMPORT YANG BARU (BEBAS ERROR PATH CANNOT BE EMPTY)
    // =======================================================================
    public function import(Request $request)
    {
        // 1. Validasi
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_action_' . $file->getClientOriginalName();
            
            // 2. Pindahkan file fisik secara paksa ke folder public/temp_imports
            $destinationPath = public_path('temp_imports');
            
            // Jika foldernya belum ada, buat otomatis
            if (!file_exists($destinationPath)) {
                mkdir($destinationPath, 0775, true);
            }
            
            $file->move($destinationPath, $filename);
            
            // 3. Dapatkan path absolut 
            $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;

            // 4. MENGAKALI BUG EXCEL: Paksa library menggunakan folder kita
            config(['excel.temporary_files.local_path' => $destinationPath]);

            // 5. Eksekusi import dengan path yang sudah dijamin ada
            Excel::import(new ActionRecordsImport, $fullPath);

            // 6. Hapus file sementara
            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return back()->with('success', 'Data riwayat tindakan berhasil diimpor!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris ke-{$failure->row()}: " . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Gagal validasi Excel: ' . implode(' | ', $errorMessages));

        } catch (\Throwable $e) {
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            \Log::error('ActionRecord Import Error: ' . $e->getMessage());
            return back()->with('error', 'Kesalahan Sistem: ' . $e->getMessage());
        }
    }
}
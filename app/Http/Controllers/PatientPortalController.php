<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Patient;
use App\Models\ActionRecord;
use App\Models\PatientDocument;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class PatientPortalController extends Controller
{
    public function showForm()
    {
        if (session()->has('patient_id')) {
            return redirect()->route('patient.portal.documents');
        }
        return view('patients.portal.verify');
    }

    public function authenticate(Request $request)
    {
        $request->validate([
            'medical_record_number' => 'required',
            'portal_token' => 'required|size:6',
        ]);

        $patient = Patient::where('medical_record_number', trim($request->medical_record_number))
                          ->where('portal_token', trim($request->portal_token))
                          ->first();

        if (!$patient) {
            return back()->with('error', 'Nomor Rekam Medis atau Token 6 Digit tidak valid.');
        }

        session([
            'patient_id' => $patient->id,
            'patient_rm' => $patient->medical_record_number,
            'patient_name' => $patient->name
        ]);

        return redirect()->route('patient.portal.documents');
    }

    /**
     * Login otomatis menggunakan token unik dari admin (URL Route)
     */
    public function loginWithToken($token)
    {
        $patient = Patient::where('portal_token', $token)->first();

        if (!$patient) {
            return redirect()->route('patient.portal.login')->with('error', 'Token akses tidak valid atau sudah kedaluwarsa.');
        }

        session([
            'patient_id' => $patient->id,
            'patient_rm' => $patient->medical_record_number,
            'patient_name' => $patient->name
        ]);

        return redirect()->route('patient.portal.documents');
    }

    public function documentsList()
    {
        $patientId = session('patient_id');
        if (!$patientId) {
            return redirect()->route('patient.portal.login');
        }

        $patient = Patient::with(['documents', 'actionRecords.action', 'actionRecords.category', 'actionRecords.doctor'])->find($patientId);

        return view('patients.portal.documents', compact('patient'));
    }

    public function downloadSelected(Request $request)
    {
        $patientId = session('patient_id');
        if (!$patientId) {
            return redirect()->route('patient.portal.login');
        }

        $selectedActionIds = $request->input('selected_actions', []);
        $selectedDocIds = $request->input('selected_documents', []);

        if (empty($selectedActionIds) && empty($selectedDocIds)) {
            return back()->with('error', 'Silakan centang minimal satu tindakan atau dokumen yang ingin diunduh.');
        }

        $patient = Patient::find($patientId);

        if (empty($selectedActionIds) && !empty($selectedDocIds)) {
            $documents = $patient->documents()->whereIn('id', $selectedDocIds)->get();

            if (count($documents) === 1) {
                $doc = $documents->first();
                $filePath = storage_path('app/public/' . $doc->file_path);
                
                if (file_exists($filePath)) {
                    return response()->download($filePath, $doc->document_name . '.' . pathinfo($filePath, PATHINFO_EXTENSION));
                }
                return back()->with('error', 'File fisik dokumen tidak ditemukan di server.');
            }

            $zipName = 'Dokumen_Medis_' . $patient->medical_record_number . '_' . date('Y-m-d') . '.zip';
            $zipPath = public_path($zipName);

            $zip = new ZipArchive;
            if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                foreach ($documents as $doc) {
                    $filePath = storage_path('app/public/' . $doc->file_path);
                    if (file_exists($filePath)) {
                        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
                        $safeName = preg_replace('/[^A-Za-z0-9-_]/', '_', $doc->document_name) . '.' . $extension;
                        $zip->addFile($filePath, $safeName);
                    }
                }
                $zip->close();
            }

            return response()->download($zipPath)->deleteFileAfterSend(true);
        }

        $actionRecords = ActionRecord::with(['action', 'category', 'doctor'])
            ->where('patient_id', $patientId)
            ->whereIn('id', $selectedActionIds)
            ->get();

        $documents = $patient->documents()->whereIn('id', $selectedDocIds)->get();

        $pdf = Pdf::loadView('patient.portal.pdf_template', compact('patient', 'actionRecords', 'documents'));

        return $pdf->download('Ringkasan_Tindakan_' . $patient->medical_record_number . '_' . date('Y-m-d') . '.pdf');
    }

    /**
     * Unduh aman berkas fisik individu menggunakan Signed URL (Token Kedaluwarsa)
     */
    public function downloadSecure(Request $request, PatientDocument $patientDocument)
    {
        if (session('patient_id') !== $patientDocument->patient_id) {
            abort(403, 'Akses ditolak. Dokumen ini bukan milik Anda.');
        }

        $filePath = storage_path('app/public/' . $patientDocument->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File fisik dokumen tidak ditemukan di server.');
        }

        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $safeName = preg_replace('/[^A-Za-z0-9-_]/', '_', $patientDocument->document_name) . '.' . $extension;

        return response()->download($filePath, $safeName);
    }

    public function securePreview(Request $request, PatientDocument $patientDocument)
    {
        $filePath = storage_path('app/public/' . ($patientDocument->file_path ?? $patientDocument->path));

        if (!file_exists($filePath)) {
            abort(404, 'File fisik dokumen tidak ditemukan di server.');
        }

        return response()->file($filePath);
    }

    public function logout()
    {
        session()->forget(['patient_id', 'patient_rm', 'patient_name']);
        return redirect()->route('patient.portal.login');
    }
}
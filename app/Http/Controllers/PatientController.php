<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Insurance;
use App\Models\SupportingOption;
use App\Models\User;
use App\Models\Doctor;
use App\Models\SubDivision;
use App\Models\Action;
use App\Models\ActionCategory;
use App\Models\ActionRecord;
use App\Models\PatientBhp;
use App\Models\PatientDocument;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

use Maatwebsite\Excel\Facades\Excel;

use App\Exports\PatientsExport;
use App\Exports\PatientsTemplateExport;
use App\Imports\PatientsImport;

use Smalot\PdfParser\Parser;
use Carbon\Carbon;

class PatientController extends Controller
{
    /**
     * ============================================================
     * GENERATE TICKET NUMBER
     * ============================================================
     */
    private function generateTicketNumber(Patient $patient): string
    {
        if (empty($patient->ticket_number)) {
            $lastTicket = Patient::whereNotNull('ticket_number')->count() + 1;
            $patient->ticket_number = 'REG-' . date('y') . str_pad($lastTicket, 4, '0', STR_PAD_LEFT);
            $patient->save();
        }
        return $patient->ticket_number;
    }

    /**
     * ============================================================
     * INDEX PASIEN
     * ============================================================
     */
    public function index(Request $request)
    {
        $activeTab = $request->query('tab', 'belum_dipanggil');
        $query = Patient::with(['insurance', 'supportingOptions', 'caller']);

        if ($activeTab === 'sudah_dipanggil') {
            $query->where('status', 'pernah_tindakan');
        } elseif ($activeTab === 'menolak') {
            $query->where('status', 'menolak');
        } else {
            $query->whereIn('status', ['pending', 'verified']);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('medical_record_number', 'like', "%{$search}%")
                  ->orWhere('patient_phone', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        if ($activeTab === 'sudah_dipanggil' || $activeTab === 'menolak') {
            $patients = $query->orderBy('updated_at', 'desc')->get();
        } else {
            $patients = $query->orderBy('is_priority', 'desc')
                            ->orderBy('created_at', 'desc')
                            ->get();
        }

        $users = User::all();

        if ($request->ajax()) {
            return view('patients.partials.table', compact('patients', 'users'))->render();
        }

        return view('patients.index', compact('patients', 'users', 'activeTab'));
    }

    /**
     * ============================================================
     * ACTION QUEUE
     * ============================================================
     */
    public function actionQueue(Request $request)
    {
        $activeTab = 'antre_tindakan';
        $query = Patient::with(['insurance', 'supportingOptions', 'caller'])
            ->where('status', 'bersedia');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('medical_record_number', 'like', "%{$search}%")
                  ->orWhere('patient_phone', 'like', "%{$search}%")
                  ->orWhere('ticket_number', 'like', "%{$search}%");
            });
        }

        $patients = $query->orderBy('scheduled_at', 'asc')->get();
        $users = User::all();

        if ($request->ajax()) {
            return view('patients.partials.table', compact('patients', 'users'))->render();
        }

        return view('patients.queue', compact('patients', 'users', 'activeTab'));
    }

    /**
     * ============================================================
     * CREATE & STORE PASIEN
     * ============================================================
     */
    public function create()
    {
        $insurances = Insurance::all();
        $supportingOptions = SupportingOption::all();
        return view('patients.create', compact('insurances', 'supportingOptions'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'source' => 'required|string',
            'origin_hospital' => 'nullable|string|max:255',
            'origin_hospital_custom' => 'nullable|string|max:255',
            'medical_record_number' => 'nullable|string|unique:patients,medical_record_number',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:L,P',
            'address' => 'required|string',
            'regency' => 'required|string',
            'district' => 'required|string',
            'patient_phone' => 'nullable|string|max:20',
            'family_phone' => 'nullable|string|max:20',
            'insurance_id' => 'required|exists:insurances,id',
            'notes' => 'nullable|string',
            'is_priority' => 'nullable|boolean',
            'supporting_options' => 'array',
        ], [
            'medical_record_number.unique' => 'Gagal! Nomor RM ini sudah terdaftar di sistem.',
        ]);

        $data = $request->except(['supporting_options', 'origin_hospital_custom']);

        if ($request->origin_hospital === 'Klinik / RS Lainnya') {
            $data['origin_hospital'] = $request->origin_hospital_custom;
        }

        $lastTicket = Patient::whereNotNull('ticket_number')->count() + 1;
        $data['ticket_number'] = 'REG-' . date('y') . str_pad($lastTicket, 4, '0', STR_PAD_LEFT);
        $data['is_priority'] = $request->has('is_priority');
        $data['status'] = 'pending';

        $patient = Patient::create($data);

        if ($request->has('supporting_options')) {
            $patient->supportingOptions()->attach($request->supporting_options);
        }

        return redirect()->route('patients.index')->with('success', 'Pendaftaran pasien berhasil disimpan! Silakan lakukan pemanggilan.');
    }

    /**
     * ============================================================
     * SHOW & ACTIONS HISTORY
     * ============================================================
     */
    public function show(Patient $patient)
    {
        $patient->load(['insurance', 'caller', 'supportingOptions']);
        return view('patients.show', compact('patient'));
    }

    public function callHistory(Patient $patient)
    {
        $patient->load(['insurance', 'caller']);
        return view('patients.partials.call-history', compact('patient'));
    }

    public function actionsHistory(Patient $patient)
    {
        $actions = $patient->actionRecords()
            ->with(['action', 'category', 'doctor.subDivision'])
            ->orderByRaw('COALESCE(action_date, created_at) DESC')
            ->get();

        $patient->load(['insurance', 'caller']);
        return view('patients.partials.actions-history', compact('patient', 'actions'));
    }

    public function actionDetail(Patient $patient, ActionRecord $actionRecord)
    {
        $actionRecord->load(['doctor.subDivision', 'category', 'action']);
        $anesthesiaDoctor = $actionRecord->anesthesia_doctor_id 
            ? Doctor::find($actionRecord->anesthesia_doctor_id) 
            : null;

        return view('patients.actions.show', compact('patient', 'actionRecord', 'anesthesiaDoctor'));
    }

    /**
     * ============================================================
     * EDIT & UPDATE ACTION
     * ============================================================
     */
    public function editAction(Patient $patient, ActionRecord $actionRecord)
    {
        $categories = ActionCategory::all();
        $subDivisions = SubDivision::all();
        $doctors = Doctor::all();
        $allActions = Action::all();
        $anesthesiaDoctors = Doctor::where('is_anesthesia', true)->get();

        return view('patients.actions.edit', compact(
            'patient', 'actionRecord', 'categories', 'subDivisions', 'doctors', 'allActions', 'anesthesiaDoctors'
        ));
    }

    public function updateAction(Request $request, Patient $patient, ActionRecord $actionRecord)
    {
        $request->validate([
            'action_category_id' => 'required|exists:action_categories,id',
            'sub_division_id' => 'required|exists:sub_divisions,id',
            'doctor_id' => 'required|exists:doctors,id',
            'action_id' => 'required|exists:actions,id',
            'origin_ward' => 'required|string|max:255',
            'action_date' => 'required|date',
            'diagnosis_1' => 'required|string|max:255',
            'conclusion' => 'required|string',
            'suggestion' => 'required|string',
            'timi_flow_post' => 'nullable|integer',
            'contrast_volume' => 'nullable|integer',
            'fluro_time' => 'nullable|numeric',
            'is_successful' => 'nullable|boolean',
            'is_cito' => 'nullable|boolean',
            'arrived_hospital_at' => 'nullable|required_if:is_cito,1|date',
            'balloon_inflation_at' => 'nullable|required_if:is_cito,1|date',
        ]);

        $data = $request->all();
        $data['is_cito'] = $request->has('is_cito');
        $data['is_successful'] = $request->has('is_successful');

        $actionRecord->update($data);

        return redirect()->route('patients.actions-history', $patient->id)->with('success', 'Catatan tindakan medis berhasil diperbarui!');
    }

    /**
     * ============================================================
     * TAMPILAN & HAPUS BHP
     * ============================================================
     */
    public function bhp(Patient $patient)
    {
        $patient->load(['insurance', 'caller']);
        $bhpsGrouped = $patient->bhps()->orderBy('action_date', 'desc')->get()->groupBy('receipt_number');

        return view('patients.partials.bhp', compact('patient', 'bhpsGrouped'));
    }

    public function destroyBhp(Patient $patient, string $receiptNumber)
    {
        try {
            PatientBhp::where('patient_id', $patient->id)
                ->where('receipt_number', $receiptNumber)
                ->delete();

            return back()->with('success', 'Data nota BHP berhasil dihapus!');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus data BHP: ' . $e->getMessage());
        }
    }

    /**
     * ============================================================
     * IMPORT BHP PDF (NOMOR RESEP DIAMBIL PRESISI DARI PDF)
     * ============================================================
     */
    public function importBhpPdf(Request $request, Patient $patient)
    {
        $request->validate([
            'pdf_file' => 'required|file|mimes:pdf|max:2048',
        ]);

        try {
            $file = $request->file('pdf_file');
            if (!$file || !$file->isValid()) {
                return back()->with('error', 'File PDF tidak valid atau gagal diunggah.');
            }

            $parser = new Parser();
            $pdf = $parser->parseFile($file->getPathname());
            $text = $pdf->getText();

            if (trim($text) === '') {
                return back()->with('error', 'PDF berhasil dibuka tetapi tidak memiliki teks yang dapat dibaca.');
            }

            $normalizedText = str_replace(["\r\n", "\r", "\n"], " ", $text);
            $normalizedText = preg_replace('/\s+/', ' ', $normalizedText);
            $normalizedText = trim($normalizedText);

            $receiptNumber = null;
            
            if (preg_match('/No\s*Resep\D*([0-9A-Za-z\-\/]{5,25})/i', $normalizedText, $match)) {
                $receiptNumber = trim($match[1]);
            } 
            elseif (preg_match('/([0-9]{10,15})\s*No\s*Resep/i', $normalizedText, $match)) {
                $receiptNumber = trim($match[1]);
            }
            elseif (preg_match('/\b(0[1-9][0-9]{10,15})\b/', $normalizedText, $match)) {
                $receiptNumber = trim($match[1]);
            }

            if (!$receiptNumber || strlen($receiptNumber) < 4) {
                $receiptNumber = 'RESEP-' . substr(md5($normalizedText), 0, 8);
            }

            $isDuplicate = PatientBhp::where('patient_id', $patient->id)
                ->where('receipt_number', $receiptNumber)
                ->exists();

            if ($isDuplicate) {
                return back()->with('error', 'Gagal! Resep dengan Nomor (' . $receiptNumber . ') sudah pernah diunggah sebelumnya.');
            }

            $actionDate = now();
            if (preg_match('/Tanggal\s*:\s*([0-9]{1,2}\s+[A-Za-z]+\s+[0-9]{4})/i', $normalizedText, $dateMatches)) {
                try {
                    $actionDate = Carbon::parse(trim($dateMatches[1]));
                } catch (\Throwable $e) {
                    $actionDate = now();
                }
            }

            $itemBlock = $normalizedText;
            if (preg_match('/(?:SUBTOTAL|HARGA SATUAN SUBTOTAL)(.*?)TOTAL/iu', $normalizedText, $match)) {
                $itemBlock = trim($match[1]);
            }

            $unitPattern = '(?:PCS|AMPUL|CC|BOX|BOTOL|FLASH|OXYFLOW|TERUMO|POLI\s+JANTUNG|DERMAFIX\s*T|VIAL|MILILITER|TUBE|SET|PACK|ROLL|LEMBAR|PASANG|KOTAK|TABLET|KAPSUL|STRIP|SACHET|METER|\d+(?:[.,]\d+)?\s*CM\s*x\s*\d+(?:[.,]\d+)?\s*M|[A-Za-z]+)';
            
            $pattern = '/(?:^|\s)(?P<no>\d+)\s+(?P<name>.*?)\s+(?P<qty>\d+(?:[.,]\d+)?)\s+(?P<unit>' . $unitPattern . ')\s+(?P<price>[\d,.]+)\s+(?P<subtotal>[\d,.]+)(?=\s+\d+\s+|$)/iu';

            preg_match_all($pattern, $itemBlock, $matches, PREG_SET_ORDER);
            $parsedItems = [];

            foreach ($matches as $match) {
                $name = trim($match['name']);
                $name = preg_replace('/^\d+\s+/', '', $name);

                $parsedItems[] = [
                    'number'     => (int) $match['no'],
                    'item_name'  => substr($name, 0, 245), 
                    'quantity'   => $this->parseBhpQuantity($match['qty']),
                    'unit'       => trim($match['unit']),
                    'unit_price' => $this->parseBhpNumber($match['price']),
                    'subtotal'   => $this->parseBhpNumber($match['subtotal']),
                ];
            }

            if (empty($parsedItems)) {
                return back()->with('error', 'Tidak ada rincian BHP yang terdeteksi dari nota resep ini.');
            }

            DB::transaction(function () use ($parsedItems, $patient, $receiptNumber, $actionDate) {
                foreach ($parsedItems as $parsedItem) {
                    PatientBhp::create([
                        'patient_id'     => $patient->id,
                        'receipt_number' => $receiptNumber,
                        'action_date'    => $actionDate,
                        'item_name'      => $parsedItem['item_name'],
                        'quantity'       => $parsedItem['quantity'],
                        'unit_price'     => $parsedItem['unit_price'],
                        'subtotal'       => $parsedItem['subtotal'],
                    ]);
                }
            });

            return back()->with('success', 'Berhasil mengimpor ' . count($parsedItems) . ' item dari Nomor Resep: ' . $receiptNumber . '!');

        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal memproses file PDF: ' . $e->getMessage());
        }
    }

    private function parseBhpNumber($value): float
    {
        $value = trim((string) $value);
        if ($value === '') return 0;
        
        $value = str_replace(' ', '', $value);

        if (preg_match('/^\d{1,3}(?:\.\d{3})+$/', $value)) {
            return (float) str_replace('.', '', $value);
        }

        if (preg_match('/^\d{1,3}(?:,\d{3})+$/', $value)) {
            return (float) str_replace(',', '', $value);
        }

        if (preg_match('/^\d+,\d+$/', $value)) {
            return (float) str_replace(',', '.', $value);
        }

        return (float) str_replace(',', '', $value);
    }

    private function parseBhpQuantity($value): float
    {
        $value = trim((string) $value);
        if ($value === '') return 1;

        if (preg_match('/^\d+$/', $value)) {
            return (float) $value;
        }

        if (preg_match('/^\d+,\d+$/', $value)) {
            return (float) str_replace(',', '.', $value);
        }

        return (float) $value;
    }

    public function edit(Patient $patient)
    {
        $insurances = Insurance::all();
        $supportingOptions = SupportingOption::all();
        $patient->load('supportingOptions');

        return view('patients.edit', compact('patient', 'insurances', 'supportingOptions'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'source' => 'required|string',
            'origin_hospital' => 'nullable|string|max:255',
            'origin_hospital_custom' => 'nullable|string|max:255',
            'medical_record_number' => 'nullable|string|unique:patients,medical_record_number,' . $patient->id,
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:L,P',
            'address' => 'required|string',
            'regency' => 'required|string',
            'district' => 'required|string',
            'patient_phone' => 'nullable|string|max:20',
            'insurance_id' => 'required|exists:insurances,id',
            'notes' => 'nullable|string',
            'is_priority' => 'nullable|boolean',
            'supporting_options' => 'array',
        ]);

        $data = $request->except(['supporting_options', 'origin_hospital_custom']);

        if ($request->origin_hospital === 'Klinik / RS Lainnya') {
            $data['origin_hospital'] = $request->origin_hospital_custom;
        }

        $data['is_priority'] = $request->has('is_priority');

        $patient->update($data);

        if ($request->has('supporting_options')) {
            $patient->supportingOptions()->sync($request->supporting_options);
        } else {
            $patient->supportingOptions()->detach();
        }

        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil diperbarui!');
    }

    public function callPatient(Request $request, Patient $patient)
    {
        $request->validate([
            'status' => 'required|in:bersedia,menolak',
            'called_by' => 'required|exists:users,id',
            'scheduled_at' => 'required_if:status,bersedia|nullable|date',
            'unwillingness_reason' => 'required_if:status,menolak|nullable|string|max:255',
        ]);

        $isBersedia = $request->status === 'bersedia';

        $patient->update([
            'status' => $isBersedia ? 'bersedia' : 'menolak',
            'willingness' => $isBersedia ? 'bersedia' : 'tidak_bersedia',
            'called_by' => $request->called_by,
            'called_at' => now(),
            'scheduled_at' => $isBersedia ? $request->scheduled_at : null,
            'unwillingness_reason' => $isBersedia ? null : $request->unwillingness_reason,
            'rejection_date' => $isBersedia ? null : now()->toDateString(),
            'is_priority' => false,
        ]);

        if ($isBersedia) {
            return redirect()->route('patients.action-queue')->with('success', 'Pasien berhasil dimasukkan ke Antre Tindakan!');
        }

        return redirect()->route('patients.index', ['tab' => 'menolak'])->with('success', 'Status pasien diperbarui menjadi Menolak.');
    }

    public function resetPatientStatus(Patient $patient)
    {
        $patient->update([
            'status' => 'pending',
            'willingness' => null,
            'action_date' => null,
            'rejection_date' => null,
            'unwillingness_reason' => null,
            'scheduled_at' => null,
            'called_by' => null,
            'called_at' => null,
        ]);

        return back()->with('success', 'Status pasien direset.');
    }

    public function reregister(Request $request, Patient $patient)
    {
        $lastTicket = Patient::whereNotNull('ticket_number')->count() + 1;
        $newTicketNumber = 'REG-' . date('y') . str_pad($lastTicket, 4, '0', STR_PAD_LEFT);

        $patient->update([
            'status' => 'pending',
            'ticket_number' => $newTicketNumber,
            'is_priority' => $request->has('is_priority') && $request->is_priority == '1',
            'willingness' => null,
            'action_date' => null,
            'scheduled_at' => null,
            'called_by' => null,
            'called_at' => null,
        ]);

        return back()->with('success', 'Pasien ' . $patient->name . ' berhasil didaftarkan ulang dengan Nomor Tiket baru: ' . $newTicketNumber);
    }

    public function downloadTemplate()
    {
        return Excel::download(new PatientsTemplateExport, 'template-import-pasien.xlsx');
    }

    public function export()
    {
        return Excel::download(new PatientsExport, 'data-pasien-' . date('Y-m-d') . '.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            
            $destinationPath = public_path('temp_imports');
            $file->move($destinationPath, $filename);
            
            $fullPath = $destinationPath . DIRECTORY_SEPARATOR . $filename;

            Excel::import(new PatientsImport, $fullPath);

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }

            return back()->with('success', 'Data pasien berhasil diimpor!');

        } catch (\Maatwebsite\Excel\Validators\ValidationException $e) {
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }

            $failures = $e->failures();
            $errorMessages = [];
            foreach ($failures as $failure) {
                $errorMessages[] = "Baris ke-{$failure->row()}: " . implode(', ', $failure->errors());
            }
            return back()->with('error', 'Gagal validasi baris Excel: ' . implode(' | ', $errorMessages));

        } catch (\Throwable $e) {
            if (isset($fullPath) && file_exists($fullPath)) {
                unlink($fullPath);
            }
            
            \Log::error('Import Error: ' . $e->getMessage());
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function documents(Patient $patient)
    {
        $patient->load(['insurance', 'caller', 'documents']);
        return view('patients.partials.documents', compact('patient'));
    }

    public function storeDocument(Request $request, Patient $patient)
    {
        $request->validate([
            'document_name' => 'required|string|max:255',
            'document_file' => 'required|file|mimes:pdf,jpg,jpeg,png,webp,gif,mp4,webm,mov,m4v,ogg|max:102400',
            'document_date' => 'nullable|date',
        ]);

        try {
            $file = $request->file('document_file');

            if (!$file) {
                return back()->withInput()->with(
                    'error',
                    'File dokumen tidak diterima oleh PHP.'
                );
            }

            if (!$file->isValid()) {
                return back()->withInput()->with(
                    'error',
                    'Upload file gagal: ' . $file->getErrorMessage()
                );
            }

            /*
             * Untuk upload besar di Windows/Laragon, gunakan pathname file
             * temporary secara langsung. Ini menghindari kasus storeAs()
             * berakhir dengan error "Path cannot be empty".
             */
            $temporaryPath = $file->getPathname();

            if (
                !is_string($temporaryPath) ||
                trim($temporaryPath) === '' ||
                !is_file($temporaryPath)
            ) {
                Log::error('Temporary upload file tidak tersedia.', [
                    'patient_id' => $patient->id,
                    'original_name' => $file->getClientOriginalName(),
                    'temporary_path' => $temporaryPath,
                    'upload_error' => $file->getError(),
                    'upload_error_message' => $file->getErrorMessage(),
                    'upload_tmp_dir' => ini_get('upload_tmp_dir'),
                    'system_tmp_dir' => sys_get_temp_dir(),
                ]);

                return back()->withInput()->with(
                    'error',
                    'File sementara upload tidak ditemukan. Periksa upload_tmp_dir PHP/Laragon.'
                );
            }

            $originalName = trim((string) $file->getClientOriginalName());
            $extension = strtolower((string) $file->getClientOriginalExtension());
            $baseName = pathinfo($originalName, PATHINFO_FILENAME);
            $baseName = preg_replace('/[^A-Za-z0-9_\-]+/', '_', (string) $baseName);
            $baseName = trim((string) $baseName, '_-');

            if ($baseName === '') {
                $baseName = 'dokumen';
            }

            $filename =
                now()->format('Ymd_His') . '_' .
                Str::random(8) . '_' .
                $baseName .
                ($extension !== '' ? '.' . $extension : '');

            $disk = Storage::disk('public');

            if (!$disk->exists('patient-documents')) {
                $disk->makeDirectory('patient-documents');
            }

            $relativePath = 'patient-documents/' . $filename;

            $stream = fopen($temporaryPath, 'rb');

            if ($stream === false) {
                throw new \RuntimeException(
                    'File temporary upload tidak dapat dibuka.'
                );
            }

            try {
                $stored = $disk->put($relativePath, $stream);
            } finally {
                if (is_resource($stream)) {
                    fclose($stream);
                }
            }

            if (!$stored || !$disk->exists($relativePath)) {
                throw new \RuntimeException(
                    'File gagal ditulis ke storage patient-documents.'
                );
            }

            try {
                $document = $patient->documents()->create([
                    'document_name' => $request->document_name,
                    'file_path' => $relativePath,
                    'file_type' => $extension,
                ]);

                $document->setAttribute(
                    'document_date',
                    $request->filled('document_date')
                        ? Carbon::parse($request->document_date)
                        : now()
                );

                $document->save();
            } catch (\Throwable $e) {
                if ($disk->exists($relativePath)) {
                    $disk->delete($relativePath);
                }

                throw $e;
            }

            return back()->with(
                'success',
                'Dokumen berhasil diunggah dan disimpan!'
            );

        } catch (\Throwable $e) {
            report($e);

            Log::error('Gagal mengunggah dokumen pasien.', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'post_max_size' => ini_get('post_max_size'),
                'upload_tmp_dir' => ini_get('upload_tmp_dir'),
                'system_tmp_dir' => sys_get_temp_dir(),
            ]);

            return back()->withInput()->with(
                'error',
                'Gagal mengunggah dokumen: ' . $e->getMessage()
            );
        }
    }

    /**
     * ============================================================
     * UPDATE TANGGAL DOKUMEN
     * ============================================================
     *
     * Hanya document_date yang diubah.
     * created_at tetap menjadi waktu asli ketika file masuk ke sistem.
     */
    public function updateDocumentDate(
        Request $request,
        Patient $patient,
        PatientDocument $document
    ) {
        $this->ensureDocumentBelongsToPatient($patient, $document);

        $validated = $request->validate([
            'document_date' => 'required|date',
        ], [
            'document_date.required' => 'Tanggal dokumen wajib diisi.',
            'document_date.date' => 'Format tanggal dokumen tidak valid.',
        ]);

        try {
            $document->setAttribute(
                'document_date',
                Carbon::parse($validated['document_date'])
            );
            $document->save();

            return back()->with(
                'success',
                'Tanggal dokumen berhasil diperbarui. Tanggal upload tetap tidak berubah.'
            );
        } catch (\Throwable $e) {
            report($e);

            return back()->with(
                'error',
                'Gagal memperbarui tanggal dokumen: ' . $e->getMessage()
            );
        }
    }

    /**
     * ============================================================
     * PREVIEW DOKUMEN AMAN
     * ============================================================
     *
     * Mendukung:
     * - JPG / JPEG / PNG / WEBP / GIF
     * - PDF
     * - MP4 / WEBM / MOV / M4V / OGG
     *
     * Untuk video, HTTP Range didukung agar Play/Pause/Seek bekerja.
     */
    public function previewDocument(
        Request $request,
        Patient $patient,
        PatientDocument $document
    ) {
        $this->ensureDocumentBelongsToPatient($patient, $document);

        [$relativePath, $absolutePath] =
            $this->resolvePatientDocumentPath($document);

        $mimeType =
            $this->resolvePatientDocumentMimeType(
                (string) ($document->file_type ?? ''),
                $relativePath
            );

        $filename =
            $this->buildPatientDocumentFilename(
                $document,
                $relativePath
            );

        $fileSize = filesize($absolutePath);

        abort_if(
            $fileSize === false,
            404,
            'Ukuran file dokumen tidak dapat dibaca.'
        );

        $fileSize = (int) $fileSize;

        /*
        |--------------------------------------------------------------------------
        | RANGE REQUEST UNTUK VIDEO
        |--------------------------------------------------------------------------
        */
        $rangeHeader = $request->header('Range');

        if (
            is_string($rangeHeader) &&
            preg_match('/bytes=(\d*)-(\d*)/i', $rangeHeader, $matches)
        ) {
            $startText = $matches[1] ?? '';
            $endText   = $matches[2] ?? '';

            if ($startText === '' && $endText !== '') {
                $suffixLength = min((int) $endText, $fileSize);
                $start = max(0, $fileSize - $suffixLength);
                $end   = max(0, $fileSize - 1);
            } else {
                $start = $startText !== '' ? (int) $startText : 0;
                $end   = $endText !== '' ? (int) $endText : ($fileSize - 1);
            }

            if ($start < 0 || $start >= $fileSize) {
                return response(
                    '',
                    416,
                    [
                        'Content-Range' => 'bytes */' . $fileSize,
                        'Accept-Ranges' => 'bytes',
                    ]
                );
            }

            $end = min($end, $fileSize - 1);

            if ($end < $start) {
                $end = $fileSize - 1;
            }

            $length = ($end - $start) + 1;

            return response()->stream(
                function () use ($absolutePath, $start, $length) {
                    $handle = fopen($absolutePath, 'rb');

                    if ($handle === false) {
                        return;
                    }

                    try {
                        fseek($handle, $start);

                        $remaining = $length;
                        $chunkSize = 1024 * 1024;

                        while ($remaining > 0 && !feof($handle)) {
                            $readLength = min($chunkSize, $remaining);
                            $buffer = fread($handle, $readLength);

                            if ($buffer === false || $buffer === '') {
                                break;
                            }

                            echo $buffer;
                            $remaining -= strlen($buffer);

                            if (function_exists('ob_flush')) {
                                @ob_flush();
                            }

                            flush();
                        }
                    } finally {
                        fclose($handle);
                    }
                },
                206,
                [
                    'Content-Type'        => $mimeType,
                    'Content-Length'      => (string) $length,
                    'Content-Range'       => 'bytes ' . $start . '-' . $end . '/' . $fileSize,
                    'Accept-Ranges'       => 'bytes',
                    'Content-Disposition' => 'inline; filename="' . $filename . '"',
                    'X-Content-Type-Options' => 'nosniff',
                    'Cache-Control'       => 'private, no-store, no-cache, must-revalidate',
                    'Pragma'              => 'no-cache',
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | FULL RESPONSE UNTUK IMAGE / PDF / VIDEO AWAL
        |--------------------------------------------------------------------------
        */
        return response()->stream(
            function () use ($absolutePath) {
                $handle = fopen($absolutePath, 'rb');

                if ($handle === false) {
                    return;
                }

                try {
                    fpassthru($handle);
                } finally {
                    fclose($handle);
                }
            },
            200,
            [
                'Content-Type'           => $mimeType,
                'Content-Length'         => (string) $fileSize,
                'Accept-Ranges'          => 'bytes',
                'Content-Disposition'    => 'inline; filename="' . $filename . '"',
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control'          => 'private, no-store, no-cache, must-revalidate',
                'Pragma'                 => 'no-cache',
            ]
        );
    }

    /**
     * ============================================================
     * DOWNLOAD DOKUMEN AMAN
     * ============================================================
     */
    public function downloadDocument(
        Patient $patient,
        PatientDocument $document
    ) {
        $this->ensureDocumentBelongsToPatient($patient, $document);

        [$relativePath, $absolutePath] =
            $this->resolvePatientDocumentPath($document);

        $mimeType =
            $this->resolvePatientDocumentMimeType(
                (string) ($document->file_type ?? ''),
                $relativePath
            );

        $filename =
            $this->buildPatientDocumentFilename(
                $document,
                $relativePath
            );

        return response()->download(
            $absolutePath,
            $filename,
            [
                'Content-Type'           => $mimeType,
                'X-Content-Type-Options' => 'nosniff',
                'Cache-Control'          => 'private, no-store',
            ]
        );
    }

    /**
     * Pastikan dokumen memang milik pasien pada URL.
     */
    private function ensureDocumentBelongsToPatient(
        Patient $patient,
        PatientDocument $document
    ): void {
        abort_unless(
            (string) $document->patient_id === (string) $patient->id,
            404
        );
    }

    /**
     * Cari file pada disk public.
     *
     * Mendukung path normal dan beberapa format path lama:
     * - patient-documents/file.jpg
     * - storage/patient-documents/file.jpg
     * - public/patient-documents/file.jpg
     * - storage/app/public/patient-documents/file.jpg
     */
    private function resolvePatientDocumentPath(
        PatientDocument $document
    ): array {
        $raw = trim(
            str_replace(
                '\\',
                '/',
                (string) $document->file_path
            )
        );

        abort_if(
            $raw === '',
            404,
            'Path dokumen kosong.'
        );

        /*
        | Cegah path traversal.
        */
        abort_if(
            str_contains($raw, '../') ||
            str_contains($raw, '..\\'),
            404
        );

        $raw = ltrim($raw, '/');

        $candidates = [$raw];

        foreach ([
            'storage/app/public/',
            'public/storage/',
            'storage/',
            'public/',
        ] as $prefix) {
            if (
                str_starts_with(
                    strtolower($raw),
                    strtolower($prefix)
                )
            ) {
                $candidates[] = substr(
                    $raw,
                    strlen($prefix)
                );
            }
        }

        $candidates = array_values(
            array_unique(
                array_filter($candidates)
            )
        );

        $disk = Storage::disk('public');

        foreach ($candidates as $candidate) {
            $candidate = ltrim(
                str_replace('\\', '/', $candidate),
                '/'
            );

            if (
                $candidate !== '' &&
                $disk->exists($candidate)
            ) {
                $absolutePath = $disk->path($candidate);

                if (is_file($absolutePath)) {
                    return [
                        $candidate,
                        $absolutePath,
                    ];
                }
            }
        }

        abort(
            404,
            'File dokumen tidak ditemukan pada storage Laravel.'
        );
    }

    /**
     * Tentukan Content-Type berdasarkan ekstensi terlebih dahulu.
     *
     * Ini lebih stabil pada Windows daripada hanya mengandalkan
     * mime_content_type().
     */
    private function resolvePatientDocumentMimeType(
        string $storedType,
        string $path
    ): string {
        $extension = strtolower(
            pathinfo(
                $path,
                PATHINFO_EXTENSION
            )
        );

        $mimeByExtension = match ($extension) {
            'jpg', 'jpeg' => 'image/jpeg',
            'png'         => 'image/png',
            'webp'        => 'image/webp',
            'gif'         => 'image/gif',
            'bmp'         => 'image/bmp',

            'pdf'         => 'application/pdf',

            'mp4', 'm4v'  => 'video/mp4',
            'webm'        => 'video/webm',
            'ogg', 'ogv'  => 'video/ogg',
            'mov'         => 'video/quicktime',

            default       => null,
        };

        if ($mimeByExtension !== null) {
            return $mimeByExtension;
        }

        $storedType = strtolower(
            trim($storedType)
        );

        if (
            $storedType !== '' &&
            str_contains($storedType, '/')
        ) {
            return $storedType;
        }

        return 'application/octet-stream';
    }

    /**
     * Nama file yang aman untuk inline/download.
     */
    private function buildPatientDocumentFilename(
        PatientDocument $document,
        string $path
    ): string {
        $extension = strtolower(
            pathinfo(
                $path,
                PATHINFO_EXTENSION
            )
        );

        $name = trim(
            (string) $document->document_name
        );

        if ($name === '') {
            $name = 'dokumen-pasien';
        }

        $name = Str::slug(
            $name,
            '-'
        );

        if ($name === '') {
            $name = 'dokumen-pasien';
        }

        return $extension !== ''
            ? $name . '.' . $extension
            : $name;
    }

    public function destroyDocument(Patient $patient, PatientDocument $document)
    {
        $this->ensureDocumentBelongsToPatient($patient, $document);

        try {
            if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
                Storage::disk('public')->delete($document->file_path);
            }
            $document->delete();

            return back()->with('success', 'Dokumen berhasil dihapus!');
        } catch (\Throwable $e) {
            report($e);
            return back()->with('error', 'Gagal menghapus dokumen: ' . $e->getMessage());
        }
    }

    /**
     * Generate 6-digit numeric token for patient portal login
     */
    public function generatePortalToken(Patient $patient)
    {
        do {
            $token = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Patient::where('portal_token', $token)->exists());

        $patient->portal_token = $token;
        $patient->save();

        return back()->with('success', 'Token portal 6 digit berhasil dibuat: ' . $token);
    }

    public function destroy(Patient $patient)
    {
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'Data pasien berhasil dihapus!');
    }
}
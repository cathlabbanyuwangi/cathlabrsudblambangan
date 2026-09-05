<?php

namespace App\Http\Controllers;

use App\Models\DicomStudy;
use App\Models\Patient;
use App\Models\PatientDocument;
use App\Services\OrthancService;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Symfony\Component\Process\Process;

class DicomController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TEST KONEKSI LARAVEL -> ORTHANC
    |--------------------------------------------------------------------------
    */

    public function test(OrthancService $orthanc)
    {
        try {
            return response()->json([
                'success' => true,
                'message' => 'Laravel berhasil terhubung ke Orthanc.',
                'orthanc' => $orthanc->systemInfo(),
            ]);
        } catch (\Throwable $e) {
            Log::error('Orthanc Connection Error', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Laravel gagal terhubung ke Orthanc.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /*
    |--------------------------------------------------------------------------
    | DAFTAR DICOM MILIK PASIEN
    |--------------------------------------------------------------------------
    */

    public function index(Patient $patient)
    {
        $studies = DicomStudy::where('patient_id', $patient->id)
            ->orderByRaw('study_date IS NULL')
            ->orderByDesc('study_date')
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('dicom.index', compact(
            'patient',
            'studies'
        ));
    }

    /*
    |--------------------------------------------------------------------------
    | FORM UPLOAD DICOM
    |--------------------------------------------------------------------------
    */

    public function create(Patient $patient)
    {
        return view('dicom.create', compact('patient'));
    }

    /*
    |--------------------------------------------------------------------------
    | PROSES UPLOAD DICOM
    |--------------------------------------------------------------------------
    |
    | Mendukung:
    | - DICOM .dcm
    | - DICOM tanpa ekstensi
    | - Multiple file
    | - Philips Allura XA
    | - Multi-frame / Cine
    | - ZIP
    |
    */

    public function store(
        Request $request,
        Patient $patient,
        OrthancService $orthanc
    ) {
        /*
        |--------------------------------------------------------------------------
        | VALIDASI
        |--------------------------------------------------------------------------
        |
        | Jangan gunakan mimes:dcm.
        | Banyak file DICOM Philips tidak mempunyai ekstensi.
        |
        | max = KB
        | 1048576 KB = 1 GB per file
        */

        $request->validate([
            'dicom_file' => [
                'nullable',
                'file',
                'max:1048576',
            ],

            'dicom_files' => [
                'nullable',
                'array',
                'max:1000',
            ],

            'dicom_files.*' => [
                'file',
                'max:1048576',
            ],
        ]);

        /*
        |--------------------------------------------------------------------------
        | KUMPULKAN FILE
        |--------------------------------------------------------------------------
        */

        $files = [];

        if ($request->hasFile('dicom_file')) {
            $singleFile = $request->file('dicom_file');

            if ($singleFile instanceof UploadedFile) {
                $files[] = $singleFile;
            }
        }

        if ($request->hasFile('dicom_files')) {
            foreach ((array) $request->file('dicom_files') as $file) {
                if ($file instanceof UploadedFile) {
                    $files[] = $file;
                }
            }
        }

        if (empty($files)) {
            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Belum ada file DICOM yang dipilih.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | CATAT STUDY SEBELUM UPLOAD
        |--------------------------------------------------------------------------
        */

        try {
            $studiesBefore = $orthanc->studies();
        } catch (\Throwable $e) {
            Log::error('Tidak dapat membaca daftar Study Orthanc', [
                'patient_id' => $patient->id,
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Tidak dapat terhubung ke server DICOM Orthanc: ' .
                    $e->getMessage()
                );
        }

        $studyIds = [];
        $uploadedCount = 0;
        $failedFiles = [];

        /*
        |--------------------------------------------------------------------------
        | UPLOAD FILE SATU PER SATU
        |--------------------------------------------------------------------------
        */

        foreach ($files as $file) {
            try {
                if (!$file instanceof UploadedFile) {
                    throw new RuntimeException(
                        'Objek file upload tidak valid.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | CEK UPLOAD PHP
                |--------------------------------------------------------------------------
                */

                if (!$file->isValid()) {
                    throw new RuntimeException(
                        'Upload file gagal: ' .
                        $file->getErrorMessage()
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | PATH TEMPORARY
                |--------------------------------------------------------------------------
                |
                | Gunakan getPathname().
                | Jangan gunakan getRealPath() pada Windows/Laragon.
                */

                $tempPath = $file->getPathname();

                if (empty($tempPath)) {
                    throw new RuntimeException(
                        'Path temporary PHP kosong.'
                    );
                }

                if (!file_exists($tempPath)) {
                    throw new RuntimeException(
                        'File temporary PHP tidak ditemukan. ' .
                        'Periksa upload_tmp_dir pada php.ini.'
                    );
                }

                if (!is_file($tempPath)) {
                    throw new RuntimeException(
                        'Temporary upload bukan file yang valid.'
                    );
                }

                if (!is_readable($tempPath)) {
                    throw new RuntimeException(
                        'File temporary PHP ditemukan tetapi tidak dapat dibaca.'
                    );
                }

                clearstatcache(true, $tempPath);

                $fileSize = filesize($tempPath);

                if ($fileSize === false || $fileSize <= 0) {
                    throw new RuntimeException(
                        'File DICOM kosong atau ukuran file tidak dapat dibaca.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | LOG UPLOAD
                |--------------------------------------------------------------------------
                */

                Log::info('DICOM temporary upload received', [
                    'patient_id' => $patient->id,

                    'medical_record_number' =>
                        $patient->medical_record_number,

                    'original_name' =>
                        $file->getClientOriginalName(),

                    'temporary_path' =>
                        $tempPath,

                    'exists' =>
                        file_exists($tempPath),

                    'readable' =>
                        is_readable($tempPath),

                    'size' =>
                        $fileSize,

                    'upload_error' =>
                        $file->getError(),
                ]);

                /*
                |--------------------------------------------------------------------------
                | KIRIM KE ORTHANC
                |--------------------------------------------------------------------------
                */

                $result = $orthanc->uploadDicom($tempPath);

                if (!is_array($result)) {
                    throw new RuntimeException(
                        'Response dari Orthanc tidak valid.'
                    );
                }

                /*
                |--------------------------------------------------------------------------
                | AMBIL PARENT STUDY
                |--------------------------------------------------------------------------
                */

                $foundStudyIds =
                    $this->extractStudyIdsFromUploadResponse(
                        $result
                    );

                foreach ($foundStudyIds as $studyId) {
                    if (!in_array($studyId, $studyIds, true)) {
                        $studyIds[] = $studyId;
                    }
                }

                $uploadedCount++;

                Log::info(
                    'DICOM uploaded successfully to Orthanc',
                    [
                        'patient_id' => $patient->id,
                        'filename' => $file->getClientOriginalName(),
                        'size' => $fileSize,
                        'study_ids' => $foundStudyIds,
                    ]
                );
            } catch (\Throwable $e) {
                $failedFiles[] = [
                    'name' =>
                        $file instanceof UploadedFile
                            ? $file->getClientOriginalName()
                            : 'unknown',

                    'error' =>
                        $e->getMessage(),
                ];

                Log::warning(
                    'DICOM File Upload Failed',
                    [
                        'patient_id' =>
                            $patient->id,

                        'filename' =>
                            $file instanceof UploadedFile
                                ? $file->getClientOriginalName()
                                : 'unknown',

                        'error' =>
                            $e->getMessage(),

                        'upload_error' =>
                            $file instanceof UploadedFile
                                ? $file->getError()
                                : null,

                        'upload_error_message' =>
                            $file instanceof UploadedFile
                                ? $file->getErrorMessage()
                                : null,
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | FALLBACK STUDY
        |--------------------------------------------------------------------------
        |
        | Jika ParentStudy tidak ditemukan dari response,
        | bandingkan Study Orthanc sebelum dan sesudah upload.
        */

        if (empty($studyIds)) {
            try {
                $studiesAfter = $orthanc->studies();

                $newStudies = array_values(
                    array_diff(
                        $studiesAfter,
                        $studiesBefore
                    )
                );

                foreach ($newStudies as $studyId) {
                    if (!in_array($studyId, $studyIds, true)) {
                        $studyIds[] = $studyId;
                    }
                }
            } catch (\Throwable $e) {
                Log::warning(
                    'Tidak dapat mencari Study baru setelah upload.',
                    [
                        'patient_id' => $patient->id,
                        'error' => $e->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SEMUA FILE GAGAL
        |--------------------------------------------------------------------------
        */

        if ($uploadedCount === 0) {
            $firstError =
                $failedFiles[0]['error']
                ?? 'Tidak ada file yang berhasil diupload.';

            return redirect()
                ->back()
                ->withInput()
                ->with(
                    'error',
                    'Upload DICOM gagal. ' . $firstError
                );
        }

        /*
        |--------------------------------------------------------------------------
        | STUDY TIDAK TERIDENTIFIKASI
        |--------------------------------------------------------------------------
        */

        if (empty($studyIds)) {
            return redirect()
                ->back()
                ->with(
                    'error',
                    'File berhasil dikirim ke Orthanc, tetapi Study DICOM ' .
                    'tidak dapat diidentifikasi.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | SINKRONKAN ORTHANC -> DATABASE
        |--------------------------------------------------------------------------
        */

        $syncedStudies = [];
        $syncErrors = [];

        foreach ($studyIds as $studyId) {
            try {
                $dicomStudy =
                    $this->syncStudyToDatabase(
                        $patient,
                        $studyId,
                        $orthanc
                    );

                $syncedStudies[] = $dicomStudy;
            } catch (\Throwable $e) {
                $syncErrors[] = $e->getMessage();

                Log::error(
                    'DICOM Study Sync Error',
                    [
                        'patient_id' =>
                            $patient->id,

                        'orthanc_study_id' =>
                            $studyId,

                        'error' =>
                            $e->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | SEMUA SYNC GAGAL
        |--------------------------------------------------------------------------
        */

        if (empty($syncedStudies)) {
            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    'File sudah masuk ke Orthanc, tetapi data Study gagal ' .
                    'disinkronkan ke database Laravel. ' .
                    ($syncErrors[0] ?? '')
                );
        }

        /*
        |--------------------------------------------------------------------------
        | HASIL
        |--------------------------------------------------------------------------
        */

        $message =
            count($syncedStudies) .
            ' Study DICOM berhasil diimport.';

        if (!empty($failedFiles)) {
            $message .=
                ' ' .
                count($failedFiles) .
                ' file tidak dapat diproses.';
        }

        if (!empty($syncErrors)) {
            $message .=
                ' ' .
                count($syncErrors) .
                ' Study gagal disinkronkan.';
        }

        return redirect()
            ->route(
                'patients.dicom.index',
                [
                    'patient' => $patient,
                ]
            )
            ->with(
                'success',
                $message
            );
    }

    /*
    |--------------------------------------------------------------------------
    | DETAIL STUDY
    |--------------------------------------------------------------------------
    */

    public function show(
        Patient $patient,
        DicomStudy $dicomStudy,
        OrthancService $orthanc
    ) {
        $this->ensureStudyBelongsToPatient(
            $patient,
            $dicomStudy
        );

        $orthancStudy = null;
        $series = [];
        $orthancError = null;

        try {
            /*
            |--------------------------------------------------------------------------
            | AMBIL STUDY
            |--------------------------------------------------------------------------
            */

            $orthancStudy =
                $orthanc->study(
                    $dicomStudy->orthanc_study_id
                );

            /*
            |--------------------------------------------------------------------------
            | AMBIL SERIES
            |--------------------------------------------------------------------------
            */

            foreach (
                $orthancStudy['Series'] ?? []
                as $seriesId
            ) {
                try {
                    $seriesData =
                        $orthanc->series(
                            $seriesId
                        );

                    $series[] = $seriesData;
                } catch (\Throwable $e) {
                    Log::warning(
                        'Tidak dapat membaca DICOM Series.',
                        [
                            'patient_id' =>
                                $patient->id,

                            'series_id' =>
                                $seriesId,

                            'error' =>
                                $e->getMessage(),
                        ]
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | URUTKAN SERIES
            |--------------------------------------------------------------------------
            */

            usort(
                $series,
                function ($a, $b) {
                    $numberA =
                        isset(
                            $a['MainDicomTags']['SeriesNumber']
                        )
                            ? (int) $a['MainDicomTags']['SeriesNumber']
                            : PHP_INT_MAX;

                    $numberB =
                        isset(
                            $b['MainDicomTags']['SeriesNumber']
                        )
                            ? (int) $b['MainDicomTags']['SeriesNumber']
                            : PHP_INT_MAX;

                    return $numberA <=> $numberB;
                }
            );
        } catch (\Throwable $e) {
            $orthancError = $e->getMessage();

            Log::error(
                'Tidak dapat membuka Study dari Orthanc.',
                [
                    'patient_id' =>
                        $patient->id,

                    'dicom_study_id' =>
                        $dicomStudy->id,

                    'orthanc_study_id' =>
                        $dicomStudy->orthanc_study_id,

                    'error' =>
                        $e->getMessage(),
                ]
            );
        }

        return view(
            'dicom.show',
            compact(
                'patient',
                'dicomStudy',
                'orthancStudy',
                'series',
                'orthancError'
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | VIEWER DICOM DI DALAM LARAVEL
    |--------------------------------------------------------------------------
    |
    | Tidak lagi redirect langsung ke website Orthanc.
    |
    | Laravel menampilkan:
    |
    | resources/views/dicom/viewer.blade.php
    |
    | kemudian OHIF dimuat melalui iframe di dalam halaman Laravel.
    |
    */

    public function viewer(
        Patient $patient,
        DicomStudy $dicomStudy,
        OrthancService $orthanc
    ) {
        /*
        |--------------------------------------------------------------------------
        | PASTIKAN STUDY MILIK PASIEN
        |--------------------------------------------------------------------------
        */

        $this->ensureStudyBelongsToPatient(
            $patient,
            $dicomStudy
        );

        /*
        |--------------------------------------------------------------------------
        | CEK STUDY INSTANCE UID
        |--------------------------------------------------------------------------
        */

        if (empty($dicomStudy->study_instance_uid)) {
            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    'StudyInstanceUID DICOM tidak tersedia.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | PASTIKAN STUDY MASIH ADA DI ORTHANC
        |--------------------------------------------------------------------------
        */

        try {
            $orthanc->study(
                $dicomStudy->orthanc_study_id
            );
        } catch (\Throwable $e) {
            Log::warning(
                'Study DICOM tidak tersedia saat membuka viewer.',
                [
                    'patient_id' =>
                        $patient->id,

                    'dicom_study_id' =>
                        $dicomStudy->id,

                    'orthanc_study_id' =>
                        $dicomStudy->orthanc_study_id,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    'Study DICOM tidak ditemukan atau Orthanc tidak dapat diakses.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | PUBLIC URL ORTHANC
        |--------------------------------------------------------------------------
        |
        | Backend Laravel tetap menggunakan:
        |
        | http://127.0.0.1:8042
        |
        | Browser menggunakan:
        |
        | https://dicom.cathlabbanyuwangi.my.id
        |
        */

        $orthancPublicUrl = config(
            'services.orthanc.public_url',
            config('services.orthanc.url')
        );

        $orthancPublicUrl =
            rtrim(
                (string) $orthancPublicUrl,
                '/'
            );

        if (empty($orthancPublicUrl)) {
            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    'ORTHANC_PUBLIC_URL belum dikonfigurasi.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | URL OHIF HANYA UNTUK STUDY YANG DIPILIH
        |--------------------------------------------------------------------------
        |
        | Jadi meskipun Orthanc menyimpan banyak pasien,
        | OHIF yang dibuka dari Laravel diarahkan ke StudyInstanceUID
        | milik Study pasien ini saja.
        */

        $viewerUrl =
            $orthancPublicUrl .
            '/ohif/viewer?StudyInstanceUIDs=' .
            rawurlencode(
                $dicomStudy->study_instance_uid
            );

        /*
        |--------------------------------------------------------------------------
        | TAMPILKAN VIEWER DI DALAM WEB LARAVEL
        |--------------------------------------------------------------------------
        */

        return view(
            'dicom.viewer',
            [
                'patient' => $patient,
                'dicomStudy' => $dicomStudy,
                'viewerUrl' => $viewerUrl,
                'orthancPublicUrl' => $orthancPublicUrl,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | EKSPOR STUDY DICOM -> DOKUMEN PASIEN
    |--------------------------------------------------------------------------
    |
    | Aturan:
    |
    | - Instance 1 frame    -> JPEG
    | - Instance > 1 frame  -> MP4 (cine)
    |
    | DICOM asli TIDAK dihapus dan tetap disimpan di Orthanc.
    | Hasil JPEG/MP4 disimpan ke disk "public" Laravel dan diregistrasikan
    | ke patient_documents agar dapat muncul pada menu Dokumen/Portal Pasien.
    |
    */

    public function export(
        Patient $patient,
        DicomStudy $dicomStudy,
        OrthancService $orthanc
    ) {
        $this->ensureStudyBelongsToPatient(
            $patient,
            $dicomStudy
        );

        /*
        |--------------------------------------------------------------------------
        | PASTIKAN TABEL DOKUMEN TERSEDIA
        |--------------------------------------------------------------------------
        */

        try {
            $this->patientDocumentColumns();
        } catch (\Throwable $e) {
            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    $e->getMessage()
                );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL STUDY DARI ORTHANC
        |--------------------------------------------------------------------------
        */

        try {
            $study =
                $orthanc->study(
                    $dicomStudy->orthanc_study_id
                );
        } catch (\Throwable $e) {
            Log::error(
                'DICOM Export - Study Orthanc tidak dapat dibaca.',
                [
                    'patient_id' =>
                        $patient->id,

                    'dicom_study_id' =>
                        $dicomStudy->id,

                    'orthanc_study_id' =>
                        $dicomStudy->orthanc_study_id,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    'Study DICOM tidak dapat dibaca dari Orthanc: ' .
                    $e->getMessage()
                );
        }

        $seriesIds =
            $study['Series']
            ?? [];

        if (empty($seriesIds)) {
            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    'Study DICOM tidak memiliki Series yang dapat diekspor.'
                );
        }

        /*
        |--------------------------------------------------------------------------
        | COUNTER HASIL
        |--------------------------------------------------------------------------
        */

        $jpegCount = 0;
        $mp4Count = 0;
        $skippedCount = 0;
        $failedCount = 0;
        $errors = [];

        /*
        |--------------------------------------------------------------------------
        | PROSES SERIES
        |--------------------------------------------------------------------------
        */

        foreach ($seriesIds as $seriesOrdinal => $seriesId) {
            try {
                $series =
                    $orthanc->series(
                        $seriesId
                    );
            } catch (\Throwable $e) {
                $failedCount++;

                $errors[] =
                    'Series ' .
                    ($seriesOrdinal + 1) .
                    ': ' .
                    $e->getMessage();

                Log::warning(
                    'DICOM Export - Series gagal dibaca.',
                    [
                        'patient_id' =>
                            $patient->id,

                        'series_id' =>
                            $seriesId,

                        'error' =>
                            $e->getMessage(),
                    ]
                );

                continue;
            }

            $instances =
                $series['Instances']
                ?? [];

            if (empty($instances)) {
                continue;
            }

            $seriesNumber =
                $series['MainDicomTags']['SeriesNumber']
                ?? ($seriesOrdinal + 1);

            /*
            |--------------------------------------------------------------------------
            | PROSES INSTANCE
            |--------------------------------------------------------------------------
            */

            foreach ($instances as $instanceOrdinal => $instanceId) {
                try {
                    $result =
                        $this->exportDicomInstance(
                            $patient,
                            $dicomStudy,
                            $instanceId,
                            $seriesNumber,
                            $instanceOrdinal + 1
                        );

                    if ($result === 'jpeg') {
                        $jpegCount++;
                    } elseif ($result === 'mp4') {
                        $mp4Count++;
                    } elseif ($result === 'skipped') {
                        $skippedCount++;
                    }
                } catch (\Throwable $e) {
                    $failedCount++;

                    $errors[] =
                        'Series ' .
                        $seriesNumber .
                        ', Instance ' .
                        ($instanceOrdinal + 1) .
                        ': ' .
                        $e->getMessage();

                    Log::warning(
                        'DICOM Export - Instance gagal diekspor.',
                        [
                            'patient_id' =>
                                $patient->id,

                            'dicom_study_id' =>
                                $dicomStudy->id,

                            'orthanc_study_id' =>
                                $dicomStudy->orthanc_study_id,

                            'series_number' =>
                                $seriesNumber,

                            'instance_id' =>
                                $instanceId,

                            'error' =>
                                $e->getMessage(),
                        ]
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | TIDAK ADA FILE BERHASIL
        |--------------------------------------------------------------------------
        */

        if (
            $jpegCount === 0 &&
            $mp4Count === 0 &&
            $skippedCount === 0
        ) {
            $firstError =
                $errors[0]
                ?? 'Tidak ada citra yang dapat diekspor.';

            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    'Ekspor DICOM gagal. ' .
                    $firstError
                );
        }

        /*
        |--------------------------------------------------------------------------
        | PESAN HASIL
        |--------------------------------------------------------------------------
        */

        $message =
            'Ekspor DICOM selesai. ' .
            $jpegCount .
            ' JPEG dan ' .
            $mp4Count .
            ' MP4 berhasil dimasukkan ke Dokumen pasien.';

        if ($skippedCount > 0) {
            $message .=
                ' ' .
                $skippedCount .
                ' file dilewati karena sebelumnya sudah pernah diekspor.';
        }

        if ($failedCount > 0) {
            $message .=
                ' ' .
                $failedCount .
                ' instance gagal diproses.';

            if (!empty($errors)) {
                $message .=
                    ' Error pertama: ' .
                    Str::limit(
                        $errors[0],
                        250
                    );
            }
        }

        return redirect()
            ->route(
                'patients.dicom.index',
                [
                    'patient' => $patient,
                ]
            )
            ->with(
                $failedCount > 0
                    ? 'error'
                    : 'success',
                $message
            );
    }

    /*
    |--------------------------------------------------------------------------
    | HAPUS STUDY
    |--------------------------------------------------------------------------
    */

    public function destroy(
        Patient $patient,
        DicomStudy $dicomStudy,
        OrthancService $orthanc
    ) {
        /*
        |--------------------------------------------------------------------------
        | PASTIKAN STUDY MILIK PASIEN
        |--------------------------------------------------------------------------
        */

        $this->ensureStudyBelongsToPatient(
            $patient,
            $dicomStudy
        );

        $orthancStudyId =
            $dicomStudy->orthanc_study_id;

        /*
        |--------------------------------------------------------------------------
        | HAPUS DARI ORTHANC
        |--------------------------------------------------------------------------
        */

        try {
            $orthanc->deleteStudy(
                $orthancStudyId
            );
        } catch (\Throwable $e) {
            Log::error(
                'Gagal menghapus Study dari Orthanc.',
                [
                    'patient_id' =>
                        $patient->id,

                    'orthanc_study_id' =>
                        $orthancStudyId,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            return redirect()
                ->back()
                ->with(
                    'error',
                    'Study DICOM gagal dihapus dari Orthanc: ' .
                    $e->getMessage()
                );
        }

        /*
        |--------------------------------------------------------------------------
        | HAPUS DATABASE
        |--------------------------------------------------------------------------
        */

        try {
            $dicomStudy->delete();
        } catch (\Throwable $e) {
            Log::error(
                'DICOM sudah dihapus dari Orthanc tetapi gagal dihapus dari database.',
                [
                    'patient_id' =>
                        $patient->id,

                    'dicom_study_id' =>
                        $dicomStudy->id,

                    'error' =>
                        $e->getMessage(),
                ]
            );

            return redirect()
                ->route(
                    'patients.dicom.index',
                    [
                        'patient' => $patient,
                    ]
                )
                ->with(
                    'error',
                    'File sudah dihapus dari Orthanc, tetapi record database ' .
                    'Laravel gagal dihapus.'
                );
        }

        return redirect()
            ->route(
                'patients.dicom.index',
                [
                    'patient' => $patient,
                ]
            )
            ->with(
                'success',
                'Study DICOM berhasil dihapus.'
            );
    }


    /*
    |--------------------------------------------------------------------------
    | EXPORT SATU INSTANCE DICOM
    |--------------------------------------------------------------------------
    */

    private function exportDicomInstance(
        Patient $patient,
        DicomStudy $dicomStudy,
        string $instanceId,
        $seriesNumber,
        int $instanceOrdinal
    ): string {
        $tags =
            $this->getInstanceSimplifiedTags(
                $instanceId
            );

        $frameCount =
            $this->resolveFrameCount(
                $tags
            );

        $instanceNumber =
            $this->dicomTagScalar(
                $tags['InstanceNumber']
                ?? null
            );

        if (
            $instanceNumber === null ||
            trim($instanceNumber) === ''
        ) {
            $instanceNumber =
                (string) $instanceOrdinal;
        }

        $seriesNumberText =
            trim(
                (string) $seriesNumber
            );

        if ($seriesNumberText === '') {
            $seriesNumberText = '1';
        }

        $studyLabel =
            $dicomStudy->study_description
            ?: 'DICOM';

        $studyLabel =
            $this->safeFileLabel(
                $studyLabel
            );

        $instanceHash =
            substr(
                preg_replace(
                    '/[^A-Za-z0-9]/',
                    '',
                    $instanceId
                ),
                0,
                16
            );

        if ($instanceHash === '') {
            $instanceHash =
                substr(
                    sha1($instanceId),
                    0,
                    16
                );
        }

        $relativeDirectory =
            'patient-documents/' .
            $patient->id .
            '/dicom/' .
            $dicomStudy->orthanc_study_id;

        /*
        |--------------------------------------------------------------------------
        | SINGLE FRAME -> JPEG
        |--------------------------------------------------------------------------
        */

        if ($frameCount <= 1) {
            $relativePath =
                $relativeDirectory .
                '/S' .
                $this->numberLabel($seriesNumberText) .
                '_I' .
                $this->numberLabel($instanceNumber) .
                '_' .
                $instanceHash .
                '.jpg';

            $marker =
                'DICOM_EXPORT|' .
                'study=' .
                $dicomStudy->orthanc_study_id .
                '|instance=' .
                $instanceId .
                '|type=jpeg';

            if (
                $this->exportedPatientDocumentExists(
                    $patient,
                    $marker,
                    $relativePath
                )
            ) {
                return 'skipped';
            }

            $jpeg =
                $this->getRenderedDicomFrame(
                    $instanceId,
                    0
                );

            if ($jpeg === '') {
                throw new RuntimeException(
                    'Orthanc menghasilkan JPEG kosong.'
                );
            }

            $stored =
                Storage::disk('public')
                    ->put(
                        $relativePath,
                        $jpeg
                    );

            if (!$stored) {
                throw new RuntimeException(
                    'JPEG gagal disimpan ke storage Laravel.'
                );
            }

            try {
                $this->savePatientDocument(
                    $patient,
                    'DICOM - ' .
                        $studyLabel .
                        ' - Series ' .
                        $seriesNumberText .
                        ' - Gambar ' .
                        $instanceNumber,
                    $relativePath,
                    'image/jpeg',
                    'jpg',
                    $marker,
                    'Ekspor otomatis dari DICOM. ' .
                    'Study: ' .
                    $dicomStudy->study_instance_uid .
                    '. Series: ' .
                    $seriesNumberText .
                    '. Instance: ' .
                    $instanceNumber .
                    '.'
                );
            } catch (\Throwable $e) {
                Storage::disk('public')
                    ->delete(
                        $relativePath
                    );

                throw $e;
            }

            return 'jpeg';
        }

        /*
        |--------------------------------------------------------------------------
        | MULTI FRAME / CINE -> MP4
        |--------------------------------------------------------------------------
        */

        $relativePath =
            $relativeDirectory .
            '/S' .
            $this->numberLabel($seriesNumberText) .
            '_I' .
            $this->numberLabel($instanceNumber) .
            '_' .
            $instanceHash .
            '.mp4';

        $marker =
            'DICOM_EXPORT|' .
            'study=' .
            $dicomStudy->orthanc_study_id .
            '|instance=' .
            $instanceId .
            '|type=mp4';

        if (
            $this->exportedPatientDocumentExists(
                $patient,
                $marker,
                $relativePath
            )
        ) {
            return 'skipped';
        }

        $fps =
            $this->resolveDicomFps(
                $tags
            );

        $tempDirectory =
            storage_path(
                'app/dicom-export-temp/' .
                Str::uuid()->toString()
            );

        File::ensureDirectoryExists(
            $tempDirectory
        );

        try {
            /*
            |--------------------------------------------------------------------------
            | RENDER SEMUA FRAME DARI ORTHANC
            |--------------------------------------------------------------------------
            */

            for (
                $frame = 0;
                $frame < $frameCount;
                $frame++
            ) {
                $jpeg =
                    $this->getRenderedDicomFrame(
                        $instanceId,
                        $frame
                    );

                if ($jpeg === '') {
                    throw new RuntimeException(
                        'Frame ' .
                        $frame .
                        ' kosong.'
                    );
                }

                $framePath =
                    $tempDirectory .
                    DIRECTORY_SEPARATOR .
                    sprintf(
                        'frame_%06d.jpg',
                        $frame
                    );

                $bytes =
                    file_put_contents(
                        $framePath,
                        $jpeg
                    );

                if (
                    $bytes === false ||
                    $bytes <= 0
                ) {
                    throw new RuntimeException(
                        'Frame ' .
                        $frame .
                        ' gagal ditulis ke temporary folder.'
                    );
                }
            }

            /*
            |--------------------------------------------------------------------------
            | KONVERSI FRAME -> MP4 DENGAN FFMPEG
            |--------------------------------------------------------------------------
            */

            $temporaryMp4 =
                $tempDirectory .
                DIRECTORY_SEPARATOR .
                'output.mp4';

            $this->createMp4WithFfmpeg(
                $tempDirectory,
                $temporaryMp4,
                $fps
            );

            if (
                !is_file($temporaryMp4) ||
                filesize($temporaryMp4) <= 0
            ) {
                throw new RuntimeException(
                    'FFmpeg selesai tetapi file MP4 tidak terbentuk.'
                );
            }

            /*
            |--------------------------------------------------------------------------
            | SIMPAN MP4 KE STORAGE PUBLIC
            |--------------------------------------------------------------------------
            */

            $stream =
                fopen(
                    $temporaryMp4,
                    'rb'
                );

            if ($stream === false) {
                throw new RuntimeException(
                    'MP4 temporary tidak dapat dibaca.'
                );
            }

            try {
                $stored =
                    Storage::disk('public')
                        ->put(
                            $relativePath,
                            $stream
                        );
            } finally {
                fclose($stream);
            }

            if (!$stored) {
                throw new RuntimeException(
                    'MP4 gagal disimpan ke storage Laravel.'
                );
            }

            try {
                $this->savePatientDocument(
                    $patient,
                    'DICOM - ' .
                        $studyLabel .
                        ' - Series ' .
                        $seriesNumberText .
                        ' - Cine ' .
                        $instanceNumber,
                    $relativePath,
                    'video/mp4',
                    'mp4',
                    $marker,
                    'Ekspor otomatis cine DICOM (' .
                    $frameCount .
                    ' frame, ' .
                    number_format(
                        $fps,
                        2,
                        '.',
                        ''
                    ) .
                    ' fps). Study: ' .
                    $dicomStudy->study_instance_uid .
                    '. Series: ' .
                    $seriesNumberText .
                    '. Instance: ' .
                    $instanceNumber .
                    '.'
                );
            } catch (\Throwable $e) {
                Storage::disk('public')
                    ->delete(
                        $relativePath
                    );

                throw $e;
            }
        } finally {
            if (
                is_dir(
                    $tempDirectory
                )
            ) {
                File::deleteDirectory(
                    $tempDirectory
                );
            }
        }

        return 'mp4';
    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL SIMPLIFIED TAGS INSTANCE
    |--------------------------------------------------------------------------
    */

    private function getInstanceSimplifiedTags(
        string $instanceId
    ): array {
        $url =
            $this->orthancLocalUrl() .
            '/instances/' .
            rawurlencode($instanceId) .
            '/simplified-tags';

        $request =
            Http::acceptJson()
                ->connectTimeout(10)
                ->timeout(180);

        $username =
            config(
                'services.orthanc.username'
            );

        $password =
            config(
                'services.orthanc.password'
            );

        if (
            $username !== null &&
            $username !== ''
        ) {
            $request =
                $request->withBasicAuth(
                    (string) $username,
                    (string) $password
                );
        }

        $response =
            $request->get(
                $url
            );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Gagal membaca metadata instance dari Orthanc. HTTP ' .
                $response->status() .
                ': ' .
                Str::limit(
                    $response->body(),
                    300
                )
            );
        }

        $data =
            $response->json();

        if (!is_array($data)) {
            throw new RuntimeException(
                'Metadata instance dari Orthanc bukan JSON yang valid.'
            );
        }

        return $data;
    }


    /*
    |--------------------------------------------------------------------------
    | RENDER FRAME DICOM -> JPEG
    |--------------------------------------------------------------------------
    */

    private function getRenderedDicomFrame(
        string $instanceId,
        int $frame
    ): string {
        $url =
            $this->orthancLocalUrl() .
            '/instances/' .
            rawurlencode($instanceId) .
            '/frames/' .
            $frame .
            '/rendered';

        $request =
            Http::withHeaders([
                'Accept' =>
                    'image/jpeg',
            ])
                ->connectTimeout(10)
                ->timeout(180);

        $username =
            config(
                'services.orthanc.username'
            );

        $password =
            config(
                'services.orthanc.password'
            );

        if (
            $username !== null &&
            $username !== ''
        ) {
            $request =
                $request->withBasicAuth(
                    (string) $username,
                    (string) $password
                );
        }

        $response =
            $request->get(
                $url
            );

        if (!$response->successful()) {
            throw new RuntimeException(
                'Orthanc gagal merender frame ' .
                $frame .
                '. HTTP ' .
                $response->status() .
                ': ' .
                Str::limit(
                    $response->body(),
                    300
                )
            );
        }

        $body =
            $response->body();

        if ($body === '') {
            throw new RuntimeException(
                'Frame ' .
                $frame .
                ' dari Orthanc kosong.'
            );
        }

        return $body;
    }


    /*
    |--------------------------------------------------------------------------
    | URL LOCAL ORTHANC
    |--------------------------------------------------------------------------
    */

    private function orthancLocalUrl(): string
    {
        $url =
            rtrim(
                (string) config(
                    'services.orthanc.url',
                    'http://127.0.0.1:8042'
                ),
                '/'
            );

        if ($url === '') {
            throw new RuntimeException(
                'ORTHANC_URL belum dikonfigurasi.'
            );
        }

        return $url;
    }


    /*
    |--------------------------------------------------------------------------
    | JUMLAH FRAME
    |--------------------------------------------------------------------------
    */

    private function resolveFrameCount(
        array $tags
    ): int {
        $value =
            $this->dicomTagScalar(
                $tags['NumberOfFrames']
                ?? null
            );

        if (
            $value === null ||
            !is_numeric($value)
        ) {
            return 1;
        }

        return max(
            1,
            (int) $value
        );
    }


    /*
    |--------------------------------------------------------------------------
    | FPS CINE
    |--------------------------------------------------------------------------
    |
    | Prioritas:
    |
    | 1. CineRate
    | 2. RecommendedDisplayFrameRate
    | 3. FrameTime (ms) -> 1000 / FrameTime
    | 4. Fallback 15 fps
    |
    */

    private function resolveDicomFps(
        array $tags
    ): float {
        $cineRate =
            $this->dicomTagScalar(
                $tags['CineRate']
                ?? null
            );

        if (
            $cineRate !== null &&
            is_numeric($cineRate) &&
            (float) $cineRate > 0
        ) {
            return $this->clampFps(
                (float) $cineRate
            );
        }

        $recommendedRate =
            $this->dicomTagScalar(
                $tags['RecommendedDisplayFrameRate']
                ?? null
            );

        if (
            $recommendedRate !== null &&
            is_numeric($recommendedRate) &&
            (float) $recommendedRate > 0
        ) {
            return $this->clampFps(
                (float) $recommendedRate
            );
        }

        $frameTime =
            $this->dicomTagScalar(
                $tags['FrameTime']
                ?? null
            );

        if (
            $frameTime !== null &&
            is_numeric($frameTime) &&
            (float) $frameTime > 0
        ) {
            return $this->clampFps(
                1000 /
                (float) $frameTime
            );
        }

        return 15.0;
    }


    private function clampFps(
        float $fps
    ): float {
        return max(
            1.0,
            min(
                60.0,
                $fps
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | AMBIL NILAI TAG DICOM SEBAGAI STRING
    |--------------------------------------------------------------------------
    */

    private function dicomTagScalar(
        $value
    ): ?string {
        if ($value === null) {
            return null;
        }

        if (
            is_string($value) ||
            is_int($value) ||
            is_float($value)
        ) {
            return trim(
                (string) $value
            );
        }

        if (is_array($value)) {
            if (
                array_key_exists(
                    'Value',
                    $value
                )
            ) {
                return $this->dicomTagScalar(
                    $value['Value']
                );
            }

            $first =
                reset(
                    $value
                );

            if ($first !== false) {
                return $this->dicomTagScalar(
                    $first
                );
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | FFMPEG -> MP4 H.264
    |--------------------------------------------------------------------------
    */

    private function createMp4WithFfmpeg(
        string $framesDirectory,
        string $outputPath,
        float $fps
    ): void {
        /*
        |--------------------------------------------------------------------------
        | PATH FFMPEG
        |--------------------------------------------------------------------------
        |
        | Gunakan slash "/" di .env Windows, contoh:
        |
        | FFMPEG_PATH=C:/ffmpeg/bin/ffmpeg.exe
        |
        */

        $ffmpeg = (string) config(
            'services.ffmpeg.path',
            'ffmpeg'
        );

        $ffmpeg = trim($ffmpeg);
        $ffmpeg = trim($ffmpeg, "\"'");

        if ($ffmpeg === '') {
            $ffmpeg = 'ffmpeg';
        }

        if (
            PHP_OS_FAMILY === 'Windows' &&
            strtolower($ffmpeg) !== 'ffmpeg'
        ) {
            $ffmpeg = str_replace(
                '/',
                DIRECTORY_SEPARATOR,
                $ffmpeg
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI FFMPEG
        |--------------------------------------------------------------------------
        */

        if (
            strtolower($ffmpeg) !== 'ffmpeg' &&
            !is_file($ffmpeg)
        ) {
            throw new RuntimeException(
                'File FFmpeg tidak ditemukan pada: ' . $ffmpeg
            );
        }

        if (!is_dir($framesDirectory)) {
            throw new RuntimeException(
                'Folder temporary frame tidak ditemukan: ' .
                $framesDirectory
            );
        }

        $realFramesDirectory = realpath($framesDirectory);

        if ($realFramesDirectory === false) {
            throw new RuntimeException(
                'Folder temporary frame tidak dapat dibaca.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | CEK FFMPEG BISA DIEKSEKUSI
        |--------------------------------------------------------------------------
        */

        $versionProcess = new Process([
            $ffmpeg,
            '-version',
        ]);

        $versionProcess->setTimeout(30);

        try {
            $versionProcess->run();
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'FFmpeg tidak dapat dijalankan dari Laravel. ' .
                'Path: ' . $ffmpeg .
                '. Detail: ' . $e->getMessage()
            );
        }

        if (!$versionProcess->isSuccessful()) {
            $error = trim($versionProcess->getErrorOutput());

            if ($error === '') {
                $error = trim($versionProcess->getOutput());
            }

            throw new RuntimeException(
                'FFmpeg ditemukan tetapi gagal dijalankan: ' .
                Str::limit(
                    $error ?: 'Tidak ada pesan error dari FFmpeg.',
                    700
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | AMBIL SEMUA FRAME
        |--------------------------------------------------------------------------
        |
        | Kita TIDAK lagi memakai:
        |
        | frame_%06d.jpg
        |
        | pada command FFmpeg. Di Windows, karakter "%" pada pola sequence
        | dapat menimbulkan masalah parsing pada sebagian lingkungan proses.
        |
        | Sebagai gantinya dibuat frames.txt untuk concat demuxer FFmpeg.
        |
        */

        $frameFiles = glob(
            $realFramesDirectory .
            DIRECTORY_SEPARATOR .
            'frame_*.jpg'
        );

        if (
            $frameFiles === false ||
            empty($frameFiles)
        ) {
            throw new RuntimeException(
                'Tidak ada frame JPEG untuk dikonversi.'
            );
        }

        natsort($frameFiles);
        $frameFiles = array_values($frameFiles);

        /*
        |--------------------------------------------------------------------------
        | FPS
        |--------------------------------------------------------------------------
        */

        $fps = max(
            1.0,
            min(
                60.0,
                $fps
            )
        );

        $frameDuration = 1 / $fps;

        /*
        |--------------------------------------------------------------------------
        | BUAT CONCAT LIST
        |--------------------------------------------------------------------------
        |
        | Semua nama file di list bersifat RELATIF karena FFmpeg dijalankan
        | dengan working directory = folder frame.
        |
        */

        $concatFile = $realFramesDirectory .
            DIRECTORY_SEPARATOR .
            'frames.txt';

        $concatLines = [];

        foreach ($frameFiles as $frameFile) {
            $basename = basename($frameFile);

            /*
            | Nama frame kita dibuat sendiri (frame_000001.jpg), jadi aman.
            */
            $concatLines[] = "file '" . $basename . "'";
            $concatLines[] = 'duration ' .
                number_format(
                    $frameDuration,
                    8,
                    '.',
                    ''
                );
        }

        /*
        | FFmpeg concat tidak selalu menerapkan duration pada frame terakhir.
        | Ulangi frame terakhir sekali lagi agar durasi terakhir ikut terbaca.
        */

        $lastFrame = basename(
            $frameFiles[count($frameFiles) - 1]
        );

        $concatLines[] = "file '" . $lastFrame . "'";

        $written = file_put_contents(
            $concatFile,
            implode(PHP_EOL, $concatLines) . PHP_EOL
        );

        if (
            $written === false ||
            $written <= 0
        ) {
            throw new RuntimeException(
                'Daftar frame FFmpeg gagal dibuat.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | NAMA OUTPUT
        |--------------------------------------------------------------------------
        */

        $outputFilename = basename($outputPath);

        if (
            $outputFilename === '' ||
            strtolower(pathinfo($outputFilename, PATHINFO_EXTENSION)) !== 'mp4'
        ) {
            $outputFilename = 'output.mp4';
        }

        /*
        |--------------------------------------------------------------------------
        | KONVERSI CINE -> MP4
        |--------------------------------------------------------------------------
        |
        | Perbaikan utama Windows:
        |
        | - tidak ada frame_%06d.jpg
        | - tidak ada absolute path frame pada command
        | - input = frames.txt
        | - output = output.mp4
        | - working directory di-set langsung ke folder temporary
        |
        */

        $process = new Process(
            [
                $ffmpeg,

                '-hide_banner',
                '-loglevel',
                'error',

                '-y',

                '-f',
                'concat',

                '-safe',
                '0',

                '-i',
                'frames.txt',

                /*
                | Pastikan frame rate keluaran sesuai metadata cine DICOM.
                */
                '-r',
                number_format(
                    $fps,
                    4,
                    '.',
                    ''
                ),

                '-c:v',
                'libx264',

                '-preset',
                'medium',

                '-crf',
                '20',

                /*
                | XA tertentu dapat mempunyai dimensi ganjil.
                | H.264 yuv420p membutuhkan width/height genap.
                */
                '-vf',
                'scale=trunc(iw/2)*2:trunc(ih/2)*2',

                '-pix_fmt',
                'yuv420p',

                '-an',

                /*
                | Optimal untuk playback/download melalui browser.
                */
                '-movflags',
                '+faststart',

                $outputFilename,
            ],
            $realFramesDirectory
        );

        /*
        | Beri waktu sampai 30 menit untuk cine besar.
        */

        $process->setTimeout(1800);

        try {
            $process->run();
        } catch (\Throwable $e) {
            throw new RuntimeException(
                'FFmpeg tidak dapat menjalankan konversi. ' .
                'Executable: ' . $ffmpeg .
                '. Folder kerja: ' . $realFramesDirectory .
                '. Detail: ' . $e->getMessage()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | ERROR FFMPEG
        |--------------------------------------------------------------------------
        */

        if (!$process->isSuccessful()) {
            $error = trim(
                $process->getErrorOutput()
            );

            if ($error === '') {
                $error = trim(
                    $process->getOutput()
                );
            }

            throw new RuntimeException(
                'Konversi cine ke MP4 gagal: ' .
                Str::limit(
                    $error ?: 'FFmpeg berhenti tanpa memberikan pesan error.',
                    1200
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | VALIDASI HASIL
        |--------------------------------------------------------------------------
        */

        $generatedFile =
            $realFramesDirectory .
            DIRECTORY_SEPARATOR .
            $outputFilename;

        clearstatcache(
            true,
            $generatedFile
        );

        if (
            !is_file($generatedFile) ||
            filesize($generatedFile) === false ||
            filesize($generatedFile) <= 0
        ) {
            throw new RuntimeException(
                'FFmpeg selesai tanpa error tetapi file MP4 tidak terbentuk.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | JIKA OUTPUT PATH BERBEDA, PINDAHKAN
        |--------------------------------------------------------------------------
        */

        $generatedReal =
            realpath(
                $generatedFile
            );

        $outputDirectory =
            dirname(
                $outputPath
            );

        $outputDirectoryReal =
            realpath(
                $outputDirectory
            );

        $targetComparable =
            $outputDirectoryReal !== false
                ? $outputDirectoryReal .
                    DIRECTORY_SEPARATOR .
                    basename($outputPath)
                : $outputPath;

        $samePath =
            PHP_OS_FAMILY === 'Windows'
                ? strcasecmp(
                    (string) $generatedReal,
                    (string) $targetComparable
                ) === 0
                : (string) $generatedReal ===
                    (string) $targetComparable;

        if (!$samePath) {
            if (
                file_exists($outputPath) &&
                !@unlink($outputPath)
            ) {
                throw new RuntimeException(
                    'File MP4 lama tidak dapat dihapus: ' .
                    $outputPath
                );
            }

            if (
                !@rename(
                    $generatedFile,
                    $outputPath
                )
            ) {
                throw new RuntimeException(
                    'MP4 berhasil dibuat tetapi gagal dipindahkan ke: ' .
                    $outputPath
                );
            }
        }
    }


    /*
    |--------------------------------------------------------------------------
    | CEK HASIL EKSPOR SUDAH ADA
    |--------------------------------------------------------------------------
    */

    private function exportedPatientDocumentExists(
        Patient $patient,
        string $marker,
        string $relativePath
    ): bool {
        $columns =
            $this->patientDocumentColumns();

        $query =
            PatientDocument::query()
                ->where(
                    $columns['patient'],
                    $patient->id
                );

        if ($columns['notes']) {
            $query->where(
                $columns['notes'],
                'like',
                '%' .
                $marker .
                '%'
            );
        } else {
            $query->where(
                $columns['path'],
                $relativePath
            );
        }

        $document =
            $query->first();

        if (!$document) {
            return false;
        }

        /*
        | Jika record dan file sama-sama ada -> benar-benar sudah diekspor.
        */

        $existingPath =
            $document->getAttribute(
                $columns['path']
            );

        if (
            is_string($existingPath) &&
            $existingPath !== '' &&
            Storage::disk('public')
                ->exists(
                    $existingPath
                )
        ) {
            return true;
        }

        /*
        | Record ada tetapi file fisik hilang.
        | Hapus record lama agar proses ekspor bisa memperbaikinya.
        */

        try {
            $document->delete();
        } catch (\Throwable $e) {
            Log::warning(
                'DICOM Export - record dokumen lama gagal dihapus.',
                [
                    'patient_id' =>
                        $patient->id,

                    'relative_path' =>
                        $relativePath,

                    'error' =>
                        $e->getMessage(),
                ]
            );
        }

        return false;
    }


    /*
    |--------------------------------------------------------------------------
    | SIMPAN KE PATIENT_DOCUMENTS
    |--------------------------------------------------------------------------
    */

    private function savePatientDocument(
        Patient $patient,
        string $documentName,
        string $relativePath,
        string $mimeType,
        string $extension,
        string $marker,
        string $description
    ): PatientDocument {
        $columns =
            $this->patientDocumentColumns();

        $document =
            new PatientDocument();

        $document->setAttribute(
            $columns['patient'],
            $patient->id
        );

        $document->setAttribute(
            $columns['name'],
            mb_substr(
                $documentName,
                0,
                250
            )
        );

        $document->setAttribute(
            $columns['path'],
            $relativePath
        );

        /*
        | file_type:
        | Simpan MIME type karena lebih informatif:
        | image/jpeg atau video/mp4.
        */

        if ($columns['type']) {
            $document->setAttribute(
                $columns['type'],
                $mimeType
            );
        }

        /*
        | Jika tabel mempunyai kolom mime_type terpisah.
        */

        if (
            $columns['mime'] &&
            $columns['mime'] !==
            $columns['type']
        ) {
            $document->setAttribute(
                $columns['mime'],
                $mimeType
            );
        }

        /*
        | Jika tabel mempunyai kolom extension.
        */

        if ($columns['extension']) {
            $document->setAttribute(
                $columns['extension'],
                $extension
            );
        }

        /*
        | Marker dipakai untuk mencegah duplikasi ekspor.
        */

        if ($columns['notes']) {
            $document->setAttribute(
                $columns['notes'],
                $marker .
                ' | ' .
                $description
            );
        }

        if ($columns['size']) {
            $fileSize =
                Storage::disk('public')
                    ->size(
                        $relativePath
                    );

            $document->setAttribute(
                $columns['size'],
                $fileSize
            );
        }

        $document->save();

        return $document;
    }


    /*
    |--------------------------------------------------------------------------
    | DETEKSI STRUKTUR TABEL PATIENT_DOCUMENTS
    |--------------------------------------------------------------------------
    |
    | Dibuat fleksibel agar tetap kompatibel bila nama kolom Anda sedikit
    | berbeda dari document_name / file_path / file_type.
    |
    */

    private function patientDocumentColumns(): array
    {
        static $cache = null;

        if ($cache !== null) {
            return $cache;
        }

        if (!class_exists(PatientDocument::class)) {
            throw new RuntimeException(
                'Model App\\Models\\PatientDocument belum tersedia.'
            );
        }

        $model =
            new PatientDocument();

        $table =
            $model->getTable();

        if (!Schema::hasTable($table)) {
            throw new RuntimeException(
                'Tabel ' .
                $table .
                ' tidak ditemukan. Ekspor ke Dokumen pasien belum dapat dilakukan.'
            );
        }

        $patientColumn =
            $this->firstExistingColumn(
                $table,
                [
                    'patient_id',
                ]
            );

        $nameColumn =
            $this->firstExistingColumn(
                $table,
                [
                    'document_name',
                    'name',
                    'title',
                ]
            );

        $pathColumn =
            $this->firstExistingColumn(
                $table,
                [
                    'file_path',
                    'path',
                ]
            );

        if (
            !$patientColumn ||
            !$nameColumn ||
            !$pathColumn
        ) {
            throw new RuntimeException(
                'Struktur tabel ' .
                $table .
                ' belum sesuai. Dibutuhkan minimal kolom patient_id, ' .
                'document_name/name, dan file_path/path.'
            );
        }

        $cache = [
            'table' =>
                $table,

            'patient' =>
                $patientColumn,

            'name' =>
                $nameColumn,

            'path' =>
                $pathColumn,

            'type' =>
                $this->firstExistingColumn(
                    $table,
                    [
                        'file_type',
                        'type',
                    ]
                ),

            'mime' =>
                $this->firstExistingColumn(
                    $table,
                    [
                        'mime_type',
                    ]
                ),

            'extension' =>
                $this->firstExistingColumn(
                    $table,
                    [
                        'extension',
                        'file_extension',
                    ]
                ),

            'notes' =>
                $this->firstExistingColumn(
                    $table,
                    [
                        'notes',
                        'description',
                    ]
                ),

            'size' =>
                $this->firstExistingColumn(
                    $table,
                    [
                        'file_size',
                        'size',
                    ]
                ),
        ];

        return $cache;
    }


    private function firstExistingColumn(
        string $table,
        array $columns
    ): ?string {
        foreach ($columns as $column) {
            if (
                Schema::hasColumn(
                    $table,
                    $column
                )
            ) {
                return $column;
            }
        }

        return null;
    }


    /*
    |--------------------------------------------------------------------------
    | AMANKAN LABEL NAMA FILE
    |--------------------------------------------------------------------------
    */

    private function safeFileLabel(
        string $value
    ): string {
        $value =
            preg_replace(
                '/[\\\\\\/\\:\\*\\?"<>\\|]+/',
                ' ',
                $value
            );

        $value =
            preg_replace(
                '/\\s+/',
                ' ',
                (string) $value
            );

        $value =
            trim(
                (string) $value
            );

        if ($value === '') {
            return 'DICOM';
        }

        return mb_substr(
            $value,
            0,
            80
        );
    }


    private function numberLabel(
        $value
    ): string {
        $value =
            preg_replace(
                '/[^A-Za-z0-9_-]/',
                '',
                (string) $value
            );

        if ($value === '') {
            return '1';
        }

        return $value;
    }


    /*
    |--------------------------------------------------------------------------
    | SINKRONISASI STUDY ORTHANC -> DATABASE
    |--------------------------------------------------------------------------
    */

    private function syncStudyToDatabase(
        Patient $patient,
        string $studyId,
        OrthancService $orthanc
    ): DicomStudy {
        /*
        |--------------------------------------------------------------------------
        | AMBIL STUDY
        |--------------------------------------------------------------------------
        */

        $study =
            $orthanc->study(
                $studyId
            );

        $studyTags =
            $study['MainDicomTags']
            ?? [];

        $patientTags =
            $study['PatientMainDicomTags']
            ?? [];

        /*
        |--------------------------------------------------------------------------
        | STUDY INSTANCE UID
        |--------------------------------------------------------------------------
        */

        $studyInstanceUid =
            $studyTags['StudyInstanceUID']
            ?? null;

        if (!$studyInstanceUid) {
            throw new RuntimeException(
                'StudyInstanceUID tidak ditemukan pada data DICOM.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | SERIES
        |--------------------------------------------------------------------------
        */

        $seriesIds =
            $study['Series']
            ?? [];

        $seriesCount =
            count($seriesIds);

        $instanceCount = 0;
        $modalities = [];

        foreach ($seriesIds as $seriesId) {
            try {
                $series =
                    $orthanc->series(
                        $seriesId
                    );

                /*
                |--------------------------------------------------------------------------
                | HITUNG INSTANCE
                |--------------------------------------------------------------------------
                */

                $instanceCount +=
                    count(
                        $series['Instances']
                        ?? []
                    );

                /*
                |--------------------------------------------------------------------------
                | MODALITY
                |--------------------------------------------------------------------------
                */

                $modality =
                    $series['MainDicomTags']['Modality']
                    ?? null;

                if (
                    $modality &&
                    !in_array(
                        $modality,
                        $modalities,
                        true
                    )
                ) {
                    $modalities[] = $modality;
                }
            } catch (\Throwable $e) {
                Log::warning(
                    'Gagal membaca Series saat sinkronisasi DICOM.',
                    [
                        'patient_id' =>
                            $patient->id,

                        'series_id' =>
                            $seriesId,

                        'error' =>
                            $e->getMessage(),
                    ]
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | MODALITY
        |--------------------------------------------------------------------------
        */

        $modality =
            !empty($modalities)
                ? implode(',', $modalities)
                : null;

        if ($modality) {
            $modality =
                mb_substr(
                    $modality,
                    0,
                    20
                );
        }

        /*
        |--------------------------------------------------------------------------
        | STUDY DATE
        |--------------------------------------------------------------------------
        */

        $studyDate =
            $this->normalizeDicomDate(
                $studyTags['StudyDate']
                ?? null
            );

        /*
        |--------------------------------------------------------------------------
        | CEK STUDY SUDAH ADA
        |--------------------------------------------------------------------------
        */

        $dicomStudy =
            DicomStudy::where(
                'study_instance_uid',
                $studyInstanceUid
            )
            ->orWhere(
                'orthanc_study_id',
                $studyId
            )
            ->first();

        /*
        |--------------------------------------------------------------------------
        | JANGAN SAMPAI STUDY TERHUBUNG KE PASIEN LAIN
        |--------------------------------------------------------------------------
        */

        if (
            $dicomStudy &&
            (string) $dicomStudy->patient_id !==
            (string) $patient->id
        ) {
            throw new RuntimeException(
                'Study DICOM ini sudah terhubung dengan pasien lain.'
            );
        }

        if (!$dicomStudy) {
            $dicomStudy =
                new DicomStudy();
        }

        /*
        |--------------------------------------------------------------------------
        | SIMPAN DATA
        |--------------------------------------------------------------------------
        */

        $dicomStudy->patient_id =
            $patient->id;

        $dicomStudy->orthanc_study_id =
            $studyId;

        $dicomStudy->study_instance_uid =
            $studyInstanceUid;

        $dicomStudy->study_date =
            $studyDate;

        $dicomStudy->study_time =
            $studyTags['StudyTime']
            ?? null;

        $dicomStudy->accession_number =
            $studyTags['AccessionNumber']
            ?? null;

        $dicomStudy->study_description =
            $studyTags['StudyDescription']
            ?? null;

        $dicomStudy->modality =
            $modality;

        $dicomStudy->dicom_patient_id =
            $patientTags['PatientID']
            ?? null;

        $dicomStudy->dicom_patient_name =
            $patientTags['PatientName']
            ?? null;

        $dicomStudy->series_count =
            $seriesCount;

        $dicomStudy->instance_count =
            $instanceCount;

        /*
        |--------------------------------------------------------------------------
        | FIELD OPSIONAL
        |--------------------------------------------------------------------------
        */

        if (
            Schema::hasColumn(
                'dicom_studies',
                'import_status'
            )
        ) {
            $dicomStudy->import_status =
                'completed';
        }

        if (
            Schema::hasColumn(
                'dicom_studies',
                'import_error'
            )
        ) {
            $dicomStudy->import_error =
                null;
        }

        if (
            Schema::hasColumn(
                'dicom_studies',
                'uploaded_by'
            )
        ) {
            $dicomStudy->uploaded_by =
                auth()->id();
        }

        $dicomStudy->save();

        return $dicomStudy;
    }

    /*
    |--------------------------------------------------------------------------
    | AMBIL STUDY ID DARI RESPONSE UPLOAD
    |--------------------------------------------------------------------------
    */

    private function extractStudyIdsFromUploadResponse(
        array $response
    ): array {
        $studyIds = [];

        $walker = function ($value) use (
            &$walker,
            &$studyIds
        ) {
            if (!is_array($value)) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSE INSTANCE BIASA
            |--------------------------------------------------------------------------
            */

            if (
                isset($value['ParentStudy']) &&
                is_string($value['ParentStudy']) &&
                trim($value['ParentStudy']) !== ''
            ) {
                $studyIds[] =
                    trim(
                        $value['ParentStudy']
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | RESPONSE NESTED
            |--------------------------------------------------------------------------
            */

            foreach ($value as $child) {
                if (is_array($child)) {
                    $walker($child);
                }
            }
        };

        $walker($response);

        return array_values(
            array_unique(
                $studyIds
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | NORMALISASI DICOM DATE
    |--------------------------------------------------------------------------
    |
    | DICOM:
    | 20260901
    |
    | DATABASE:
    | 2026-09-01
    |
    */

    private function normalizeDicomDate(
        ?string $value
    ): ?string {
        if (!$value) {
            return null;
        }

        $value = trim($value);

        if (
            !preg_match(
                '/^(\d{4})(\d{2})(\d{2})$/',
                $value,
                $matches
            )
        ) {
            return null;
        }

        $year =
            (int) $matches[1];

        $month =
            (int) $matches[2];

        $day =
            (int) $matches[3];

        if (
            !checkdate(
                $month,
                $day,
                $year
            )
        ) {
            return null;
        }

        return sprintf(
            '%04d-%02d-%02d',
            $year,
            $month,
            $day
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PASTIKAN STUDY MILIK PASIEN
    |--------------------------------------------------------------------------
    */

    private function ensureStudyBelongsToPatient(
        Patient $patient,
        DicomStudy $dicomStudy
    ): void {
        abort_unless(
            (string) $dicomStudy->patient_id ===
            (string) $patient->id,
            404
        );
    }
}
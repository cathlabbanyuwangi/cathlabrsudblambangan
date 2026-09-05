<?php

use App\Http\Controllers\ActionController;
use App\Http\Controllers\ActionCategoryController;
use App\Http\Controllers\ActionRecordController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DoctorController;
use App\Http\Controllers\InsuranceController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportSelectionController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SubDivisionController;
use App\Http\Controllers\SupportingOptionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MasterBackupController;
use App\Http\Controllers\QueueCheckController;
use App\Http\Controllers\PublicRegistrationController; 
use App\Http\Controllers\CheckBhpController;
use App\Http\Controllers\DicomController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Route publik untuk cek perkiraan jadwal antrean pasien (Tanpa login)
Route::post('/cek-antrean', [QueueCheckController::class, 'search'])->name('queue.check');

// Route publik untuk kirim pendaftaran mandiri (Dilindungi throttle untuk cegah bom/spam bot)
Route::middleware(['throttle:3,1'])->group(function () {
    Route::post('/daftar-mandiri', [PublicRegistrationController::class, 'store'])->name('public.register.store');
});



// Dashboard dilindungi permission 'akses-dashboard'
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:akses-dashboard'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    
    /*
    |--------------------------------------------------------------------------
    | USER PROFILE MANAGEMENT
    |--------------------------------------------------------------------------
    */

    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    /*
    |--------------------------------------------------------------------------
    | MODERASI PENDAFTARAN PUBLIK (STAGING PENGISIAN DARI LUAR - KHUSUS ADMIN)
    |--------------------------------------------------------------------------
    */

    Route::prefix('public-registrations')
        ->name('public-registrations.')
        ->middleware(['permission:pendaftaran-pasien'])
        ->group(function () {

            Route::get('/', [PublicRegistrationController::class, 'index'])
                ->name('index');

            Route::post('/{publicRegistration}/approve', [PublicRegistrationController::class, 'approve'])
                ->name('approve');

            Route::delete('/{publicRegistration}', [PublicRegistrationController::class, 'destroy'])
                ->name('destroy');
        });

    /*
    |--------------------------------------------------------------------------
    | MASTER DATA MANAGEMENT (Disesuaikan dengan database)
    |--------------------------------------------------------------------------
    */

    Route::resource('roles', RoleController::class)
        ->except(['show'])
        ->middleware(['permission:kelola-role']);

    Route::resource('users', UserController::class)
        ->except(['show'])
        ->middleware(['permission:kelola-user']); 

    Route::resource('insurances', InsuranceController::class)
        ->except(['show'])
        ->middleware(['permission:kelola-jaminan']);

    Route::resource('supporting-options', SupportingOptionController::class)
        ->except(['show'])
        ->middleware(['permission:kelola-penunjang']);
    
    Route::resource('categories', ActionCategoryController::class)
        ->only(['index', 'store', 'destroy'])
        ->middleware(['permission:kelola-kategori-divisi']);

    Route::resource('sub-divisions', SubDivisionController::class)
        ->only(['index', 'store', 'destroy'])
        ->middleware(['permission:kelola-sub-divisi']);

    Route::resource('actions', ActionController::class)
        ->only(['index', 'store', 'destroy'])
        ->middleware(['permission:kelola-tindakan']);

    Route::resource('doctors', DoctorController::class)
        ->only(['index', 'store', 'update', 'destroy'])
        ->middleware(['permission:kelola-dokter']);

    // Backup Laporan Data
    Route::get('/master/backup-laporan-data', [MasterBackupController::class, 'downloadBackup'])
        ->name('master.backup.laporan')
        ->middleware(['permission:backup-laporan']); 

    /*
    |--------------------------------------------------------------------------
    | API AJAX ENDPOINTS (DYNAMIC DROPDOWNS)
    |--------------------------------------------------------------------------
    */

    Route::get('sub-divisions/by-category/{id}', [SubDivisionController::class, 'getByCategory']);
    Route::get('actions/by-category/{id}', [ActionRecordController::class, 'getActionsByCategory']);
    Route::get('doctors/by-sub-division/{id}', [ActionRecordController::class, 'getDoctorsBySubDivision']);
    Route::get('actions/by-doctor/{doctor}', [ActionRecordController::class, 'getActionsByDoctor']);

    /*
    |--------------------------------------------------------------------------
    | PATIENT CONTEXT & SUB-MENUS
    |--------------------------------------------------------------------------
    */

    Route::get('patients/action-queue', [PatientController::class, 'actionQueue'])
        ->name('patients.action-queue')
        ->middleware(['permission:pendaftaran-pasien']);

    Route::prefix('patients/{patient}')
        ->name('patients.')
        ->group(function () {

            Route::get('call-history', [PatientController::class, 'callHistory'])
                ->name('call-history')
                ->middleware(['permission:riwayat-tindakan']);

            Route::get('actions-history', [PatientController::class, 'actionsHistory'])
                ->name('actions-history')
                ->middleware(['permission:riwayat-tindakan']);
        
            // Dokumen Pasien
            Route::get('documents', [PatientController::class, 'documents'])
                ->name('documents')
                ->middleware(['permission:pendaftaran-pasien']);

            Route::post('documents', [PatientController::class, 'storeDocument'])
                ->name('documents.store')
                ->middleware(['permission:pendaftaran-pasien']);

            // Preview aman: JPEG / PNG / WEBP / PDF / MP4 / WEBM / MOV
            Route::get('documents/{document}/preview', [PatientController::class, 'previewDocument'])
                ->name('documents.preview')
                ->middleware(['permission:pendaftaran-pasien']);

            // Download aman melalui Laravel
            Route::get('documents/{document}/download', [PatientController::class, 'downloadDocument'])
                ->name('documents.download')
                ->middleware(['permission:pendaftaran-pasien']);

            // Edit tanggal dokumen tanpa mengubah created_at
            Route::patch('documents/{document}/date', [PatientController::class, 'updateDocumentDate'])
                ->name('documents.date.update')
                ->middleware(['permission:pendaftaran-pasien']);

            Route::delete('documents/{document}', [PatientController::class, 'destroyDocument'])
                ->name('documents.destroy')
                ->middleware(['permission:pendaftaran-pasien']);


            /*
            |--------------------------------------------------------------------------
            | DICOM / ANGIOGRAPHY
            |--------------------------------------------------------------------------
            */

            Route::prefix('dicom')
                ->name('dicom.')
                ->middleware(['permission:riwayat-tindakan'])
                ->group(function () {

                    // Daftar seluruh pemeriksaan DICOM pasien
                    Route::get('/', [DicomController::class, 'index'])
                        ->name('index');

                    // Halaman upload DICOM
                    Route::get('/upload', [DicomController::class, 'create'])
                        ->name('create');

                    // Proses upload DICOM ke Orthanc
                    Route::post('/', [DicomController::class, 'store'])
                        ->name('store');

                    // Detail pemeriksaan DICOM
                    Route::get('/{dicomStudy}', [DicomController::class, 'show'])
                        ->name('show');

                    // Buka DICOM Viewer
                    Route::get('/{dicomStudy}/viewer', [DicomController::class, 'viewer'])
                        ->name('viewer');

                    Route::post('/{dicomStudy}/export',[DicomController::class, 'export'])
                        ->name('export');

                    // Hapus pemeriksaan DICOM
                    Route::delete('/{dicomStudy}', [DicomController::class, 'destroy'])
                        ->name('destroy');
                });


            // BHP & Import PDF BHP
            Route::get('bhp', [PatientController::class, 'bhp'])
                ->name('bhp')
                ->middleware(['permission:riwayat-tindakan']);

            Route::post('bhp/import-pdf', [PatientController::class, 'importBhpPdf'])
                ->name('bhp.import-pdf')
                ->middleware(['permission:riwayat-tindakan']);

            Route::delete('bhp/{receiptNumber}', [PatientController::class, 'destroyBhp'])
                ->name('bhp.destroy')
                ->middleware(['permission:riwayat-tindakan']);

            // Patient Action Records (Transaksi Tindakan)
            Route::get('actions/create', [ActionRecordController::class, 'create'])
                ->name('actions.create')
                ->middleware(['permission:riwayat-tindakan']);

            Route::post('actions', [ActionRecordController::class, 'store'])
                ->name('actions.store')
                ->middleware(['permission:riwayat-tindakan']);

            Route::get('actions/{actionRecord}', [ActionRecordController::class, 'show'])
                ->name('actions.show')
                ->middleware(['permission:riwayat-tindakan']);

            Route::get('actions/{actionRecord}/edit', [ActionRecordController::class, 'edit'])
                ->name('actions.edit')
                ->middleware(['permission:riwayat-tindakan']);

            Route::put('actions/{actionRecord}', [ActionRecordController::class, 'update'])
                ->name('actions.update')
                ->middleware(['permission:riwayat-tindakan']);

            Route::delete('actions/{actionRecord}', [ActionRecordController::class, 'destroy'])
                ->name('actions.destroy')
                ->middleware(['permission:riwayat-tindakan']);

            // Door-to-Balloon (D2B) Audit Routes
            Route::get('actions/{actionRecord}/door-to-balloon', [ActionRecordController::class, 'editDoorToBalloon'])
                ->name('actions.door-to-balloon.edit')
                ->middleware(['permission:riwayat-tindakan']);

            Route::put('actions/{actionRecord}/door-to-balloon', [ActionRecordController::class, 'updateDoorToBalloon'])
                ->name('actions.door-to-balloon.update')
                ->middleware(['permission:riwayat-tindakan']);

            Route::get('actions/{actionRecord}/door-to-balloon/print', [ActionRecordController::class, 'printDoorToBalloon'])
                ->name('actions.door-to-balloon.print')
                ->middleware(['permission:cetak-laporan']);

            // Patient Status & Call Management
            Route::post('call', [PatientController::class, 'callPatient'])
                ->name('call')
                ->middleware(['permission:pendaftaran-pasien']);

            Route::post('reject', [PatientController::class, 'rejectPatient'])
                ->name('reject')
                ->middleware(['permission:pendaftaran-pasien']);

            Route::post('reset-status', [PatientController::class, 'resetPatientStatus'])
                ->name('reset-status')
                ->middleware(['permission:pendaftaran-pasien']);
        
            // Daftar Ulang Pasien Selesai
            Route::post('reregister', [PatientController::class, 'reregister'])
                ->name('reregister')
                ->middleware(['permission:pendaftaran-pasien']);

            // Generate Token Akses Portal Pasien oleh Admin
            Route::post('generate-token', [PatientController::class, 'generatePortalToken'])
                ->name('generate-portal-token')
                ->middleware(['permission:pendaftaran-pasien']);
        });

    /*
    |--------------------------------------------------------------------------
    | EXCEL OPERATIONS (TEMPLATE, IMPORT, EXPORT) - PATIENT
    |--------------------------------------------------------------------------
    */

    Route::prefix('patients')
        ->name('patients.')
        ->middleware(['permission:pendaftaran-pasien'])
        ->group(function () {

            Route::get('template', [PatientController::class, 'downloadTemplate'])
                ->name('download-template');

            Route::post('import', [PatientController::class, 'import'])
                ->name('import');

            Route::get('export', [PatientController::class, 'export'])
                ->name('export');
        });

    /*
|--------------------------------------------------------------------------
| QR CODE VIEW (DILINDUNGI IZIN PENDAFTARAN PASIEN)
|--------------------------------------------------------------------------
*/
    Route::get('/portal-pasien/qrcode-view', function () {
        return view('patients.portal.qrcode');
    })->middleware(['permission:pendaftaran-pasien'])->name('patient.portal.qrcode-view');

    /*
    |--------------------------------------------------------------------------
    | MAIN PATIENT RESOURCE
    |--------------------------------------------------------------------------
    */

    Route::resource('patients', PatientController::class)
        ->middleware(['permission:pendaftaran-pasien']);

    /*
    |--------------------------------------------------------------------------
    | GLOBAL ACTION RECORDS HISTORY
    |--------------------------------------------------------------------------
    */

    Route::prefix('actions-history')
        ->name('actions.history.')
        ->middleware(['permission:riwayat-tindakan'])
        ->group(function () {

            Route::get('/', [ActionRecordController::class, 'index'])
                ->name('index');

            Route::get('/export', [ActionRecordController::class, 'export'])
                ->name('export');

            Route::post('/import', [ActionRecordController::class, 'import'])
                ->name('import');

            Route::get('/template', [ActionRecordController::class, 'downloadTemplate'])
                ->name('template');
        });

    /*
    |--------------------------------------------------------------------------
    | REPORTS & STATISTICS
    |--------------------------------------------------------------------------
    */

    Route::prefix('reports')->group(function () {

        Route::get('/', [ReportController::class, 'index'])
            ->name('reports.index')
            ->middleware(['permission:laporan-ringkasan']);

        Route::get('/clinical', [ReportController::class, 'clinical'])
            ->name('reports.clinical')
            ->middleware(['permission:laporan-klinis']);

        Route::get('/operational', [ReportController::class, 'operational'])
            ->name('reports.operational')
            ->middleware(['permission:laporan-operasional']);

        Route::get('/operational/export', [ReportController::class, 'exportOperational'])
            ->name('reports.operational.export')
            ->middleware(['permission:laporan-operasional']);

        Route::get('/recapitulation', [ReportController::class, 'recapitulation'])
            ->name('reports.recapitulation')
            ->middleware(['permission:laporan-rekapitulasi']);
        
        Route::get('/selection', [ReportSelectionController::class, 'index'])
            ->name('reports.selection.index')
            ->middleware(['permission:cetak-laporan']);

        Route::post('/selection/print', [ReportSelectionController::class, 'print'])
            ->name('reports.selection.print')
            ->middleware(['permission:cetak-laporan']);
    });

    /*
    |--------------------------------------------------------------------------
    | CEK BHP (BAHAN HABIS PAKAI)
    |--------------------------------------------------------------------------
    */

    Route::get('/check-bhp', [CheckBhpController::class, 'index'])
        ->name('check-bhp.index')
        ->middleware(['permission:cek-bhp']);


    /*
    |--------------------------------------------------------------------------
    | TEST KONEKSI ORTHANC
    |--------------------------------------------------------------------------
    */

    Route::get('/dicom/test', [DicomController::class, 'test']);

});

require __DIR__.'/auth.php';
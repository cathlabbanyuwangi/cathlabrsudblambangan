<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Exports\MasterBackupExport;
use Maatwebsite\Excel\Facades\Excel;

class MasterBackupController extends Controller
{
    public function downloadBackup()
    {
        $records = DB::table('action_records')
            ->leftJoin('patients', 'action_records.patient_id', '=', 'patients.id')
            ->leftJoin('actions', 'action_records.action_id', '=', 'actions.id')
            ->leftJoin('action_categories', 'actions.action_category_id', '=', 'action_categories.id')
            ->leftJoin('doctors', 'action_records.doctor_id', '=', 'doctors.id')
            ->leftJoin('insurances', 'patients.insurance_id', '=', 'insurances.id')
            ->select(
                'action_records.action_date',
                'patients.medical_record_number',
                'patients.name as patient_name',
                'patients.address',
                'patients.date_of_birth',
                'patients.gender',
                'insurances.name as insurance_name',
                'action_records.origin_ward',
                'action_categories.name as category_name',
                'actions.name as action_name',
                'doctors.name as doctor_name',
                'action_records.conclusion',
                'action_records.d2b_igd_time',
                'action_records.d2b_balloon_dilatation_time',
                'action_records.is_cito',
                'action_records.is_successful'
            )
            ->latest('action_records.action_date')
            ->get();

        if ($records->isEmpty()) {
            return back()->with('error', 'Belum ada data untuk dibackup.');
        }

        $filename = 'Backup_Lengkap_Master_Cathlab_' . date('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(new MasterBackupExport($records), $filename);
    }
}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\ActionCategory;
use App\Models\Action;
use Carbon\Carbon;
use App\Exports\CustomCathlabReportExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportSelectionController extends Controller
{
    public function index()
    {
        $categories = ActionCategory::all();
        $actions = Action::all();
        return view('reports.selection.index', compact('categories', 'actions'));
    }

    public function print(Request $request)
    {
        $startMonth = $request->input('start_month');
        $endMonth = $request->input('end_month');
        $selectedDivisions = $request->input('divisions', []);
        $selectedActions = $request->input('actions', []);

        $query = DB::table('action_records')
            ->leftJoin('patients', 'action_records.patient_id', '=', 'patients.id')
            ->leftJoin('actions', 'action_records.action_id', '=', 'actions.id')
            ->leftJoin('insurances', 'patients.insurance_id', '=', 'insurances.id') 
            ->select(
                'action_records.action_date',
                'action_records.created_at',
                'patients.medical_record_number',
                'patients.name as patient_name',
                'patients.gender',
                'patients.date_of_birth', // <-- Ditambahkan di sini agar tanggal lahir ikut terambil
                'insurances.name as insurance_name',
                'action_records.conclusion as diagnosis', 
                'actions.name as action_name'
            );

        if ($startMonth) {
            $query->where('action_records.action_date', '>=', $startMonth . '-01 00:00:00');
        }
        if ($endMonth) {
            $query->where('action_records.action_date', '<=', Carbon::parse($endMonth)->endOfMonth());
        }
        if (!empty($selectedDivisions)) {
            $query->whereIn('actions.action_category_id', $selectedDivisions);
        }
        if (!empty($selectedActions)) {
            $query->whereIn('action_records.action_id', $selectedActions);
        }

        $records = $query->latest('action_records.action_date')->get();

        if ($records->isEmpty()) {
            return back()->with('error', 'Tidak ada data ditemukan dalam rentang tersebut.');
        }

        return Excel::download(new CustomCathlabReportExport($records), 'Laporan_Cathlab_' . date('Y-m-d') . '.xlsx');
    }
}
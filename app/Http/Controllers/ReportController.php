<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ActionRecord;
use App\Models\ActionCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class ReportController extends Controller
{
    /**
     * Tampilkan halaman utama laporan & statistik umum.
     */
    public function index()
    {
        $now = Carbon::now();
        $startThisMonth = $now->copy()->startOfMonth();
        $startLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endLastMonth = $now->copy()->subMonth()->endOfMonth();

        // Total Pasien & Comparison Period (%)
        $totalThisMonth = Patient::where('created_at', '>=', $startThisMonth)->count();
        $totalLastMonth = Patient::whereBetween('created_at', [$startLastMonth, $endLastMonth])->count();
        $patientGrowth = $totalLastMonth > 0 ? round((($totalThisMonth - $totalLastMonth) / $totalLastMonth) * 100, 1) : 0;

        $stats = [
            'total' => $totalThisMonth,
            'patient_growth' => $patientGrowth,
            'completed' => Patient::where('status', 'pernah_tindakan')->where('updated_at', '>=', $startThisMonth)->count(),
            'pending' => Patient::where('status', 'pending')->count(),
            'priority' => Patient::where('is_priority', true)->where('status', '!=', 'pernah_tindakan')->count(),
        ];

        // Aktivitas Departemen / Kategori Tindakan
        $categories = ActionCategory::all();
        $departmentPerformance = $categories->map(function($cat) {
            $count = 0;
            if (Schema::hasColumn('action_records', 'action_category_id')) {
                $count = ActionRecord::where('action_category_id', $cat->id)->count();
            } elseif (Schema::hasColumn('action_records', 'category_id')) {
                $count = ActionRecord::where('category_id', $cat->id)->count();
            }
            $cat->records_count = $count;
            return $cat;
        });

        // Tren Pasien 7 Hari Terakhir
        $trends = Patient::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get();

        // Data Pasien untuk Audit / Tabel
        $patients = Patient::with(['insurance'])->latest()->paginate(15);

        // Top 5 Procedure Types (Tindakan Cathlab Terbanyak)
        $topProcedures = DB::table('action_records')
            ->join('actions', 'action_records.action_id', '=', 'actions.id')
            ->select('actions.name', DB::raw('count(*) as total'))
            ->groupBy('actions.id', 'actions.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return view('reports.index', compact('stats', 'departmentPerformance', 'trends', 'patients', 'topProcedures'));
    }

    /**
     * Laporan Performa Klinis & Outcome Dokter (Standar Akreditasi KARS / JCI).
     */
    public function clinical()
    {
        // Deteksi ketersediaan kolom klinis pada tabel action_records secara aman
        $hasTimi = Schema::hasColumn('action_records', 'timi_flow_post');
        $hasContrast = Schema::hasColumn('action_records', 'contrast_volume');
        $hasFluro = Schema::hasColumn('action_records', 'fluro_time');
        $hasSuccess = Schema::hasColumn('action_records', 'is_successful');

        // 1. Mengambil ringkasan KPI Global (Metrics) untuk kartu laporan atas
        $metricsQuery = DB::table('action_records');
        
        $metricSelects = [
            DB::raw('count(*) as total_procedures')
        ];

        if ($hasSuccess) {
            $metricSelects[] = DB::raw('sum(case when is_successful = 1 then 1 else 0 end) as success_count');
        } else {
            $metricSelects[] = DB::raw('count(*) as success_count');
        }

        if ($hasTimi) {
            $metricSelects[] = DB::raw('sum(case when timi_flow_post = 3 then 1 else 0 end) as timi_flow_3_count');
        } else {
            $metricSelects[] = DB::raw('0 as timi_flow_3_count');
        }

        if ($hasContrast) {
            $metricSelects[] = DB::raw('avg(contrast_volume) as avg_contrast');
        } else {
            $metricSelects[] = DB::raw('0 as avg_contrast');
        }

        if ($hasFluro) {
            $metricSelects[] = DB::raw('avg(fluro_time) as avg_fluro_time');
        } else {
            $metricSelects[] = DB::raw('0 as avg_fluro_time');
        }

        $metrics = $metricsQuery->select($metricSelects)->first();

        // 2. Mengambil rekapitulasi performa per dokter spesialis untuk tabel audit
        $query = DB::table('action_records')
            ->join('doctors', 'action_records.doctor_id', '=', 'doctors.id')
            ->select(
                'doctors.id',
                'doctors.name',
                DB::raw('count(*) as total_procedures')
            );

        if ($hasSuccess) {
            $query->addSelect(DB::raw('sum(case when action_records.is_successful = 1 then 1 else 0 end) as success_count'));
        } else {
            $query->addSelect(DB::raw('count(*) as success_count'));
        }

        if ($hasTimi) {
            $query->addSelect(DB::raw('sum(case when action_records.timi_flow_post = 3 then 1 else 0 end) as timi_flow_3_count'));
        } else {
            $query->addSelect(DB::raw('0 as timi_flow_3_count'));
        }

        if ($hasContrast) {
            $query->addSelect(DB::raw('avg(action_records.contrast_volume) as avg_contrast'));
        } else {
            $query->addSelect(DB::raw('0 as avg_contrast'));
        }

        if ($hasFluro) {
            $query->addSelect(DB::raw('avg(action_records.fluro_time) as avg_fluro')) ;
        } else {
            $query->addSelect(DB::raw('0 as avg_fluro'));
        }

        $doctorPerformance = $query->groupBy('doctors.id', 'doctors.name')->get();

        return view('reports.clinical', compact('metrics', 'doctorPerformance'));
    }

    /**
     * Laporan Operasional & Efisiensi Waktu (Door-to-Balloon).
     */
    public function operational()
    {
        $formattedDoorToBalloon = '0 Menit';
        $logs = [];
        $stemiCount = 0;
        $successCount = 0;
        $totalProcedures = 0;
        $trendsData = collect();

        if (Schema::hasTable('patients') && Schema::hasTable('action_records') &&
            Schema::hasColumn('action_records', 'd2b_igd_time') && 
            Schema::hasColumn('action_records', 'd2b_balloon_dilatation_time')) {
            
            $hasSuccess = Schema::hasColumn('action_records', 'is_successful');
            $hasComplication = Schema::hasColumn('action_records', 'complication_notes');

            $query = DB::table('action_records')
                ->join('patients', 'action_records.patient_id', '=', 'patients.id')
                ->where('action_records.is_cito', 1)
                ->whereNotNull('action_records.d2b_igd_time')
                ->whereNotNull('action_records.d2b_balloon_dilatation_time');

            $selectFields = [
                'action_records.id as action_record_id',
                'patients.id as patient_id',
                'patients.name as patient_name',
                'patients.medical_record_number',
                'action_records.d2b_igd_time as arrived_hospital_at',
                'action_records.d2b_balloon_dilatation_time as balloon_inflation_at',
                DB::raw('TIMESTAMPDIFF(MINUTE, action_records.d2b_igd_time, action_records.d2b_balloon_dilatation_time) as duration_minutes')
            ];

            if ($hasSuccess) {
                $selectFields[] = 'action_records.is_successful';
            }
            if ($hasComplication) {
                $selectFields[] = 'action_records.complication_notes';
            }

            $query->select($selectFields);

            $logs = $query->latest('action_records.d2b_igd_time')->get();
            $stemiCount = $logs->count();

            if ($stemiCount > 0) {
                $avgMinutes = $logs->avg('duration_minutes');
                $formattedDoorToBalloon = round($avgMinutes) . ' Menit';
            }

            $totalProcedures = DB::table('action_records')->count();
            if ($hasSuccess) {
                $successCount = DB::table('action_records')->where('is_successful', 1)->count();
            } else {
                $successCount = $totalProcedures;
            }

            $trendsData = DB::table('action_records')
                ->where('is_cito', 1)
                ->whereNotNull('d2b_igd_time')
                ->whereNotNull('d2b_balloon_dilatation_time')
                ->select(
                    DB::raw("DATE_FORMAT(d2b_igd_time, '%M %Y') as month_label"),
                    DB::raw("AVG(TIMESTAMPDIFF(MINUTE, d2b_igd_time, d2b_balloon_dilatation_time)) as avg_duration")
                )
                ->groupBy('month_label')
                ->orderByRaw("MIN(d2b_igd_time) ASC")
                ->limit(6)
                ->get();
        }

        $successRate = $totalProcedures > 0 ? round(($successCount / $totalProcedures) * 100, 1) : 100;

        $operationalData = [
            'avg_door_to_balloon' => $formattedDoorToBalloon,
            'stemi_cases_count' => $stemiCount,
            'success_rate' => $successRate . '%',
            'room_utilization' => '78.5%',
            'avg_waiting_days' => '3 Hari',
            'logs' => $logs,
            'trends_data' => $trendsData
        ];

        return view('reports.operational', compact('operationalData'));
    }

    /**
     * Export Laporan Operasional.
     */
    public function exportOperational()
    {
        $logs = [];

        if (Schema::hasTable('patients') && Schema::hasTable('action_records') &&
            Schema::hasColumn('action_records', 'd2b_igd_time') && 
            Schema::hasColumn('action_records', 'd2b_balloon_dilatation_time')) {
            
            $hasSuccess = Schema::hasColumn('action_records', 'is_successful');
            $hasComplication = Schema::hasColumn('action_records', 'complication_notes');

            $query = DB::table('action_records')
                ->join('patients', 'action_records.patient_id', '=', 'patients.id')
                ->where('action_records.is_cito', 1)
                ->whereNotNull('action_records.d2b_igd_time')
                ->whereNotNull('action_records.d2b_balloon_dilatation_time');

            $selectFields = [
                'patients.name as patient_name',
                'patients.medical_record_number',
                'action_records.d2b_igd_time as arrived_hospital_at',
                'action_records.d2b_balloon_dilatation_time as balloon_inflation_at',
                DB::raw('TIMESTAMPDIFF(MINUTE, action_records.d2b_igd_time, action_records.d2b_balloon_dilatation_time) as duration_minutes')
            ];

            if ($hasSuccess) {
                $selectFields[] = 'action_records.is_successful';
            }
            if ($hasComplication) {
                $selectFields[] = 'action_records.complication_notes';
            }

            $logs = $query->select($selectFields)->latest('action_records.created_at')->get();
        }

        return view('reports.operational-print', compact('logs'));
    }

   /**
     * Laporan Rekapitulasi Komprehensif Cathlab.
     */
    public function recapitulation(Request $request)
    {
        $filterType = $request->get('filter_type', 'all'); // 'all', 'daily', 'monthly', 'yearly'
        $month = $request->get('month', date('m'));
        $year = $request->get('year', date('Y'));
        $selectedDate = $request->get('date', date('Y-m-d'));

        $actionQuery = ActionRecord::query();

        // Deteksi kolom tanggal tindakan medis secara fleksibel
        $actionDateCol = Schema::hasColumn('action_records', 'action_date') ? 'action_records.action_date' : 
                     (Schema::hasColumn('action_records', 'date') ? 'action_records.date' : 'action_records.created_at');

        // Ambil daftar tanggal unik berdasarkan bulan & tahun yang dipilih untuk filter harian
        $availableDatesQuery = ActionRecord::selectRaw("DATE({$actionDateCol}) as date");
        if ($year) {
            $availableDatesQuery->whereYear($actionDateCol, $year);
        }
        if ($month) {
            $availableDatesQuery->whereMonth($actionDateCol, $month);
        }
        $availableDates = $availableDatesQuery->distinct()->orderBy('date', 'desc')->pluck('date');

        if ($availableDates->isEmpty()) {
            $availableDates = collect([date('Y-m-d')]);
        }

        // Jika tanggal yang dipilih tidak ada dalam daftar bulan/tahun tersebut, fallback ke tanggal pertama
        if (!in_array($selectedDate, $availableDates->toArray())) {
            $selectedDate = $availableDates->first();
        }

        // Terapkan filter utama pada actionQuery
        if ($filterType === 'daily') {
            $actionQuery->whereDate($actionDateCol, $selectedDate);
        } elseif ($filterType === 'monthly') {
            $actionQuery->whereYear($actionDateCol, $year)->whereMonth($actionDateCol, $month);
        } elseif ($filterType === 'yearly') {
            $actionQuery->whereYear($actionDateCol, $year);
        }

        // Ambil daftar ID pasien yang memiliki tindakan pada periode waktu tersebut
        $filteredPatientIds = (clone $actionQuery)->select('patient_id')->distinct();

        $patientQuery = Patient::whereIn('patients.id', $filteredPatientIds);

        $totalPatients = $patientQuery->count();
        $totalActions = $actionQuery->count();

        // Rekap Jaminan (Asuransi)
        $recapByInsurance = (clone $patientQuery)
            ->leftJoin('insurances', 'patients.insurance_id', '=', 'insurances.id')
            ->select(
                DB::raw('COALESCE(insurances.name, "Mandiri / Umum") as insurance_name'), 
                DB::raw('count(patients.id) as total')
            )
            ->groupBy('insurance_name')
            ->get();

        // Rekap Kategori Divisi
        $recapByCategory = (clone $actionQuery)
            ->join('actions', 'action_records.action_id', '=', 'actions.id')
            ->join('action_categories', 'actions.action_category_id', '=', 'action_categories.id')
            ->select('action_categories.name', DB::raw('count(action_records.id) as total'))
            ->groupBy('action_categories.name')
            ->get();

        // Rekap Jenis Tindakan
        $recapByAction = (clone $actionQuery)
            ->join('actions', 'action_records.action_id', '=', 'actions.id')
            ->select('actions.name as action_name', DB::raw('count(action_records.id) as total'))
            ->groupBy('actions.name')
            ->get();

        // Rekap Harian (Per Tanggal yang ada pasien/tindakan sesuai filter bulan/tahun/all)
        $dailySummaryQuery = ActionRecord::query();
        if ($filterType === 'monthly') {
            $dailySummaryQuery->whereYear($actionDateCol, $year)->whereMonth($actionDateCol, $month);
        } elseif ($filterType === 'yearly') {
            $dailySummaryQuery->whereYear($actionDateCol, $year);
        } elseif ($filterType === 'daily') {
            $dailySummaryQuery->whereDate($actionDateCol, $selectedDate);
        }

        $recapByDate = $dailySummaryQuery
            ->select(
                DB::raw("DATE({$actionDateCol}) as action_date"),
                DB::raw('count(DISTINCT patient_id) as total_patients'),
                DB::raw('count(id) as total_actions')
            )
            ->groupBy('action_date')
            ->orderBy('action_date', 'DESC')
            ->get();

        // Ambil semua tahun yang terdaftar secara dinamis
        $patientYears = Patient::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year');
        $actionYears = ActionRecord::selectRaw("YEAR({$actionDateCol}) as year")->distinct()->pluck('year');
        
        $availableYears = $patientYears->merge($actionYears)->filter()->unique()->sortDesc()->values();
        
        if ($availableYears->isEmpty()) {
            $availableYears = collect([date('Y')]);
        }

        return view('reports.recapitulation', compact(
            'totalPatients', 'totalActions', 'recapByInsurance', 'recapByCategory', 'recapByAction', 'recapByDate',
            'filterType', 'month', 'year', 'selectedDate', 'availableYears', 'availableDates'
        ));
    }
}
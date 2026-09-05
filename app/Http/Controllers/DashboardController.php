<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\ActionCategory;
use App\Models\ActionRecord;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $totalPatients = Patient::count();
        $pendingPatients = Patient::where('status', 'pending')->count();
        $readyPatients = Patient::where('status', 'bersedia')->count();
        $completedPatients = Patient::where('status', 'pernah_tindakan')->count();
        $rejectedPatients = Patient::where('status', 'menolak')->count();
        $totalActions = ActionRecord::count();
        
        // Deteksi kolom tanggal tindakan medis secara fleksibel
        $actionDateCol = Schema::hasColumn('action_records', 'action_date') ? 'action_date' : 
                         (Schema::hasColumn('action_records', 'date') ? 'date' : 'created_at');

        // Pasien unik bulan ini berdasarkan tanggal tindakan
        $patientsThisMonth = Patient::whereIn('id', function($query) use ($actionDateCol) {
            $query->select('patient_id')
                  ->from('action_records')
                  ->whereYear($actionDateCol, date('Y'))
                  ->whereMonth($actionDateCol, date('m'));
        })->count();

        // Jumlah total tindakan/prosedur yang dilakukan pada bulan ini
        $actionsThisMonthCount = DB::table('action_records')
            ->whereYear($actionDateCol, date('Y'))
            ->whereMonth($actionDateCol, date('m'))
            ->count();

        // Rincian jumlah tindakan per Kategori / Divisi bulan ini
        $categoriesThisMonth = DB::table('action_records')
            ->join('actions', 'action_records.action_id', '=', 'actions.id')
            ->join('action_categories', 'actions.action_category_id', '=', 'action_categories.id')
            ->whereYear("action_records.{$actionDateCol}", date('Y'))
            ->whereMonth("action_records.{$actionDateCol}", date('m'))
            ->select('action_categories.name as category_name', DB::raw('count(action_records.id) as total'))
            ->groupBy('action_categories.name')
            ->get();

        // Rincian jumlah tindakan per Jenis Prosedur bulan ini
        $actionsBreakdownThisMonth = DB::table('action_records')
            ->join('actions', 'action_records.action_id', '=', 'actions.id')
            ->whereYear("action_records.{$actionDateCol}", date('Y'))
            ->whereMonth("action_records.{$actionDateCol}", date('m'))
            ->select('actions.name as action_name', DB::raw('count(action_records.id) as total'))
            ->groupBy('actions.name')
            ->get();

        $recentPatients = Patient::latest()->take(5)->get();

        // Mengambil data kategori divisi untuk grafik
        $categories = ActionCategory::all();
        $categoryLabels = $categories->pluck('name');
        $categoryData = $categories->map(function($cat) {
            if (Schema::hasColumn('action_records', 'action_category_id')) {
                return ActionRecord::where('action_category_id', $cat->id)->count();
            } elseif (Schema::hasColumn('action_records', 'category_id')) {
                return ActionRecord::where('category_id', $cat->id)->count();
            }
            return 0;
        });

        // Rekapitulasi Jaminan, Divisi, dan Tindakan Keseluruhan
        $recapByInsurance = Patient::leftJoin('insurances', 'patients.insurance_id', '=', 'insurances.id')
            ->select(DB::raw('COALESCE(insurances.name, "Mandiri / Umum") as insurance_name'), DB::raw('count(patients.id) as total'))
            ->groupBy('insurance_name')->get();

        $recapByCategory = DB::table('action_records')
            ->join('actions', 'action_records.action_id', '=', 'actions.id')
            ->join('action_categories', 'actions.action_category_id', '=', 'action_categories.id')
            ->select('action_categories.name', DB::raw('count(action_records.id) as total'))
            ->groupBy('action_categories.name')->get();

        $recapByAction = DB::table('action_records')
            ->join('actions', 'action_records.action_id', '=', 'actions.id')
            ->select('actions.name as action_name', DB::raw('count(action_records.id) as total'))
            ->groupBy('actions.name')->get();

        // Data Geografis (District / Kecamatan Banyuwangi)
        $rawDistricts = Patient::select('district', DB::raw('count(*) as total'))
            ->whereNotNull('district')->groupBy('district')->get();

        $regionsList = [
            "Banyuwangi", "Kalipuro", "Wongsorejo", "Giri", "Glagah", 
            "Licin", "Kabat", "Rogojampi", "Blimbingsari", "Srono", 
            "Muncar", "Cluring", "Tegaldlimo", "Purwoharjo", "Bangorejo", 
            "Siliragung", "Pesanggaran", "Tegalsari", "Gambiran", "Genteng", 
            "Sempu", "Songgon", "Singojuruh", "Glenmore", "Kalibaru"
        ];

        $patientsByDistrict = array_fill_keys($regionsList, 0);
        foreach ($rawDistricts as $item) {
            $dbName = trim($item->district);
            $cleanDb = strtolower(preg_replace('/[^a-z0-9]/', '', str_replace(['kecamatan', 'kabupaten', 'kec', 'kab'], '', $dbName)));
            foreach ($regionsList as $reg) {
                if (str_contains($cleanDb, strtolower(preg_replace('/[^a-z0-9]/', '', $reg)))) {
                    $patientsByDistrict[$reg] += $item->total;
                }
            }
        }

        $patientYears = Patient::selectRaw('YEAR(created_at) as year')->distinct()->pluck('year');
        $actionYears = ActionRecord::selectRaw("YEAR({$actionDateCol}) as year")->distinct()->pluck('year');
        $availableYears = $patientYears->merge($actionYears)->filter()->unique()->sortDesc()->values();

        return view('dashboard', compact(
            'totalPatients', 'pendingPatients', 'readyPatients', 'completedPatients', 'rejectedPatients', 
            'totalActions', 'patientsThisMonth', 'actionsThisMonthCount', 'categoriesThisMonth', 'actionsBreakdownThisMonth',
            'recentPatients', 'categoryLabels', 'categoryData', 'recapByInsurance', 'recapByCategory', 'recapByAction', 
            'patientsByDistrict', 'availableYears'
        ));
    }
}
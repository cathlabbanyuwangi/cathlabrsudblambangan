<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PatientBhp;
use Carbon\Carbon;

class CheckBhpController extends Controller
{
    public function index(Request $request)
    {
        $patientSearch = $request->input('patient');
        $itemSearch = $request->input('item');
        $month = $request->input('month', 'all');
        $year = $request->input('year', 'all');

        // Filter Utama: BHP >= 200rb
        $query = PatientBhp::with('patient')->where('unit_price', '>=', 200000);

        if ($month != 'all') {
            $query->whereMonth('created_at', $month);
        }
        if ($year != 'all') {
            $query->whereYear('created_at', $year);
        }

        if ($patientSearch) {
            $query->whereHas('patient', function ($q) use ($patientSearch) {
                $q->where('name', 'LIKE', "%{$patientSearch}%")
                  ->orWhere('medical_record_number', 'LIKE', "%{$patientSearch}%");
            });
        }

        if ($itemSearch) {
            $query->where('item_name', 'LIKE', "%{$itemSearch}%");
        }

        // ==========================================
        // DATA GRAFIK (Top 7 Item, Total Biaya & Qty)
        // ==========================================
        $chartQuery = clone $query;
        $topItems = $chartQuery->selectRaw('
                item_name, 
                SUM(quantity * unit_price) as total_biaya, 
                SUM(quantity) as jumlah_pemakaian
            ')
            ->groupBy('item_name')
            ->orderByDesc('total_biaya')
            ->take(7)
            ->get();

        $chartLabels = $topItems->pluck('item_name');
        $chartData = $topItems->pluck('total_biaya');
        $chartQty = $topItems->pluck('jumlah_pemakaian');

        // Data Tabel Paging
        $bhps = $query->latest('created_at')->paginate(20)->withQueryString(); 
        $yearsList = range(Carbon::now()->year, 2023);

        return view('reports.check-bhp.index', compact(
            'bhps', 'patientSearch', 'itemSearch', 'month', 'year', 
            'chartLabels', 'chartData', 'chartQty', 'yearsList'
        ));
    }
}
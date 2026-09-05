<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-1">
            <div>
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <span class="px-3.5 py-1 bg-indigo-50 text-indigo-700 font-black text-[10px] rounded-full uppercase tracking-widest border border-indigo-100 shadow-sm">Modul Analitik</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400 tracking-wide uppercase">Manajemen Mutu & Akreditasi</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Efisiensi Operasional & Flow Cathlab
                </h2>
            </div>
            <!-- Tombol Export / Cetak Laporan -->
            <div class="flex items-center gap-3">
                <a href="{{ route('reports.operational.export') }}" target="_blank" class="inline-flex items-center px-5 py-3 bg-slate-900 hover:bg-slate-800 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-slate-900/20 transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak / Export PDF
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- Metrik Krusial Cathlab -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Door-to-Balloon Time -->
                <div class="bg-slate-900 text-white p-7 rounded-3xl shadow-xl flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Door-to-Balloon (Avg)</p>
                            <span class="px-2.5 py-1 bg-indigo-500/20 text-indigo-300 text-[10px] font-black rounded-full uppercase">STEMI</span>
                        </div>
                        <h3 class="text-4xl font-black text-indigo-400 mt-3">{{ $operationalData['avg_door_to_balloon'] ?? '0 Menit' }}</h3>
                    </div>
                    <p class="text-xs text-slate-400 mt-4 leading-relaxed border-t border-slate-800 pt-3">
                        Target Kardiologi: &lt; 90 Menit untuk kasus infark miokard akut.
                    </p>
                </div>

                <!-- Total Kasus STEMI Tercatat -->
                <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kasus CITO / STEMI</p>
                            <span class="px-2.5 py-1 bg-rose-50 text-rose-700 text-[10px] font-black rounded-full uppercase">Darurat</span>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mt-3">{{ $operationalData['stemi_cases_count'] ?? 0 }} Pasien</h3>
                    </div>
                    <span class="text-xs font-bold text-rose-600 mt-4 border-t border-slate-50 pt-3 flex items-center">
                        Total sampel audit kepatuhan waktu
                    </span>
                </div>

                <!-- Room Utilization Rate -->
                <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Room Utilization</p>
                            <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-full uppercase">High ROI</span>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mt-3">{{ $operationalData['room_utilization'] ?? '78.5%' }}</h3>
                    </div>
                    <span class="text-xs font-bold text-emerald-600 mt-4 border-t border-slate-50 pt-3 flex items-center">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 mr-2 animate-pulse"></span>
                        Kapasitas operasional optimal
                    </span>
                </div>

                <!-- Waiting List SLA -->
                <div class="bg-white p-7 rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between">
                            <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Waiting List SLA</p>
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-black rounded-full uppercase">Elektif</span>
                        </div>
                        <h3 class="text-3xl font-black text-slate-900 mt-3">{{ $operationalData['avg_waiting_days'] ?? '3 Hari' }}</h3>
                    </div>
                    <span class="text-xs font-medium text-slate-500 mt-4 border-t border-slate-50 pt-3">
                        Rata-rata waktu tunggu terprogram
                    </span>
                </div>
            </div>

            <!-- Tabel Audit Log Pasien CITO / Door-to-Balloon & Akreditasi -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <div>
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Log Audit Kepatuhan & Outcome Klinis (Standar Akreditasi KARS)</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Daftar rincian waktu penanganan dan indikator keberhasilan tindakan darurat.</p>
                    </div>
                    <span class="px-3 py-1 bg-indigo-50 text-indigo-600 text-xs font-bold rounded-full">Real-time Data</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs font-black text-slate-400 uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">No. RM & Nama Pasien</th>
                                <th class="px-6 py-4">Waktu Tiba (Door)</th>
                                <th class="px-6 py-4">Waktu Tindakan (Balloon)</th>
                                <th class="px-6 py-4">Durasi Total</th>
                                <th class="px-6 py-4">Kepatuhan (&lt; 90m)</th>
                                <th class="px-6 py-4">Outcome Klinis</th>
                                <th class="px-6 py-4">Komplikasi</th>
                                <th class="px-6 py-4 text-center">Aksi Print</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($operationalData['logs'] ?? [] as $log)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4">
                                    <span class="font-bold text-slate-900 block">{{ $log->patient_name }}</span>
                                    <span class="text-xs text-slate-400">RM: {{ $log->medical_record_number }}</span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-slate-700">{{ $log->arrived_hospital_at ?? '—' }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-700">{{ $log->balloon_inflation_at ?? '—' }}</td>
                                <td class="px-6 py-4 font-black text-indigo-600">{{ isset($log->duration_minutes) ? $log->duration_minutes . ' Menit' : '—' }}</td>
                                <td class="px-6 py-4">
                                    @if(isset($log->duration_minutes))
                                        @if($log->duration_minutes <= 90)
                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-[10px] font-black rounded-full">Sesuai (&lt; 90m)</span>
                                        @else
                                            <span class="px-2.5 py-1 bg-rose-100 text-rose-700 text-[10px] font-black rounded-full">Terlambat (&gt; 90m)</span>
                                        @endif
                                    @else
                                        <span class="px-2.5 py-1 bg-slate-100 text-slate-500 text-[10px] font-black rounded-full">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if(isset($log->is_successful) && $log->is_successful)
                                        <span class="font-bold text-emerald-600">Berhasil (TIMI 3)</span>
                                    @else
                                        <span class="font-bold text-rose-600">Kompleks / Evaluasi</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-xs text-slate-500">
                                    {{ $log->complication_notes ?? 'Tidak ada komplikasi' }}
                                </td>
                                <!-- Kolom Print Per Pasien -->
                                <td class="px-6 py-4 text-center">
                                    @if(isset($log->action_record_id))
                                        <a href="{{ route('patients.actions.door-to-balloon.print', [$log->patient_id, $log->action_record_id]) }}" target="_blank" class="inline-flex items-center px-3.5 py-2 bg-slate-900 hover:bg-slate-800 text-white font-black text-[10px] uppercase tracking-wider rounded-xl shadow-sm transition-all" title="Cetak Lembar D2B Pasien">
                                            <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                            Print
                                        </a>
                                    @else
                                        <span class="text-xs text-slate-400">—</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="px-6 py-10 text-center text-slate-400 italic">
                                    Belum ada catatan kasus CITO/STEMI yang memiliki data Door & Balloon time lengkap di database.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- GRAFIK TREN BULANAN DOOR-TO-BALLOON (UNTUK AKREDITASI KARS) -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/50 p-6 sm:p-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-6 mb-6 border-b border-slate-100 gap-4">
                    <div>
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Tren Performa Door-to-Balloon (6 Bulan Terakhir)</h4>
                        <p class="text-xs text-slate-500 mt-0.5">Analisis evaluasi berkesinambungan (Continuous Quality Improvement) kecepatan respons darurat.</p>
                    </div>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-xs font-black rounded-full uppercase tracking-wider self-start sm:self-auto">Indikator Mutu INM</span>
                </div>
                
                <div class="h-80 w-full">
                    <canvas id="d2bTrendChart"></canvas>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('d2bTrendChart').getContext('2d');
            const chartData = @json($operationalData['trends_data']);
            
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chartData.map(item => item.month_label),
                    datasets: [{
                        label: 'Rata-rata Waktu (Menit)',
                        data: chartData.map(item => Math.round(item.avg_duration)),
                        borderColor: '#4f46e5', // Indigo-600
                        backgroundColor: 'rgba(79, 70, 229, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 6,
                        pointBackgroundColor: '#4f46e5',
                        pointBorderWidth: 3,
                        pointBorderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return ' Rata-rata: ' + context.parsed.y + ' Menit';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { borderDash: [4, 4], color: '#f1f5f9' },
                            title: { display: true, text: 'Durasi (Menit)', font: { weight: 'bold', size: 10 } }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { font: { weight: 'bold', size: 11 } }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
<x-app-layout>
    <!-- Header Khusus Laporan Performa -->
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 py-5">
            <div>
                <div class="flex items-center space-x-3 mb-2.5">
                    <span class="inline-flex items-center px-3.5 py-1.5 bg-sky-50 text-sky-700 font-black text-[10px] rounded-xl uppercase tracking-widest border border-sky-200/80 shadow-2xs">
                        Audit Mutu KARS & JCI
                    </span>
                    <span class="text-sky-300 font-bold">•</span>
                    <span class="text-xs font-extrabold text-slate-400 tracking-wider uppercase">Cathlab RSUD Blambangan</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Laporan Performa Klinis & Mutu Medis
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Audit efektivitas klinis, TIMI Flow, volume kontras, fluoroscopy time, dan outcome tindakan.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('patients.index') }}" class="inline-flex items-center px-5 py-3.5 bg-white border border-sky-200/80 text-sky-700 hover:bg-sky-50/60 font-black text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all">
                    &larr; Kembali ke Daftar Pasien
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- KPI KLINIS & MUTU UTAMA (4 KARTU PROPORSI SEIMBANG) -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                <!-- KPI 1: TIMI Flow 3 -->
                <div class="bg-white/90 backdrop-blur-xl p-6 rounded-[28px] border border-sky-100/85 shadow-xl shadow-sky-950/5 relative overflow-hidden transition-all hover:border-sky-200">
                    <p class="text-[10px] font-black text-sky-600 uppercase tracking-widest">TIMI Flow 3 (Optimal)</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-2">
                        {{ isset($metrics->timi_flow_3_count) && ($metrics->total_procedures ?? 0) > 0 ? round(($metrics->timi_flow_3_count / $metrics->total_procedures) * 100, 1) : '0' }}%
                    </h3>
                    <span class="text-xs text-slate-500 mt-1 block">Target Akreditasi > 95%</span>
                </div>

                <!-- KPI 2: Success Rate -->
                <div class="bg-white/90 backdrop-blur-xl p-6 rounded-[28px] border border-sky-100/85 shadow-xl shadow-sky-950/5 relative overflow-hidden transition-all hover:border-sky-200">
                    <p class="text-[10px] font-black text-sky-600 uppercase tracking-widest">Success Rate</p>
                    <h3 class="text-3xl font-black text-emerald-600 mt-2">
                        @php
                            $successCount = $metrics->success_count ?? 0;
                            $totalProc = $metrics->total_procedures ?? 0;
                            $successRate = $totalProc > 0 ? round(($successCount / $totalProc) * 100, 1) : 0;
                        @endphp
                        {{ $successRate }}%
                    </h3>
                    <span class="text-xs text-slate-500 mt-1 block">Status tindakan sukses/lancar</span>
                </div>

                <!-- KPI 3: Rata-rata Kontras -->
                <div class="bg-white/90 backdrop-blur-xl p-6 rounded-[28px] border border-sky-100/85 shadow-xl shadow-sky-950/5 relative overflow-hidden transition-all hover:border-sky-200">
                    <p class="text-[10px] font-black text-sky-600 uppercase tracking-widest">Rata-rata Kontras</p>
                    <h3 class="text-3xl font-black text-sky-600 mt-2">
                        {{ isset($metrics->avg_contrast) ? round($metrics->avg_contrast, 1) : '0' }} <span class="text-sm font-bold text-slate-500">ml</span>
                    </h3>
                    <span class="text-xs text-slate-500 mt-1 block">Pencegahan Risiko CIN (Gagal Ginjal)</span>
                </div>

                <!-- KPI 4: Avg Fluoroscopy Time -->
                <div class="bg-white/90 backdrop-blur-xl p-6 rounded-[28px] border border-sky-100/85 shadow-xl shadow-sky-950/5 relative overflow-hidden transition-all hover:border-sky-200">
                    <p class="text-[10px] font-black text-sky-600 uppercase tracking-widest">Avg Fluoroscopy Time</p>
                    <h3 class="text-3xl font-black text-indigo-600 mt-2">
                        {{ isset($metrics->avg_fluro_time) ? round($metrics->avg_fluro_time, 1) : '0' }} <span class="text-sm font-bold text-slate-500">Menit</span>
                    </h3>
                    <span class="text-xs text-slate-500 mt-1 block">Durasi penyinaran sinar-X optimal</span>
                </div>
            </div>

            <!-- TABEL PERFORMA DOKTER & INDIKATOR KLINIS DETAIL -->
            <div class="bg-white/90 backdrop-blur-xl rounded-[32px] border border-sky-100/80 shadow-xl shadow-sky-950/5 overflow-hidden">
                <div class="p-6 sm:p-8 border-b border-sky-100/60 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-gradient-to-r from-sky-50/40 to-transparent">
                    <div>
                        <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Evaluasi Outcome & Indikator Klinis per Dokter Spesialis</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Rekapitulasi TIMI Flow, Volume Kontras, Durasi Fluoroskopi, dan Status Keberhasilan.</p>
                    </div>
                    <span class="text-xs font-black text-sky-700 bg-sky-50 px-4 py-2 rounded-2xl border border-sky-200/60 shadow-2xs">
                        🛡️ Audit Ready KARS
                    </span>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-sky-50/60 text-[11px] font-black text-sky-800 uppercase tracking-wider border-b border-sky-100/60">
                            <tr>
                                <th class="px-6 py-4.5">Nama Dokter Spesialis</th>
                                <th class="px-6 py-4.5 text-center">Total Kasus</th>
                                <th class="px-6 py-4.5 text-center">TIMI Flow 3</th>
                                <th class="px-6 py-4.5 text-center">Rata2 Kontras (ml)</th>
                                <th class="px-6 py-4.5 text-center">Fluoroscopy (Min)</th>
                                <th class="px-6 py-4.5 text-center">Keberhasilan (%)</th>
                                <th class="px-6 py-4.5">Status Mutu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-50 font-medium">
                            @forelse($doctorPerformance as $doc)
                            @php
                                $docTotal = $doc->total_procedures ?? 0;
                                $docSuccess = $doc->success_count ?? 0;
                                $docSuccessRate = $docTotal > 0 ? round(($docSuccess / $docTotal) * 100, 1) : 0;
                            @endphp
                            <tr class="hover:bg-sky-50/40 transition-colors">
                                <td class="px-6 py-4.5 font-black text-slate-900 flex items-center space-x-2">
                                    <span class="w-2 h-2 rounded-full bg-sky-500"></span>
                                    <span>Dr. {{ $doc->name }}</span>
                                </td>
                                <td class="px-6 py-4.5 text-center font-extrabold text-slate-700">{{ $docTotal }}</td>
                                <td class="px-6 py-4.5 text-center font-bold text-sky-700">{{ $doc->timi_flow_3_count ?? '-' }}</td>
                                <td class="px-6 py-4.5 text-center text-slate-600">{{ isset($doc->avg_contrast) ? round($doc->avg_contrast, 1) : '-' }} ml</td>
                                <td class="px-6 py-4.5 text-center text-slate-600">{{ isset($doc->avg_fluro) ? round($doc->avg_fluro, 1) : '-' }} m</td>
                                <td class="px-6 py-4.5 text-center">
                                    <span class="text-emerald-600 font-black">{{ $docSuccessRate }}%</span>
                                </td>
                                <td class="px-6 py-4.5">
                                    <span class="inline-flex items-center px-3 py-1 bg-sky-100 text-sky-800 text-[10px] font-black rounded-full border border-sky-200">
                                        Verified
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center text-slate-400 italic bg-slate-50/30">
                                    Belum ada data catatan tindakan klinis yang terhubung dengan operator dokter.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
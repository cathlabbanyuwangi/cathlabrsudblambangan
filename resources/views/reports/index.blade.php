<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-slate-900 tracking-tight">Laporan Kinerja Klinis</h2>
                <p class="text-sm text-slate-500">Rekapitulasi operasional rumah sakit bulan {{ \Carbon\Carbon::now()->translatedFormat('F Y') }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" class="px-4 py-2 bg-white border border-slate-200 rounded-xl text-xs font-bold text-slate-600 hover:bg-slate-50 shadow-sm transition">
                    Print / Export PDF
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- KPI Strip -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @foreach(['Total Pasien' => 'total', 'Selesai Tindakan' => 'completed', 'Antrean Pending' => 'pending', 'Prioritas Urgent' => 'priority'] as $label => $key)
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">{{ $label }}</p>
                    <h3 class="text-3xl font-black text-slate-900 mt-2">{{ $stats[$key] }}</h3>
                </div>
                @endforeach
            </div>

            <!-- Content Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Kiri: Kinerja Departemen -->
                <div class="lg:col-span-2 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                    <h4 class="text-sm font-black text-slate-900 mb-6 uppercase tracking-wider">Aktivitas Departemen</h4>
                    <div class="space-y-6">
                        @foreach($departmentPerformance as $dept)
                        <div>
                            <div class="flex justify-between text-xs font-bold text-slate-600 mb-2">
                                <span>{{ $dept->name }}</span>
                                <span>{{ $dept->records_count }} Tindakan</span>
                            </div>
                            <div class="w-full bg-slate-100 h-2 rounded-full overflow-hidden">
                                <div class="bg-indigo-600 h-full rounded-full" style="width: {{ $stats['total'] > 0 ? ($dept->records_count / $stats['total']) * 100 : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <!-- Kanan: Tren Ringkas -->
                <div class="bg-slate-900 p-8 rounded-3xl text-white shadow-xl">
                    <h4 class="text-sm font-black mb-6 uppercase tracking-wider text-slate-400">Tren Pasien 7 Hari</h4>
                    <div class="flex items-end gap-2 h-32">
                        @foreach($trends as $trend)
                        <div class="flex-1 bg-indigo-500 rounded-t-lg hover:bg-indigo-400 transition" 
                             style="height: {{ ($trend->count / ($stats['total'] + 1)) * 100 }}%">
                        </div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-500 mt-4 text-center italic">Grafik fluktuasi input data pasien per hari</p>
                </div>
            </div>

            <!-- Tabel Data Audit -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-slate-100">
                    <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Riwayat Pasien Terbaru</h4>
                </div>
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs font-black text-slate-400 uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Jaminan</th>
                            <th class="px-6 py-4">Tanggal Masuk</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($patients as $patient)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4 font-semibold text-slate-900">{{ $patient->name }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded-full text-[10px] font-bold 
                                    {{ $patient->status == 'pernah_tindakan' ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">
                                    {{ str_replace('_', ' ', $patient->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-xs">{{ $patient->insurance->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-xs">{{ $patient->created_at->format('d M Y') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="p-4 border-t border-slate-100">
                    {{ $patients->links() }}
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded-full uppercase tracking-widest border border-indigo-100 shadow-xs">Modul Rekam Medis</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400 tracking-wider uppercase">Log Aktivitas</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Riwayat Pemanggilan: <span class="text-indigo-600 font-extrabold">{{ $patient->name }}</span>
                </h2>
            </div>
            <div>
                <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex items-center px-5 py-3.5 bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all duration-300">
                    &larr; Detail Pasien
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- SECTION: TIMELINE / DAFTAR KE BAWAH LOG PEMANGGILAN -->
            <div class="bg-white rounded-[32px] border border-slate-100 shadow-xl shadow-slate-100/60 p-8 sm:p-10 space-y-6" x-data="{ open: true }">
                
                <div class="flex items-center justify-between border-b border-slate-100 pb-5">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-black shadow-inner">
                            📞
                        </div>
                        <div>
                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-widest">Daftar Log Pemanggilan Pasien</h3>
                            <p class="text-[11px] font-bold text-slate-400 mt-0.5">Histori status, konfirmasi kesediaan, dan penjadwalan tindakan</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        @php
                            $statusBadge = match($patient->status ?? 'pending') {
                                'bersedia' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-100', 'label' => 'Bersedia / Antre Tindakan'],
                                'menolak' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-100', 'label' => 'Menolak Tindakan'],
                                'pernah_tindakan' => ['bg' => 'bg-blue-50 text-blue-700 border-blue-100', 'label' => 'Sudah / Pernah Tindakan'],
                                default => ['bg' => 'bg-amber-50 text-amber-700 border-amber-100', 'label' => 'Menunggu Panggilan (Pending)']
                            };
                        @endphp
                        <span class="px-4 py-1.5 rounded-full text-xs font-black uppercase tracking-wider border {{ $statusBadge['bg'] }} shadow-2xs hidden sm:inline-block">
                            {{ $statusBadge['label'] }}
                        </span>

                        <!-- Tombol Toggle Collapse -->
                        <button @click="open = !open" class="w-9 h-9 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center transition-all shadow-2xs" title="Sembunyikan / Tampilkan">
                            <svg class="w-4 h-4 transform transition-transform duration-300" :class="{ 'rotate-180': !open }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                    </div>
                </div>

                <!-- List Vertikal / Ke Bawah dengan Collapse -->
                <div x-show="open" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform -translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" class="relative pl-6 sm:pl-8 space-y-8 before:absolute before:left-2.5 sm:before:left-3.5 before:top-3 before:bottom-3 before:w-0.5 before:bg-slate-100 pt-2">
                    
                    <!-- Item Log Utama: Waktu, Petugas & Jadwal Tindakan (Disatukan dalam 1 Blok) -->
                    <div class="relative flex items-start space-x-4">
                        <span class="absolute -left-6 sm:-left-8 top-1.5 w-4 h-4 rounded-full bg-indigo-600 border-4 border-white shadow-md"></span>
                        <div class="bg-slate-50/80 rounded-2xl p-6 border border-slate-100 flex-1 space-y-4">
                            <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest block">Informasi Pemanggilan & Jadwal</span>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Waktu Panggil Terakhir</span>
                                    <span class="text-xs font-black text-slate-900 mt-0.5 block">
                                        {{ $patient->called_at ? \Carbon\Carbon::parse($patient->called_at)->translatedFormat('d F Y • H:i') : 'Belum Pernah Dipanggil' }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Petugas Pemanggil</span>
                                    <span class="text-xs font-black text-slate-900 mt-0.5 block">
                                        {{ $patient->caller->name ?? 'Belum Tercatat' }}
                                    </span>
                                </div>
                            </div>

                            @if($patient->scheduled_at)
                            <div class="border-t border-slate-200/60 pt-3">
                                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Jadwal Tindakan Disetujui</span>
                                <span class="text-xs font-black text-emerald-600 mt-0.5 block">
                                    📅 {{ \Carbon\Carbon::parse($patient->scheduled_at)->translatedFormat('d F Y • H:i') }}
                                </span>
                            </div>
                            @endif
                        </div>
                    </div>

                    <!-- Item Log Cadangan: Alasan Penolakan (Jika Menolak) -->
                    @if($patient->unwillingness_reason)
                    <div class="relative flex items-start space-x-4">
                        <span class="absolute -left-6 sm:-left-8 top-1.5 w-4 h-4 rounded-full bg-rose-500 border-4 border-white shadow-md"></span>
                        <div class="bg-slate-50/80 rounded-2xl p-5 border border-slate-100 flex-1 space-y-1">
                            <span class="text-[10px] font-black text-rose-600 uppercase tracking-widest block">Alasan / Catatan Penolakan</span>
                            <span class="text-xs font-bold text-slate-700 block">
                                {{ $patient->unwillingness_reason }}
                            </span>
                        </div>
                    </div>
                    @endif

                </div>

            </div>

        </div>
    </div>
</x-app-layout>
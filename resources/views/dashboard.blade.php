<x-app-layout>

    @push('styles')
    <style>
        /* Modern Card Styling */
        .card-enterprise {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(226, 232, 240, 0.8);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.03);
            border-radius: 32px;
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-enterprise:hover {
            border-color: rgba(99, 102, 241, 0.3);
            box-shadow: 0 20px 40px rgba(99, 102, 241, 0.08);
            transform: translateY(-4px);
        }

        /* Custom Scrollbar yang lebih elegan */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(248, 250, 252, 0.5);
            border-radius: 8px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 8px;
        }
        .custom-scrollbar:hover::-webkit-scrollbar-thumb {
            background: #94a3b8;
        }

        /* Animasi Masuk (Fade Up) */
        @keyframes fadeUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-up {
            animation: fadeUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }
        .delay-100 { animation-delay: 100ms; }
        .delay-200 { animation-delay: 200ms; }
        .delay-300 { animation-delay: 300ms; }
        
        /* Animasi Background Gradient */
        @keyframes gradientMove {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradientMove 6s ease infinite;
        }
    </style>
    @endpush

    @php
        // Logika Sambutan Hangat Dinamis
        $hour = \Carbon\Carbon::now()->format('H');
        if ($hour < 11) {
            $greeting = 'Selamat Pagi';
            $emoji = '🌅';
            $message = 'Semangat pagi! Siap memberikan pelayanan terbaik hari ini?';
        } elseif ($hour < 15) {
            $greeting = 'Selamat Siang';
            $emoji = '☀️';
            $message = 'Tetap fokus dan jaga kesehatan di tengah kesibukan operasional.';
        } elseif ($hour < 18) {
            $greeting = 'Selamat Sore';
            $emoji = '🌇';
            $message = 'Kerja keras Anda hari ini sangat berarti bagi banyak pasien.';
        } else {
            $greeting = 'Selamat Malam';
            $emoji = '🌙';
            $message = 'Terima kasih atas dedikasi Anda hari ini. Selamat beristirahat.';
        }
    @endphp

    {{-- HEADER --}}
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 py-6 animate-fade-up">
            <div class="space-y-4">
                <div class="flex items-center space-x-2.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 inline-block ring-4 ring-emerald-500/20 animate-pulse"></span>
                    <span class="text-[10px] font-black text-slate-500 uppercase tracking-widest bg-slate-100/80 backdrop-blur-sm px-3 py-1 rounded-full border border-slate-200 shadow-2xs">
                        Cathlab Enterprise System
                    </span>
                </div>
                
                <div>
                    {{-- PERBAIKAN: Box Nama lebih soft, melengkung rapi (rounded-full), dan warna kalem --}}
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight leading-normal">
                        {{ $greeting }}, 
                        <span class="inline-block px-5 py-1.5 mx-1 bg-indigo-50 text-indigo-600 rounded-full border border-indigo-100/80 shadow-sm align-middle transform -translate-y-0.5">
                            {{ explode(' ', auth()->user()->name)[0] }}
                        </span> 
                        {{ $emoji }}
                    </h2>
                    <p class="text-sm text-slate-500 font-medium mt-2">{{ $message }}</p>
                </div>
            </div>
            
            <div class="flex items-center">
                <div class="px-5 py-3.5 bg-white/80 backdrop-blur-md border border-slate-200/80 text-indigo-900 font-extrabold text-xs rounded-2xl shadow-sm flex items-center space-x-3 hover:bg-white transition-colors cursor-default">
                    <span class="text-lg">🗓️</span>
                    <span>{{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </div>
    </x-slot>

    {{-- MAIN CONTENT --}}
    <div class="py-8 bg-slate-50/50 min-h-screen text-slate-800 relative overflow-hidden">
        
        {{-- Dekorasi Latar Belakang Halus --}}
        <div class="absolute top-0 left-0 w-full h-96 bg-gradient-to-b from-indigo-50/40 to-transparent pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8 relative z-10">

            {{-- 1. STATS BAR UTAMA (Animasi berurutan delay-100) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-up delay-100">
                
                {{-- Card Total Pasien --}}
                <div class="card-enterprise p-6 flex flex-col justify-between group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full blur-2xl group-hover:bg-indigo-100 transition-colors"></div>
                    <div class="flex items-start justify-between relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 border border-indigo-100 flex items-center justify-center font-black text-xl group-hover:scale-110 group-hover:rotate-3 transition-transform shadow-sm">👥</div>
                        <span class="text-[10px] font-black text-slate-400 bg-slate-100 px-2 py-1 rounded-lg">ALL TIME</span>
                    </div>
                    <div class="mt-4 relative z-10">
                        <h3 class="text-4xl font-black text-slate-900 tracking-tight group-hover:text-indigo-600 transition-colors">{{ $totalPatients ?? 0 }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Total keseluruhan pasien</p>
                    </div>
                </div>

                {{-- Card Pasien Bulan Ini --}}
                <div class="card-enterprise p-6 flex flex-col justify-between border-amber-100 group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full blur-2xl group-hover:bg-amber-100 transition-colors"></div>
                    <div class="flex items-start justify-between relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-amber-50 text-amber-600 border border-amber-200/80 flex items-center justify-center font-black text-xl group-hover:scale-110 group-hover:-rotate-3 transition-transform shadow-sm">📅</div>
                        <span class="text-[10px] font-black text-amber-600 bg-amber-50 px-2 py-1 rounded-lg">THIS MONTH</span>
                    </div>
                    <div class="mt-4 relative z-10">
                        <h3 class="text-4xl font-black text-slate-900 tracking-tight group-hover:text-amber-600 transition-colors">{{ $patientsThisMonth ?? 0 }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Pasien baru bulan ini</p>
                    </div>
                </div>

                {{-- Card Tindakan Bulan Ini --}}
                <div class="card-enterprise p-6 flex flex-col justify-between border-violet-100 group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-violet-50 rounded-full blur-2xl group-hover:bg-violet-100 transition-colors"></div>
                    <div class="flex items-start justify-between relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-violet-50 text-violet-600 border border-violet-200/80 flex items-center justify-center font-black text-xl group-hover:scale-110 group-hover:rotate-3 transition-transform shadow-sm">💉</div>
                        <span class="text-[10px] font-black text-violet-600 bg-violet-50 px-2 py-1 rounded-lg">PROCEDURES</span>
                    </div>
                    <div class="mt-4 relative z-10">
                        <h3 class="text-4xl font-black text-slate-900 tracking-tight group-hover:text-violet-600 transition-colors">{{ $actionsThisMonthCount ?? 0 }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-1">Tindakan diselesaikan</p>
                    </div>
                </div>

                {{-- Card Antrean Aktif --}}
                <div class="card-enterprise p-6 flex flex-col justify-between border-emerald-100 group overflow-hidden relative">
                    <div class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-50 rounded-full blur-2xl group-hover:bg-emerald-100 transition-colors"></div>
                    <div class="flex items-start justify-between relative z-10">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-600 border border-emerald-200/80 flex items-center justify-center font-black text-xl group-hover:scale-110 group-hover:-rotate-3 transition-transform shadow-sm">⚡</div>
                        <div class="flex gap-1 items-center">
                            <span class="relative flex h-2 w-2"><span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span><span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span></span>
                            <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 px-2 py-1 rounded-lg">ACTIVE QUEUE</span>
                        </div>
                    </div>
                    <div class="mt-4 relative z-10 flex items-baseline space-x-2">
                        <h3 class="text-4xl font-black text-slate-900 tracking-tight group-hover:text-emerald-600 transition-colors">{{ ($pendingPatients ?? 0) + ($readyPatients ?? 0) }}</h3>
                        <span class="text-xs text-slate-500 font-bold">({{ $readyPatients ?? 0 }} siap)</span>
                    </div>
                </div>
            </div>

            {{-- 2. SECTION: REKAPITULASI BULAN INI (Animasi delay-200) --}}
            <div class="card-enterprise p-8 space-y-6 animate-fade-up delay-200">
                <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-100 pb-5">
                    <div>
                        <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 shadow-2xs">Clinical Insights</span>
                        <h3 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Breakdown Aktivitas Klinis Bulan Ini</h3>
                        <p class="text-sm text-slate-500 font-medium mt-1">Ringkasan operasional berdasarkan spesialisasi dan jenis tindakan medis.</p>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="px-4 py-2 bg-indigo-50 text-indigo-700 text-xs font-black rounded-xl border border-indigo-100 shadow-2xs flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>{{ count($categoriesThisMonth ?? []) }} Divisi Aktif</span>
                        <span class="px-4 py-2 bg-emerald-50 text-emerald-700 text-xs font-black rounded-xl border border-emerald-100 shadow-2xs flex items-center gap-2"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ count($actionsBreakdownThisMonth ?? []) }} Prosedur</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Kolom Kiri: Divisi --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="text-lg">🏥</span> Berdasarkan Divisi
                        </h4>
                        <div class="space-y-3 max-h-[240px] overflow-y-auto custom-scrollbar pr-2">
                            @forelse($categoriesThisMonth ?? [] as $cat)
                            <div class="flex items-center justify-between p-4 bg-slate-50/50 hover:bg-indigo-50/30 border border-slate-200/80 hover:border-indigo-200 rounded-2xl transition-all shadow-2xs group cursor-default">
                                <span class="text-sm font-bold text-slate-700 group-hover:text-indigo-900 transition-colors">{{ $cat->category_name }}</span>
                                <span class="text-xs font-black bg-white group-hover:bg-indigo-600 text-slate-700 group-hover:text-white px-3.5 py-1.5 rounded-xl shadow-xs transition-colors border border-slate-200 group-hover:border-transparent">{{ $cat->total }} Kasus</span>
                            </div>
                            @empty
                            <div class="text-center py-10 bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                                <span class="text-2xl block mb-2">📭</span>
                                <p class="text-xs text-slate-400 font-medium">Belum ada data divisi bulan ini.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Kolom Kanan: Jenis Tindakan --}}
                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest flex items-center gap-2">
                            <span class="text-lg">🩺</span> Berdasarkan Tindakan
                        </h4>
                        <div class="space-y-3 max-h-[240px] overflow-y-auto custom-scrollbar pr-2">
                            @forelse($actionsBreakdownThisMonth ?? [] as $act)
                            <div class="flex items-center justify-between p-4 bg-slate-50/50 hover:bg-emerald-50/30 border border-slate-200/80 hover:border-emerald-200 rounded-2xl transition-all shadow-2xs group cursor-default">
                                <span class="text-sm font-bold text-slate-700 group-hover:text-emerald-900 transition-colors">{{ $act->action_name }}</span>
                                <span class="text-xs font-black bg-white group-hover:bg-emerald-600 text-slate-700 group-hover:text-white px-3.5 py-1.5 rounded-xl shadow-xs transition-colors border border-slate-200 group-hover:border-transparent">{{ $act->total }} Kali</span>
                            </div>
                            @empty
                            <div class="text-center py-10 bg-slate-50/50 rounded-3xl border border-dashed border-slate-200">
                                <span class="text-2xl block mb-2">📭</span>
                                <p class="text-xs text-slate-400 font-medium">Belum ada data tindakan bulan ini.</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- 3. REGIONAL MATRIX & QUICK CONTROL (Animasi delay-300) --}}
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 animate-fade-up delay-300">
                
                {{-- PANEL KIRI: DISTRIBUSI WILAYAH (8 Kolom) --}}
                <div class="lg:col-span-8 card-enterprise p-8 flex flex-col justify-between">
                    <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-4 border-b border-slate-100 pb-5">
                        <div>
                            <span class="text-[10px] font-black text-indigo-700 uppercase tracking-widest bg-indigo-50 px-3 py-1.5 rounded-xl border border-indigo-100 shadow-2xs">Regional Intelligence</span>
                            <h3 class="text-2xl font-black text-slate-900 tracking-tight mt-2">Distribusi Pasien Per Kecamatan</h3>
                            <p class="text-sm text-slate-500 font-medium mt-1">Pemetaan sebaran wilayah rujukan dari seluruh data pasien.</p>
                        </div>
                        <div class="px-5 py-2.5 bg-slate-50 border border-slate-200/80 rounded-2xl text-xs font-bold text-slate-700 shadow-2xs flex items-center gap-2">
                            <span class="text-lg">🗺️</span> Total: <span class="text-indigo-600 font-black text-sm">25 Kecamatan</span>
                        </div>
                    </div>

                    @php
                        // PERBAIKAN: "Banyuwangi Kota" diubah menjadi "Banyuwangi"
                        $regionsList = [
                            "Banyuwangi", "Kalipuro", "Wongsorejo", "Giri", "Glagah", 
                            "Licin", "Kabat", "Rogojampi", "Blimbingsari", "Srono", 
                            "Muncar", "Cluring", "Tegaldlimo", "Purwoharjo", "Bangorejo", 
                            "Siliragung", "Pesanggaran", "Tegalsari", "Gambiran", "Genteng", 
                            "Sempu", "Songgon", "Singojuruh", "Glenmore", "Kalibaru"
                        ];

                        $maxVal = 1;
                        foreach($regionsList as $reg) {
                            $val = $patientsByDistrict[$reg] ?? 0;
                            if($val > $maxVal) $maxVal = $val;
                        }
                    @endphp

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 max-h-[420px] overflow-y-auto custom-scrollbar pr-3 mt-6">
                        @foreach($regionsList as $regName)
                            @php
                                $count = $patientsByDistrict[$regName] ?? 0;
                                $percentage = min(100, round(($count / $maxVal) * 100));
                            @endphp
                            <div class="p-4 bg-slate-50/50 hover:bg-white border border-slate-200/80 hover:border-indigo-300 rounded-2xl transition-all group shadow-2xs hover:shadow-md relative overflow-hidden">
                                {{-- Background efek progres halus --}}
                                <div class="absolute inset-y-0 left-0 bg-indigo-50/40 opacity-0 group-hover:opacity-100 transition-opacity" style="width: {{ $percentage }}%"></div>
                                
                                <div class="flex items-center justify-between mb-3 relative z-10">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-xl bg-white text-indigo-600 flex items-center justify-center font-bold text-xs border border-slate-200 shadow-sm group-hover:scale-110 group-hover:rotate-6 transition-transform">📍</div>
                                        <h4 class="text-sm font-bold text-slate-800">{{ $regName }}</h4>
                                    </div>
                                    <div class="text-right flex items-baseline gap-1">
                                        <span class="text-lg font-black text-indigo-600 group-hover:scale-110 transition-transform origin-right">{{ $count }}</span>
                                        <span class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">Pasien</span>
                                    </div>
                                </div>
                                <div class="w-full bg-slate-200/60 h-2 rounded-full overflow-hidden relative z-10">
                                    <div class="bg-gradient-to-r from-indigo-500 to-violet-500 h-full rounded-full transition-all duration-1000 ease-out shadow-2xs relative" style="width: {{ $percentage }}%;">
                                        <div class="absolute inset-0 bg-white/20 w-full h-full animate-pulse"></div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-8 pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-400 font-medium">
                        <span class="flex items-center gap-2"><span class="text-lg">🛰️</span> Database Mapping Active</span>
                        <span class="text-emerald-700 font-black tracking-wide flex items-center gap-2 bg-emerald-50 px-3.5 py-1.5 rounded-xl border border-emerald-200 shadow-2xs">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.8)] animate-pulse"></span> SYSTEM SYNCED
                        </span>
                    </div>
                </div>

                {{-- PANEL KANAN (4 Kolom: Quick Control & Status Lainnya) --}}
                <div class="lg:col-span-4 space-y-6 flex flex-col justify-between">
                    
                    {{-- Pusat Kontrol Cepat (Dirombak lebih modern) --}}
                    <div class="rounded-[32px] p-8 bg-gradient-to-br from-indigo-900 via-slate-900 to-slate-900 text-white shadow-[0_20px_50px_rgba(30,27,75,0.3)] relative overflow-hidden group animate-gradient">
                        {{-- Ornamen Latar Belakang --}}
                        <div class="absolute -right-10 -top-10 w-48 h-48 bg-indigo-500/20 rounded-full blur-3xl group-hover:bg-indigo-500/30 transition-all duration-500"></div>
                        <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-violet-500/20 rounded-full blur-3xl group-hover:bg-violet-500/30 transition-all duration-500"></div>
                        
                        <div class="relative z-10 space-y-5">
                            <div class="flex items-center gap-3">
                                <span class="p-2 bg-white/10 rounded-xl backdrop-blur-sm border border-white/10">🚀</span>
                                <span class="px-3 py-1 bg-indigo-500/20 text-indigo-300 font-black text-[10px] rounded-full uppercase tracking-widest border border-indigo-500/20 shadow-inner">Quick Workflow</span>
                            </div>
                            
                            <div>
                                <h4 class="text-2xl font-black tracking-tight text-white mb-1">Pusat Kontrol</h4>
                                <p class="text-sm text-slate-400 font-medium leading-relaxed">Jalan pintas operasional harian manajemen kateterisasi.</p>
                            </div>

                            <div class="pt-3 space-y-3">
                                <a href="{{ route('patients.create') }}" class="w-full py-4 bg-white text-indigo-900 font-black text-xs uppercase tracking-wider rounded-2xl flex items-center justify-center gap-2 shadow-[0_10px_20px_rgba(0,0,0,0.2)] hover:shadow-[0_15px_30px_rgba(255,255,255,0.15)] transition-all transform hover:-translate-y-1">
                                    <span class="text-lg">✍️</span> Daftar Pasien Baru
                                </a>
                                <a href="{{ route('patients.action-queue') }}" class="w-full py-4 bg-white/10 hover:bg-white/20 text-white font-black text-xs uppercase tracking-wider rounded-2xl flex items-center justify-center gap-2 border border-white/10 backdrop-blur-md transition-all transform hover:-translate-y-1">
                                    <span class="text-lg">📋</span> Lihat Antre Tindakan
                                </a>
                            </div>
                        </div>
                    </div>

                    {{-- Card Bawah 1: Pasien Menolak / Batal --}}
                    <div class="card-enterprise p-6 flex items-center justify-between group hover:border-rose-200">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Penolakan / Batal</span>
                            <h3 class="text-3xl font-black text-rose-600 tracking-tight">{{ $rejectedPatients ?? 0 }} <span class="text-xs text-rose-400 font-bold ml-1">Kasus</span></h3>
                            <span class="text-[11px] text-slate-400 font-medium">Riwayat pembatalan tindakan</span>
                        </div>
                        <div class="w-14 h-14 rounded-2xl bg-rose-50 border border-rose-100 text-rose-500 flex items-center justify-center font-black text-2xl shadow-inner group-hover:scale-110 group-hover:-rotate-6 transition-transform">✕</div>
                    </div>

                    {{-- Card Bawah 2: Keamanan Sistem --}}
                    <div class="card-enterprise p-6 flex items-center space-x-4 bg-gradient-to-br from-white via-indigo-50/30 to-white relative overflow-hidden group">
                        <div class="absolute right-0 top-0 w-24 h-full bg-gradient-to-l from-indigo-50/50 to-transparent transform translate-x-full group-hover:-translate-x-full transition-transform duration-1000 ease-in-out"></div>
                        <div class="w-14 h-14 rounded-2xl bg-indigo-50 border border-indigo-100 text-indigo-600 flex items-center justify-center text-2xl shadow-inner shrink-0 group-hover:scale-110 transition-transform">🛡️</div>
                        <div>
                            <h5 class="text-sm font-black text-slate-900 tracking-tight">Secure Environment</h5>
                            <p class="text-[11px] text-slate-500 font-medium mt-1">Data medik terenkripsi & terlindungi.</p>
                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>

</x-app-layout>
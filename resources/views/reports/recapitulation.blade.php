<x-app-layout>
    <!-- Header Rekapitulasi Professional -->
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 py-5">
            <div>
                <div class="flex items-center space-x-3 mb-2.5">
                    <span class="inline-flex items-center px-3.5 py-1.5 bg-sky-50 text-sky-700 font-black text-[10px] rounded-xl uppercase tracking-widest border border-sky-200/80 shadow-2xs">
                        Modul Rekapitulasi & Laporan Eksekutif
                    </span>
                    <span class="text-sky-300 font-bold">•</span>
                    <span class="text-xs font-extrabold text-slate-400 tracking-wider uppercase">Cathlab RSUD Blambangan</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Rekapitulasi Komprehensif Layanan
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-1">Analisis volume pasien, distribusi jaminan pembiayaan, kategori divisi, dan jenis tindakan medis.</p>
            </div>
            
            <div class="flex items-center gap-3">
                <button onclick="window.print()" class="inline-flex items-center px-5 py-3.5 bg-sky-600 hover:bg-sky-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-sky-600/20 transition-all cursor-pointer">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Laporan Resmi
                </button>
                <a href="{{ route('reports.index') }}" class="inline-flex items-center px-5 py-3.5 bg-white border border-sky-200/80 text-sky-700 hover:bg-sky-50/60 font-black text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8" x-data="{ 
        filterType: '{{ request('filter_type', 'all') }}',
        animateValue(target, end, duration) {
            let start = 0;
            let range = end - start;
            let current = start;
            let increment = end > start ? 1 : -1;
            let stepTime = Math.abs(Math.floor(duration / range));
            if(range === 0) { target.innerText = end; return; }
            let timer = setInterval(() => {
                current += Math.ceil(range / 20);
                if ((increment > 0 && current >= end) || (increment < 0 && current <= end)) {
                    current = end;
                    clearInterval(timer);
                }
                target.innerText = current.toLocaleString();
            }, 30);
        }
    }" x-init="
        $el.querySelectorAll('.counter-anim').forEach(el => {
            let targetVal = parseInt(el.getAttribute('data-target')) || 0;
            animateValue(el, targetVal, 800);
        });
    ">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- PANEL FILTER RENTANG WAKTU -->
            <div class="bg-white/90 backdrop-blur-xl p-6 rounded-[28px] border border-sky-100/85 shadow-xl shadow-sky-950/5 relative z-40">
                <form id="filterForm" method="GET" action="{{ route('reports.recapitulation') }}" class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="text-xs font-black text-slate-700 uppercase tracking-wider">Rentang Waktu:</span>
                        
                        <div class="inline-flex bg-slate-100 p-1 rounded-xl">
                            <button type="button" 
                                    @click="filterType = 'all'; $nextTick(() => document.getElementById('filterForm').submit())" 
                                    :class="filterType === 'all' ? 'bg-white text-sky-700 shadow-xs font-black' : 'text-slate-600 font-bold'" 
                                    class="px-4 py-2 text-xs rounded-lg transition-all cursor-pointer">Semua</button>
                            
                            <button type="button" 
                                    @click="filterType = 'daily'; $nextTick(() => document.getElementById('filterForm').submit())" 
                                    :class="filterType === 'daily' ? 'bg-white text-sky-700 shadow-xs font-black' : 'text-slate-600 font-bold'" 
                                    class="px-4 py-2 text-xs rounded-lg transition-all cursor-pointer">Harian</button>

                            <button type="button" 
                                    @click="filterType = 'monthly'; $nextTick(() => document.getElementById('filterForm').submit())" 
                                    :class="filterType === 'monthly' ? 'bg-white text-sky-700 shadow-xs font-black' : 'text-slate-600 font-bold'" 
                                    class="px-4 py-2 text-xs rounded-lg transition-all cursor-pointer">Per Bulan</button>
                            
                            <button type="button" 
                                    @click="filterType = 'yearly'; $nextTick(() => document.getElementById('filterForm').submit())" 
                                    :class="filterType === 'yearly' ? 'bg-white text-sky-700 shadow-xs font-black' : 'text-slate-600 font-bold'" 
                                    class="px-4 py-2 text-xs rounded-lg transition-all cursor-pointer">Per Tahun</button>
                        </div>
                        
                        <input type="hidden" name="filter_type" x-model="filterType">
                    </div>

                    <!-- Dropdown Pilihan Tanggal, Bulan, & Tahun -->
                    <div class="flex items-center gap-3">
                        
                        <!-- Pilihan Tanggal (Harian) -->
                        <div x-show="filterType === 'daily'" style="display: none;" 
                             x-data="{ 
                                openDate: false, 
                                selectedDate: '{{ request('date', $selectedDate) }}' 
                             }" class="relative z-50">
                            
                            <input type="hidden" name="date" x-model="selectedDate">

                            <button type="button" @click="openDate = !openDate" 
                                    class="flex items-center justify-between text-xs bg-white rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-sky-500 shadow-2xs py-2.5 px-3.5 text-left transition-all cursor-pointer min-w-[150px]">
                                <span class="font-bold text-slate-800" x-text="selectedDate"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 ml-2" :class="openDate ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="openDate" @click.away="openDate = false"
                                 class="absolute left-0 z-50 mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 max-h-60 overflow-y-auto"
                                 style="display: none;">
                                @forelse($availableDates ?? [] as $d)
                                    <div @click="selectedDate = '{{ $d }}'; openDate = false; $nextTick(() => document.getElementById('filterForm').submit())"
                                         class="px-4 py-2.5 text-xs text-slate-700 hover:bg-sky-50 hover:text-sky-700 cursor-pointer font-bold transition-colors flex items-center justify-between"
                                         :class="selectedDate === '{{ $d }}' ? 'bg-sky-50 text-sky-700 font-black' : ''">
                                        <span>{{ \Carbon\Carbon::parse($d)->format('d M Y') }}</span>
                                        <span x-show="selectedDate === '{{ $d }}'">
                                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                @empty
                                    <div class="px-4 py-2 text-xs text-slate-400 italic">Tidak ada tanggal di bulan ini</div>
                                @endforelse
                            </div>
                        </div>

                        <!-- Pilihan Bulan -->
                        <div x-show="filterType === 'daily' || filterType === 'monthly'" style="display: none;" 
                             x-data="{ 
                                openMonth: false, 
                                selectedMonth: '{{ request('month', $month) }}',
                                getMonthLabel(m) {
                                    const months = { '01': 'January', '02': 'February', '03': 'March', '04': 'April', '05': 'May', '06': 'June', '07': 'July', '08': 'August', '09': 'September', '10': 'October', '11': 'November', '12': 'December' };
                                    return months[m] || m;
                                }
                             }" class="relative z-50">
                            
                            <input type="hidden" name="month" x-model="selectedMonth">

                            <button type="button" @click="openMonth = !openMonth" 
                                    class="flex items-center justify-between text-xs bg-white rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-sky-500 shadow-2xs py-2.5 px-3.5 text-left transition-all cursor-pointer min-w-[140px]">
                                <span class="font-bold text-slate-800" x-text="getMonthLabel(selectedMonth)"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 ml-2" :class="openMonth ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="openMonth" @click.away="openMonth = false"
                                 class="absolute left-0 z-50 mt-2 w-48 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 max-h-60 overflow-y-auto"
                                 style="display: none;">
                                @foreach(range(1, 12) as $m)
                                    @php $mVal = sprintf('%02d', $m); @endphp
                                    <div @click="selectedMonth = '{{ $mVal }}'; openMonth = false; $nextTick(() => document.getElementById('filterForm').submit())"
                                         class="px-4 py-2.5 text-xs text-slate-700 hover:bg-sky-50 hover:text-sky-700 cursor-pointer font-bold transition-colors flex items-center justify-between"
                                         :class="selectedMonth === '{{ $mVal }}' ? 'bg-sky-50 text-sky-700 font-black' : ''">
                                        <span>{{ DateTime::createFromFormat('!m', $m)->format('F') }}</span>
                                        <span x-show="selectedMonth === '{{ $mVal }}'">
                                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <!-- Pilihan Tahun -->
                        <div x-show="filterType === 'daily' || filterType === 'monthly' || filterType === 'yearly'" style="display: none;"
                             x-data="{ 
                                openYear: false, 
                                selectedYear: '{{ request('year', $year) }}' 
                             }" class="relative z-50">
                            
                            <input type="hidden" name="year" x-model="selectedYear">

                            <button type="button" @click="openYear = !openYear" 
                                    class="flex items-center justify-between text-xs bg-white rounded-xl border border-slate-300 focus:border-sky-500 focus:ring-sky-500 shadow-2xs py-2.5 px-3.5 text-left transition-all cursor-pointer min-w-[110px]">
                                <span class="font-bold text-slate-800" x-text="selectedYear"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200 ml-2" :class="openYear ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="openYear" @click.away="openYear = false"
                                 class="absolute left-0 z-50 mt-2 w-32 bg-white rounded-2xl shadow-2xl border border-slate-200 py-2 max-h-60 overflow-y-auto"
                                 style="display: none;">
                                @foreach($availableYears as $y)
                                    <div @click="selectedYear = '{{ $y }}'; openYear = false; $nextTick(() => document.getElementById('filterForm').submit())"
                                         class="px-4 py-2.5 text-xs text-slate-700 hover:bg-sky-50 hover:text-sky-700 cursor-pointer font-bold transition-colors flex items-center justify-between"
                                         :class="selectedYear === '{{ $y }}' ? 'bg-sky-50 text-sky-700 font-black' : ''">
                                        <span>{{ $y }}</span>
                                        <span x-show="selectedYear === '{{ $y }}'">
                                            <svg class="w-3.5 h-3.5 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- STATISTIK UTAMA -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div class="bg-white/90 backdrop-blur-xl p-6 sm:p-7 rounded-[28px] border border-sky-100/85 shadow-xl shadow-sky-950/5 relative overflow-hidden group hover:border-sky-300 transition-all transform hover:-translate-y-1 duration-300">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black text-sky-600 uppercase tracking-widest">Total Pasien Terdaftar</p>
                        <span class="p-2.5 bg-sky-50 text-sky-600 rounded-2xl transition-transform group-hover:scale-110 duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </span>
                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mt-4">
                        <span class="counter-anim" data-target="{{ $totalPatients }}">0</span> 
                        <span class="text-lg font-bold text-slate-500">Orang</span>
                    </h3>
                    <div class="mt-3 flex items-center text-xs text-emerald-600 font-bold bg-emerald-50 px-3 py-1 rounded-xl w-fit">
                        <span>Mode Filter: {{ ucfirst(request('filter_type', 'all')) }}</span>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-xl p-6 sm:p-7 rounded-[28px] border border-sky-100/85 shadow-xl shadow-sky-950/5 relative overflow-hidden group hover:border-sky-300 transition-all transform hover:-translate-y-1 duration-300">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Akumulasi Tindakan Medis</p>
                        <span class="p-2.5 bg-indigo-50 text-indigo-600 rounded-2xl transition-transform group-hover:scale-110 duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </span>
                    </div>
                    <h3 class="text-4xl font-black text-indigo-600 mt-4">
                        <span class="counter-anim" data-target="{{ $totalActions }}">0</span> 
                        <span class="text-lg font-bold text-slate-500">Prosedur</span>
                    </h3>
                    <div class="mt-3 flex items-center text-xs text-indigo-600 font-bold bg-indigo-50 px-3 py-1 rounded-xl w-fit">
                        <span>Total layanan Cathlab terlaksana</span>
                    </div>
                </div>

                <div class="bg-white/90 backdrop-blur-xl p-6 sm:p-7 rounded-[28px] border border-sky-100/85 shadow-xl shadow-sky-950/5 relative overflow-hidden group hover:border-sky-300 transition-all transform hover:-translate-y-1 duration-300">
                    <div class="flex items-center justify-between">
                        <p class="text-[10px] font-black text-sky-600 uppercase tracking-widest">Rasio Tindakan / Pasien</p>
                        <span class="p-2.5 bg-sky-50 text-sky-600 rounded-2xl transition-transform group-hover:scale-110 duration-300">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"/></svg>
                        </span>
                    </div>
                    <h3 class="text-4xl font-black text-slate-900 mt-4">
                        {{ $totalPatients > 0 ? number_format($totalActions / $totalPatients, 2) : '0' }}
                        <span class="text-lg font-bold text-slate-500">Rasio</span>
                    </h3>
                    <div class="mt-3 flex items-center text-xs text-sky-600 font-bold bg-sky-50 px-3 py-1 rounded-xl w-fit">
                        <span>Indeks produktivitas layanan</span>
                    </div>
                </div>
            </div>

            <!-- GRID TIGA KOLOM TABEL ANALITIK (Jaminan, Kategori, Tindakan) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- TABEL 1: REKAP JAMINAN KESEHATAN -->
                <div class="bg-white/90 backdrop-blur-xl rounded-[32px] border border-sky-100/80 shadow-xl shadow-sky-950/5 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-6 sm:p-7 border-b border-sky-100/60 bg-gradient-to-r from-sky-50/40 to-transparent flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Jaminan / Pembiayaan</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Proporsi pasien berdasar penjamin.</p>
                            </div>
                            <span class="px-3 py-1 bg-sky-100 text-sky-800 text-[10px] font-black rounded-xl">Asuransi</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-sky-50/60 text-[11px] font-black text-sky-800 uppercase tracking-wider border-b border-sky-100/60">
                                    <tr>
                                        <th class="px-5 py-4">Jenis Jaminan</th>
                                        <th class="px-5 py-4 text-center">Persen</th>
                                        <th class="px-5 py-4 text-right">Jumlah</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-sky-50 font-medium">
                                    @forelse($recapByInsurance as $ins)
                                    @php
                                        $percentageIns = $totalPatients > 0 ? round(($ins->total / $totalPatients) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-sky-50/40 transition-colors">
                                        <td class="px-5 py-4 font-black text-slate-900 truncate max-w-[130px]">{{ $ins->insurance_name ?? 'Mandiri / Umum' }}</td>
                                        <td class="px-5 py-4 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-xs font-bold text-slate-700">{{ $percentageIns }}%</span>
                                                <div class="w-16 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                                    <div class="bg-sky-500 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: 0%" x-init="setTimeout(() => $el.style.width = '{{ $percentageIns }}%', 100)"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-right font-black text-sky-700">{{ number_format($ins->total) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-slate-400 italic">Belum ada data.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TABEL 2: REKAP KATEGORI DIVISI -->
                <div class="bg-white/90 backdrop-blur-xl rounded-[32px] border border-sky-100/80 shadow-xl shadow-sky-950/5 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-6 sm:p-7 border-b border-sky-100/60 bg-gradient-to-r from-sky-50/40 to-transparent flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Kategori Divisi</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Volume tindakan per kategori divisi.</p>
                            </div>
                            <span class="px-3 py-1 bg-violet-100 text-violet-800 text-[10px] font-black rounded-xl">Divisi</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-sky-50/60 text-[11px] font-black text-sky-800 uppercase tracking-wider border-b border-sky-100/60">
                                    <tr>
                                        <th class="px-5 py-4">Nama Kategori</th>
                                        <th class="px-5 py-4 text-center">Persen</th>
                                        <th class="px-5 py-4 text-right">Kasus</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-sky-50 font-medium">
                                    @forelse($recapByCategory as $cat)
                                    @php
                                        $percentageCat = $totalActions > 0 ? round(($cat->total / $totalActions) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-sky-50/40 transition-colors">
                                        <td class="px-5 py-4 font-black text-slate-900 truncate max-w-[130px]">{{ $cat->name }}</td>
                                        <td class="px-5 py-4 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-xs font-bold text-slate-700">{{ $percentageCat }}%</span>
                                                <div class="w-16 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                                    <div class="bg-violet-500 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: 0%" x-init="setTimeout(() => $el.style.width = '{{ $percentageCat }}%', 100)"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-right font-black text-violet-600">{{ number_format($cat->total) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-slate-400 italic">Belum ada data.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- TABEL 3: REKAP JENIS TINDAKAN MEDIS -->
                <div class="bg-white/90 backdrop-blur-xl rounded-[32px] border border-sky-100/80 shadow-xl shadow-sky-950/5 overflow-hidden flex flex-col justify-between">
                    <div>
                        <div class="p-6 sm:p-7 border-b border-sky-100/60 bg-gradient-to-r from-sky-50/40 to-transparent flex items-center justify-between">
                            <div>
                                <h4 class="text-sm font-black text-slate-900 uppercase tracking-wider">Jenis Tindakan</h4>
                                <p class="text-xs text-slate-400 mt-0.5">Peringkat prosedur terbanyak.</p>
                            </div>
                            <span class="px-3 py-1 bg-indigo-100 text-indigo-800 text-[10px] font-black rounded-xl">Prosedur</span>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-sky-50/60 text-[11px] font-black text-sky-800 uppercase tracking-wider border-b border-sky-100/60">
                                    <tr>
                                        <th class="px-5 py-4">Nama Prosedur</th>
                                        <th class="px-5 py-4 text-center">Persen</th>
                                        <th class="px-5 py-4 text-right">Kasus</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-sky-50 font-medium">
                                    @forelse($recapByAction as $act)
                                    @php
                                        $percentageAct = $totalActions > 0 ? round(($act->total / $totalActions) * 100, 1) : 0;
                                    @endphp
                                    <tr class="hover:bg-sky-50/40 transition-colors">
                                        <td class="px-5 py-4 font-black text-slate-900 truncate max-w-[130px]">{{ $act->action_name }}</td>
                                        <td class="px-5 py-4 text-center">
                                            <div class="inline-flex flex-col items-center">
                                                <span class="text-xs font-bold text-slate-700">{{ $percentageAct }}%</span>
                                                <div class="w-16 bg-slate-100 rounded-full h-1.5 mt-1 overflow-hidden">
                                                    <div class="bg-indigo-500 h-1.5 rounded-full transition-all duration-1000 ease-out" style="width: 0%" x-init="setTimeout(() => $el.style.width = '{{ $percentageAct }}%', 100)"></div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-5 py-4 text-right font-black text-indigo-600">{{ number_format($act->total) }}</td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" class="px-5 py-8 text-center text-slate-400 italic">Belum ada data.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

            <!-- TABEL TAMBAHAN: REKAPITULASI HARIAN (PER TANGGAL YANG ADA PASIEN) -->
            <div class="bg-white/90 backdrop-blur-xl rounded-[32px] border border-sky-100/80 shadow-xl shadow-sky-950/5 overflow-hidden">
                <div class="p-6 sm:p-7 border-b border-sky-100/60 bg-gradient-to-r from-sky-50/40 to-transparent flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h4 class="text-base font-black text-slate-900 uppercase tracking-wider">Rincian Rekapitulasi Per Tanggal</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Daftar tanggal aktif yang mencatat kunjungan pasien dan tindakan medis.</p>
                    </div>
                    <span class="px-3.5 py-1.5 bg-emerald-100 text-emerald-800 text-xs font-black rounded-xl w-fit">Timeline Harian</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-sky-50/60 text-[11px] font-black text-sky-800 uppercase tracking-wider border-b border-sky-100/60">
                            <tr>
                                <th class="px-6 py-4">Tanggal</th>
                                <th class="px-6 py-4 text-center">Jumlah Pasien Unik</th>
                                <th class="px-6 py-4 text-right">Total Tindakan Medis</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-sky-50 font-medium">
                            @forelse($recapByDate as $row)
                            <tr class="hover:bg-sky-50/40 transition-colors">
                                <td class="px-6 py-4 font-black text-slate-900 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-sky-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    {{ \Carbon\Carbon::parse($row->action_date)->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-4 text-center font-bold text-slate-700">
                                    <span class="px-3 py-1 bg-sky-50 text-sky-700 rounded-xl text-xs">{{ number_format($row->total_patients) }} Pasien</span>
                                </td>
                                <td class="px-6 py-4 text-right font-black text-indigo-600">
                                    <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-xl text-xs">{{ number_format($row->total_actions) }} Prosedur</span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data rekapitulasi harian untuk periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
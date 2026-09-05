@php
    $routePatient = request()->route('patient');
    $patientObj = null;
    if ($routePatient) {
        $patientObj = $routePatient instanceof \App\Models\Patient ? $routePatient : \App\Models\Patient::find($routePatient);
    }
@endphp

<div x-data="{ collapsed: false, showBackupModal: false, typedConfirmation: '' }" class="flex min-h-screen bg-slate-100">

    <!-- SIDEBAR -->
    <aside :class="collapsed ? 'w-20' : 'w-72'" class="bg-slate-100 border-r border-slate-200 h-screen sticky top-0 flex flex-col shrink-0 z-30 transition-all duration-300 ease-in-out shadow-xl">
        
        <!-- HEADER / LOGO SECTION -->
        <div class="h-24 flex items-center px-6 border-b border-slate-200 shrink-0">
            <div class="flex items-center space-x-3.5 w-full">
                <!-- Logo Diperbesar -->
                <div class="w-14 h-14 rounded-2xl bg-slate-100 flex items-center justify-center shrink-0 overflow-hidden p-2 shadow-md border border-slate-200">
                    <img src="{{ asset('images/IMGLOGO.png') }}" alt="Logo Cathlab" class="w-full h-full object-contain">
                </div>
                
                <div x-show="!collapsed" x-transition.opacity class="truncate flex-1">
                    <span class="text-base font-black tracking-tight text-slate-800 block leading-tight">Cathlab BWI</span>
                    <span class="text-[10px] font-extrabold text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2.5 py-0.5 rounded-md border border-indigo-100 inline-block mt-0.5">Medical System</span>
                </div>
                
                <button @click="collapsed = !collapsed" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-500 transition-all shadow-sm border border-slate-200 cursor-pointer">
                    <svg class="w-4 h-4 transition-transform duration-300" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/></svg>
                </button>
            </div>
        </div>

        <!-- NAVIGATION MENU -->
        <nav class="flex-1 py-6 px-4 space-y-4 overflow-y-auto custom-scrollbar">
            
            @if($patientObj)
                <!-- PASIEN AKTIF CARD -->
                <div class="bg-slate-100 rounded-3xl p-4 mb-4 shadow-inner border border-slate-200" x-show="!collapsed">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-[9px] font-black text-indigo-600 uppercase tracking-widest bg-indigo-50 px-2.5 py-0.5 rounded-lg border border-indigo-100">Pasien Aktif</span>
                        <a href="{{ route('patients.index') }}" class="text-[11px] font-bold text-indigo-600 hover:underline">&larr; Kembali</a>
                    </div>
                    <span class="text-sm font-black text-slate-800 truncate block">{{ $patientObj->name }}</span>
                    <span class="text-xs font-semibold text-slate-500 block mt-0.5">RM: {{ $patientObj->medical_record_number ?? '—' }}</span>
                </div>
                
                <div class="space-y-1.5">
                    <a href="{{ route('patients.show', $patientObj->id) }}" class="flex items-center px-3.5 py-3 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('patients.show') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200 shadow-sm' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                        <span x-show="!collapsed">Data Pasien</span>
                    </a>
                    <a href="{{ route('patients.call-history', $patientObj->id) }}" class="flex items-center px-3.5 py-3 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('patients.call-history') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200 shadow-sm' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                        <span x-show="!collapsed">Riwayat Panggilan</span>
                    </a>
                    <a href="{{ route('patients.actions-history', $patientObj->id) }}" class="flex items-center px-3.5 py-3 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('patients.actions-history') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200 shadow-sm' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        <span x-show="!collapsed">Riwayat Tindakan</span>
                    </a>
                    <a href="{{ route('patients.documents', $patientObj->id) }}" class="flex items-center px-3.5 py-3 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('patients.documents') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200 shadow-sm' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                        <span x-show="!collapsed">Dokumen</span>
                    </a>
                    <a href="{{ route('patients.bhp', $patientObj->id) }}" class="flex items-center px-3.5 py-3 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('patients.bhp') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200 shadow-sm' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/></svg>
                        <span x-show="!collapsed">BHP per Tanggal</span>
                    </a>
                    <a href="{{ route('patients.dicom.index', $patientObj->id) }}" class="flex items-center px-3.5 py-3 rounded-2xl text-xs font-bold transition-all {{ request()->routeIs('patients.dicom.*') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-600 hover:bg-slate-200 shadow-sm' }}">
                        <svg class="w-4 h-4 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <span x-show="!collapsed">DICOM Viewer</span>
                    </a>
                </div>
            @else

                @can('akses-dashboard')
                <div class="px-1">
                    <a href="{{ route('dashboard') }}" class="flex items-center px-3.5 py-3.5 rounded-2xl text-xs font-black transition-all {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md' : 'text-slate-700 hover:bg-slate-200 shadow-sm border border-slate-200' }}">
                        <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/></svg>
                        <span x-show="!collapsed">Dashboard</span>
                    </a>
                </div>
                @endcan

                <!-- MENU GROUP: DATA PASIEN -->
                @canany(['pendaftaran-pasien', 'riwayat-tindakan'])
                <div class="rounded-3xl p-3.5 shadow-inner border border-slate-200 transition-all" x-data="{ openPatient: {{ request()->routeIs(['patients.*', 'actions.history.*']) ? 'true' : 'false' }} }">
                    <button @click="openPatient = !openPatient" class="w-full flex items-center justify-between px-2 py-1 text-xs font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors cursor-pointer">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                            <span x-show="!collapsed">Data Pasien</span>
                        </div>
                        <svg x-show="!collapsed" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" :class="openPatient ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openPatient && !collapsed" class="mt-2.5 space-y-1.5">
                        @can('pendaftaran-pasien')
                        <a href="{{ route('patients.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('patients.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            Pendaftaran Pasien
                        </a>
                        @endcan
                         @can('pendaftaran-pasien')
<a href="{{ route('patient.portal.qrcode-view') }}" class="w-full flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-slate-200 text-left cursor-pointer">
    <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v1m6 11h2m-6 0h-2v4m0-11v3m0 0h.01M12 12h4.01M16 20h4M4 12h4m12 0h.01M5 8h2a1 1 0 001-1V5a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1zm12 0h2a1 1 0 001-1V5a1 1 0 00-1-1h-2a1 1 0 00-1 1v2a1 1 0 001 1zM5 20h2a1 1 0 001-1v-2a1 1 0 00-1-1H5a1 1 0 00-1 1v2a1 1 0 001 1z"/>
    </svg>
    QR Code Pendaftaran
</a>
@endcan

                        @can('riwayat-tindakan')
                        <a href="{{ route('actions.history.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('actions.history.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            Riwayat Tindakan
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- MENU GROUP: LAPORAN & STATISTIK -->
                @canany(['laporan-ringkasan', 'laporan-klinis', 'laporan-operasional', 'laporan-rekapitulasi', 'cetak-laporan', 'cek-bhp'])
                <div class="rounded-3xl p-3.5 shadow-inner border border-slate-200 transition-all" x-data="{ openReports: {{ request()->routeIs(['reports.*', 'check-bhp.*']) ? 'true' : 'false' }} }">
                    <button @click="openReports = !openReports" class="w-full flex items-center justify-between px-2 py-1 text-xs font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors cursor-pointer">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                            <span x-show="!collapsed">Laporan & Statistik</span>
                        </div>
                        <svg x-show="!collapsed" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" :class="openReports ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openReports && !collapsed" class="mt-2.5 space-y-1.5">
                        @can('laporan-ringkasan')
                        <a href="{{ route('reports.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('reports.index') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"/></svg>
                            Ringkasan Utama
                        </a>
                        @endcan
                        @can('laporan-klinis')
                        <a href="{{ route('reports.clinical') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('reports.clinical') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/></svg>
                            Performa Klinis
                        </a>
                        @endcan
                        @can('laporan-operasional')
                        <a href="{{ route('reports.operational') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('reports.operational') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Efisiensi Operasional
                        </a>
                        @endcan
                        @can('laporan-rekapitulasi')
                        <a href="{{ route('reports.recapitulation') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('reports.recapitulation') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                            Rekapitulasi
                        </a>
                        @endcan
                        @can('cek-bhp')
                        <a href="{{ route('check-bhp.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('check-bhp.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19.071 4.929c1.953 1.952 1.953 5.118 0 7.071L12 19.071l-7.071-7.07c-1.953-1.953-1.953-5.119 0-7.071 1.953-1.952 5.118-1.952 7.07 0l7.072 7.07z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8.464 15.536l7.072-7.072"/>
                            </svg>
                            Cek BHP
                        </a>
                        @endcan
                        @can('cetak-laporan')
                        <a href="{{ route('reports.selection.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('reports.selection.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                            Cetak Laporan
                        </a>
                        @endcan
                    </div>
                </div>
                @endcanany

                <!-- MENU GROUP: MASTER DATA -->
                @canany(['kelola-kategori-divisi', 'kelola-sub-divisi', 'kelola-tindakan', 'kelola-dokter', 'kelola-jaminan', 'kelola-penunjang', 'kelola-role', 'kelola-user', 'backup-laporan'])
                <div class="rounded-3xl p-3.5 shadow-inner border border-slate-200 transition-all" x-data="{ openMaster: {{ request()->routeIs(['categories.*', 'sub-divisions.*', 'actions.index', 'actions.create', 'actions.edit', 'doctors.*', 'insurances.*', 'supporting-options.*', 'roles.*', 'users.*']) ? 'true' : 'false' }} }">
                    <button @click="openMaster = !openMaster" class="w-full flex items-center justify-between px-2 py-1 text-xs font-black text-slate-500 uppercase tracking-widest hover:text-slate-800 transition-colors cursor-pointer">
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2 text-indigo-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"/></svg>
                            <span x-show="!collapsed">Master Data</span>
                        </div>
                        <svg x-show="!collapsed" class="w-3.5 h-3.5 transition-transform duration-200 text-slate-400" :class="openMaster ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="openMaster && !collapsed" class="mt-2.5 space-y-1.5">
                        @can('kelola-kategori-divisi')
                        <a href="{{ route('categories.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('categories.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Kategori Divisi
                        </a>
                        @endcan
                        @can('kelola-sub-divisi')
                        <a href="{{ route('sub-divisions.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('sub-divisions.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                            Sub-Divisi
                        </a>
                        @endcan
                        @can('kelola-tindakan')
                        <a href="{{ route('actions.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('actions.index') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            Daftar Tindakan
                        </a>
                        @endcan
                        @can('kelola-dokter')
                        <a href="{{ route('doctors.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('doctors.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                            Daftar Dokter
                        </a>
                        @endcan
                        @can('kelola-jaminan')
                        <a href="{{ route('insurances.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('insurances.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            Kelola Jaminan
                        </a>
                        @endcan
                        @can('kelola-penunjang')
                        <a href="{{ route('supporting-options.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('supporting-options.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2h0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            Kelola Penunjang
                        </a>
                        @endcan
                        @can('kelola-role')
                        <a href="{{ route('roles.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('roles.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            Kelola Role
                        </a>
                        @endcan
                        @can('kelola-user')
                        <a href="{{ route('users.index') }}" class="flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-sm' : 'text-slate-600 hover:bg-slate-200' }}">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            Kelola User
                        </a>
                        @endcan

                       
                        @can('backup-laporan')
                        <button @click="showBackupModal = true; typedConfirmation = ''" type="button" class="w-full flex items-center px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all text-slate-600 hover:bg-slate-200 text-left cursor-pointer">
                            <svg class="w-4 h-4 mr-2.5 shrink-0 opacity-80 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            Backup Laporan Data
                        </button>
                        @endcan
                    </div>
                </div>
                @endcanany

            @endif
        </nav>
    </aside>

    <!-- BACKUP MODAL -->
    <div x-show="showBackupModal" 
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm px-4"
         style="display: none;"
         x-cloak>
        
        <div @click.away="showBackupModal = false" 
             class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-slate-200 space-y-5"
             x-transition>
            
            <div class="flex items-center space-x-4 text-amber-600">
                <div class="p-3.5 bg-amber-50 rounded-2xl border border-amber-100 shadow-inner">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-black text-slate-900">Konfirmasi Backup Data</h3>
                    <p class="text-xs text-slate-500 font-medium">Tindakan ini akan mengunduh seluruh data historis sistem secara lengkap.</p>
                </div>
            </div>

            <p class="text-xs text-slate-600 leading-relaxed font-medium">
                Untuk melanjutkan proses download, silakan ketik kata <span class="font-black text-slate-900 uppercase bg-slate-100 px-2 py-1 rounded-lg border border-slate-200">KONFIRMASI</span> di bawah ini:
            </p>

            <input type="text" 
                   x-model="typedConfirmation" 
                   placeholder="Ketik KONFIRMASI di sini" 
                   class="w-full text-xs font-bold rounded-2xl border-slate-200 bg-slate-50 py-3 px-4 focus:bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 shadow-sm transition-all placeholder:text-slate-400 outline-none">

            <div class="flex justify-end space-x-3 pt-2">
                <button @click="showBackupModal = false" 
                        type="button"
                        class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider rounded-2xl transition-all cursor-pointer shadow-sm">
                    Batal
                </button>

                <a :href="typedConfirmation === 'KONFIRMASI' ? '{{ route('master.backup.laporan') }}' : '#'" 
                   @click="if(typedConfirmation === 'KONFIRMASI') { showBackupModal = false; }"
                   :class="typedConfirmation === 'KONFIRMASI' ? 'bg-emerald-600 hover:bg-emerald-500 text-white cursor-pointer shadow-lg shadow-emerald-600/25' : 'bg-slate-200 text-slate-400 cursor-not-allowed pointer-events-none'"
                   class="px-6 py-3 text-xs font-black uppercase tracking-wider rounded-2xl transition-all inline-flex items-center gap-2">
                    Unduh File
                </a>
            </div>
        </div>
    </div>
</div>
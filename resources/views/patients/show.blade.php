<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5 py-5">
            <div>
                <div class="flex items-center space-x-3 mb-2.5">
                    <span class="inline-flex items-center px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-black text-[10px] rounded-xl uppercase tracking-widest border border-indigo-100 shadow-2xs">
                        Modul Rekam Medis
                    </span>
                    <span class="text-indigo-300 font-bold">•</span>
                    <span class="text-xs font-extrabold text-slate-400 tracking-wider uppercase">Profil & Administrasi Pasien</span>
                </div>
                <div class="flex items-center flex-wrap gap-3.5">
                    <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                        {{ $patient->name }}
                    </h2>
                    
                    @php
                        $isFemale = $patient->gender === 'P';
                        $rmBadgeBg = $isFemale ? 'bg-pink-50 text-pink-700 border-pink-200' : 'bg-indigo-50 text-indigo-700 border-indigo-100';
                        $rmTextCol = $isFemale ? 'text-pink-500' : 'text-indigo-500';
                    @endphp
                    <span class="inline-flex items-center px-3.5 py-1.5 {{ $rmBadgeBg }} font-black text-xs rounded-xl border shadow-2xs tracking-wide">
                        <span class="{{ $rmTextCol }} font-extrabold mr-1.5">RM:</span> {{ $patient->medical_record_number ?? 'Belum ada' }}
                    </span>

                    @if($patient->is_priority)
                        <span class="inline-flex items-center px-3.5 py-1 text-[10px] font-black bg-rose-50 text-rose-600 border border-rose-200/80 rounded-full uppercase tracking-widest shadow-2xs animate-pulse">
                            <span class="w-1.5 h-1.5 rounded-full bg-rose-500 mr-1.5"></span> Prioritas Utama
                        </span>
                    @endif
                </div>
            </div>
            
            <div class="flex items-center flex-wrap gap-3">
                @if(!empty($patient->scheduled_at) && $patient->actionRecords->isEmpty())
                    <a href="{{ route('patients.actions.create', $patient->id) }}" class="inline-flex items-center px-5 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-indigo-600/25 transition-all transform active:scale-95">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/></svg>
                        Input Tindakan
                    </a>
                @endif

                <a href="{{ route('patients.edit', $patient->id) }}" class="inline-flex items-center px-5 py-3.5 bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 font-black text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all">
                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                    Edit Data
                </a>
                
                <a href="{{ route('patients.index') }}" class="inline-flex items-center px-5 py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-md transition-all">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/60 min-h-screen text-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

                <div class="lg:col-span-7 bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.04)] p-6 sm:p-8 flex flex-col justify-between space-y-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-64 h-64 bg-gradient-to-br from-indigo-50 to-transparent rounded-full blur-3xl pointer-events-none -mr-16 -mt-16"></div>

                    <div>
                        <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-6 relative z-10">
                            <div class="flex items-center space-x-3.5">
                                <div class="w-11 h-11 rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white flex items-center justify-center text-xs font-black shadow-md shadow-indigo-600/20">
                                    ID
                                </div>
                                <div>
                                    <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Identitas & Administrasi Pasien</h3>
                                    <p class="text-[11px] font-bold text-slate-400 mt-0.5">Demografi, jalur masuk, dan penjamin kesehatan</p>
                                </div>
                            </div>
                            <span class="inline-flex items-center text-[11px] font-extrabold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200/60 shadow-2xs">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2 animate-pulse"></span> Terverifikasi
                            </span>
                        </div>
                        
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 relative z-10">
                            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 hover:bg-slate-50 transition-all">
                                <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Sumber / Jalur Masuk</span>
                                <span class="text-xs font-black text-slate-900 uppercase tracking-wider block">{{ str_replace('_', ' ', $patient->source) }}</span>
                                @if($patient->origin_hospital)
                                    <span class="block text-[11px] font-bold text-slate-500 mt-0.5 truncate" title="{{ $patient->origin_hospital }}">🏥 {{ $patient->origin_hospital }}</span>
                                @endif
                            </div>

                            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 hover:bg-slate-50 transition-all">
                                <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Jaminan / Pembiayaan</span>
                                <span class="text-xs font-black text-indigo-600 uppercase tracking-wider">{{ $patient->insurance->name ?? '—' }}</span>
                            </div>

                            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 hover:bg-slate-50 transition-all">
                                <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Jenis Kelamin & Usia</span>
                                <span class="text-xs font-black text-slate-900">
                                    {{ $patient->gender == 'L' ? 'Laki-laki' : 'Perempuan' }} • <span class="text-indigo-600 font-black">{{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} Tahun</span>
                                </span>
                            </div>

                            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 hover:bg-slate-50 transition-all">
                                <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">No. Telepon Pasien</span>
                                <span class="text-xs font-black text-slate-900">{{ $patient->patient_phone ?? '—' }}</span>
                            </div>

                            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 hover:bg-slate-50 transition-all">
                                <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">No. Telepon Keluarga</span>
                                <span class="text-xs font-black text-slate-900">{{ $patient->family_phone ?? '—' }}</span>
                            </div>

                            <div class="sm:col-span-2 bg-slate-50/80 p-4 rounded-2xl border border-slate-100 hover:bg-slate-50 transition-all">
                                <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Domisili / Alamat Lengkap</span>
                                <span class="text-xs font-bold text-slate-800 leading-relaxed">{{ $patient->address }}, Kec. {{ $patient->district }}, Kab. {{ $patient->regency }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.04)] p-6 sm:p-8 flex flex-col justify-between space-y-6">
                    <div>
                        <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4 mb-6">
                            <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base font-black shadow-inner border border-indigo-100">
                                📞
                            </div>
                            <div>
                                <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Status Pemanggilan & Jadwal</h3>
                                <p class="text-[11px] font-bold text-slate-400 mt-0.5">Histori kontak dan jadwal temu tindakan</p>
                            </div>
                        </div>
                        
                        <div class="space-y-3.5">
                            <div class="flex justify-between items-center bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Status Antrean</span>
                                @php
                                    $statusMap = [
                                        'bersedia' => ['bg' => 'bg-emerald-50 text-emerald-700 border-emerald-200', 'label' => 'Bersedia / Antre'],
                                        'menolak' => ['bg' => 'bg-rose-50 text-rose-700 border-rose-200', 'label' => 'Menolak'],
                                        'pernah_tindakan' => ['bg' => 'bg-indigo-50 text-indigo-700 border-indigo-200', 'label' => 'Selesai Tindakan'],
                                        'pending' => ['bg' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Menunggu Panggilan'],
                                    ];
                                    $statusAntre = $statusMap[$patient->status ?? 'pending'] ?? ['bg' => 'bg-slate-50 text-slate-700 border-slate-200', 'label' => ucfirst($patient->status ?? 'Pending')];
                                @endphp
                                <span class="px-3.5 py-1 text-xs font-black rounded-xl border {{ $statusAntre['bg'] }} shadow-2xs">
                                    {{ $statusAntre['label'] }}
                                </span>
                            </div>

                            <div class="flex justify-between items-center bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Konfirmasi Kesediaan</span>
                                <span class="text-xs font-black {{ $patient->willingness == 'bersedia' ? 'text-emerald-600' : ($patient->willingness == 'tidak_bersedia' ? 'text-rose-600' : 'text-slate-400') }}">
                                    {{ $patient->willingness ? ucfirst(str_replace('_', ' ', $patient->willingness)) : 'Belum ditentukan' }}
                                </span>
                            </div>

                            @if($patient->willingness == 'tidak_bersedia' && $patient->unwillingness_reason)
                                <div class="p-4 bg-rose-50 border border-rose-200 rounded-2xl space-y-1">
                                    <span class="block text-[10px] font-black text-rose-600 uppercase tracking-widest">Alasan Penolakan:</span>
                                    <p class="text-xs font-bold text-rose-800 leading-relaxed">{{ $patient->unwillingness_reason }}</p>
                                </div>
                            @endif

                            <div class="flex justify-between items-center bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Petugas Pemanggil</span>
                                <span class="text-xs font-black text-slate-900">{{ $patient->caller->name ?? '—' }}</span>
                            </div>

                            <div class="flex justify-between items-center bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                                <span class="text-xs font-extrabold text-slate-500 uppercase tracking-wider">Jadwal Temu Tindakan</span>
                                <span class="text-xs font-black text-indigo-700 bg-indigo-50 px-3.5 py-1.5 rounded-xl border border-indigo-100">
                                    {{ $patient->scheduled_at ? \Carbon\Carbon::parse($patient->scheduled_at)->translatedFormat('d F Y • H:i') : 'Belum Dijadwalkan' }}
                                </span>
                            </div>

                            <!-- TOKEN PORTAL PASIEN & TOMBOL KIRIM WA -->
                            <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 space-y-2">
                                <span class="block text-xs font-extrabold text-slate-500 uppercase tracking-wider">Token Portal Pasien</span>
                                @if($patient->portal_token)
                                    <div class="flex items-center justify-between gap-2 bg-white px-3.5 py-2.5 rounded-xl border border-slate-200/70 shadow-2xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-[10px] font-extrabold text-slate-400 uppercase">Token:</span>
                                            <span class="text-sm font-black font-mono text-sky-700 tracking-wider">{{ $patient->portal_token }}</span>
                                        </div>
                                        <a href="https://api.whatsapp.com/send?phone={{ preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $patient->patient_phone)) }}&text={{ urlencode('Halo *' . $patient->name . '*, berikut token akses 6 digit Anda untuk mengunduh dokumen medis Cathlab RSUD Blambangan: *' . $patient->portal_token . '*. Silakan masukkan token tersebut di portal pasien: ' . route('patient.portal.login')) }}" 
                                           target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white text-[10px] font-black rounded-xl uppercase inline-flex items-center gap-1.5 shadow-xs transition-all shrink-0">
                                           💬 Kirim WA
                                        </a>
                                    </div>
                                @else
                                    <form action="{{ route('patients.generate-portal-token', $patient->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full px-4 py-2.5 bg-slate-900 text-white text-xs font-black rounded-xl hover:bg-slate-800 uppercase tracking-wider transition-all shadow-sm cursor-pointer">
                                            🔑 Buat Token Portal
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.04)] p-6 sm:p-8 space-y-6">
                <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4">
                    <div class="w-11 h-11 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-base font-black shadow-inner border border-indigo-100">
                        📋
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Pemeriksaan Penunjang & Catatan Tambahan</h3>
                        <p class="text-[11px] font-bold text-slate-400 mt-0.5">Opsi penunjang medis terpilih serta catatan klinis pasien</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <span class="block text-xs font-black text-slate-500 uppercase tracking-wider">Opsi Penunjang Terpilih</span>
                        <div class="flex flex-wrap gap-2.5">
                            @forelse($patient->supportingOptions as $opt)
                                <span class="inline-flex items-center px-4 py-2.5 bg-indigo-50/80 text-indigo-700 text-xs font-black rounded-xl border border-indigo-100 shadow-2xs">
                                    <svg class="w-3.5 h-3.5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    {{ $opt->name }}
                                </span>
                            @empty
                                <div class="w-full bg-slate-50 p-4.5 rounded-2xl border border-slate-100 text-center">
                                    <span class="text-xs font-bold text-slate-400 italic">Tidak ada pemeriksaan penunjang yang dipilih.</span>
                                </div>
                            @endforelse
                        </div>
                    </div>

                    <div class="space-y-3">
                        <span class="block text-xs font-black text-slate-500 uppercase tracking-wider">Catatan Tambahan / Keterangan</span>
                        <div class="bg-slate-50 p-4.5 rounded-2xl border border-slate-100 min-h-[96px] flex items-center">
                            <p class="text-xs font-medium text-slate-700 leading-relaxed">{{ $patient->notes ?? 'Tidak ada catatan tambahan yang dicantumkan untuk pasien ini.' }}</p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
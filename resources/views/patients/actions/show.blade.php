<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-1">
            <div>
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <span class="px-3.5 py-1 bg-indigo-50 text-indigo-700 font-black text-[10px] rounded-full uppercase tracking-widest border border-indigo-100 shadow-sm">Rekam Medis</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400 tracking-wide uppercase">Detail Prosedur Medis</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Informasi Tindakan
                </h2>
            </div>
            <div>
                <a href="{{ route('patients.actions-history', $patient->id) }}" class="inline-flex items-center px-5 py-3 bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-sm transition-all duration-200">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- CARD UTAMA -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/50 overflow-hidden">
                
                <!-- HEADER CARD -->
                <div class="bg-gradient-to-r from-slate-900 to-indigo-900 p-8 sm:p-10 text-white">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                        <div>
                            <div class="inline-flex items-center px-3 py-1 bg-white/10 backdrop-blur rounded-lg text-[10px] font-black uppercase tracking-widest text-indigo-100 mb-4">
                                {{ $actionRecord->is_cito ? 'Status: CITO / Darurat' : 'Status: Prosedur Elektif' }}
                            </div>
                            <h1 class="text-3xl font-black mb-2">{{ $actionRecord->action->name ?? 'Tindakan' }}</h1>
                            <p class="text-indigo-200 font-medium">Pasien: <span class="text-white font-bold">{{ $patient->name }}</span></p>
                        </div>
                        <div class="text-left md:text-right">
                            <span class="text-[10px] font-black uppercase text-indigo-300 tracking-widest block">Waktu Prosedur</span>
                            <span class="text-lg font-black">{{ $actionRecord->action_date?->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>

                <!-- BODY CARD -->
                <div class="p-8 sm:p-10 space-y-8">
                    
                    <!-- GRID INFO -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div class="space-y-6">
                            <div>
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Tim Medis</span>
                                <div class="space-y-3">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-black text-xs">DP</div>
                                        <div>
                                            <p class="text-xs font-black text-slate-900">{{ $actionRecord->doctor->name }}</p>
                                            <p class="text-[10px] font-bold text-slate-500 uppercase">{{ $actionRecord->doctor->subDivision->name ?? 'Dokter Penanggung Jawab' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center space-x-3">
                                        <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center font-black text-xs">AN</div>
                                        <div>
                                            <p class="text-xs font-black text-slate-900">{{ $anesthesiaDoctor->name ?? '— Tanpa Anestesi —' }}</p>
                                            <p class="text-[10px] font-bold text-slate-500 uppercase">Dokter Anestesi</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="p-5 bg-slate-50 rounded-2xl border border-slate-100">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-2">Metadata Prosedur</span>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-500 uppercase">Divisi</p>
                                        <p class="text-xs font-black text-slate-900">{{ $actionRecord->category->name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold text-slate-500 uppercase">Ruangan</p>
                                        <p class="text-xs font-black text-slate-900">{{ $actionRecord->origin_ward }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- DIAGNOSA -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest border-l-4 border-indigo-500 pl-3">Asesmen Diagnosa</h4>
                        <div class="p-6 bg-indigo-50/50 rounded-2xl border border-indigo-100">
                            <p class="text-[10px] font-black text-indigo-400 uppercase tracking-widest mb-1">Diagnosa Utama</p>
                            <p class="text-sm font-bold text-slate-900 mb-4">{{ $actionRecord->diagnosis_1 }}</p>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Diagnosa Sekunder 2</p>
                                    <p class="text-xs font-semibold text-slate-700">{{ $actionRecord->diagnosis_2 ?: '—' }}</p>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Diagnosa Sekunder 3</p>
                                    <p class="text-xs font-semibold text-slate-700">{{ $actionRecord->diagnosis_3 ?: '—' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- KESIMPULAN & SARAN -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">Kesimpulan</h4>
                            <div class="p-5 bg-slate-50 rounded-2xl text-xs font-medium text-slate-700 leading-relaxed whitespace-pre-line">{{ $actionRecord->conclusion }}</div>
                        </div>
                        <div class="space-y-2">
                            <h4 class="text-xs font-black text-slate-900 uppercase tracking-wider">Saran & Tindak Lanjut</h4>
                            <div class="p-5 bg-emerald-50 rounded-2xl text-xs font-medium text-emerald-900 leading-relaxed whitespace-pre-line">{{ $actionRecord->suggestion }}</div>
                        </div>
                    </div>

                    @if($actionRecord->notes)
                    <div class="space-y-2">
                        <h4 class="text-xs font-black text-slate-900 uppercase tracking-widest">Catatan Tambahan</h4>
                        <div class="p-5 bg-amber-50 rounded-2xl text-xs font-medium text-amber-900">{{ $actionRecord->notes }}</div>
                    </div>
                    @endif
                </div>

                <!-- FOOTER CARD -->
                <div class="bg-slate-50 p-6 sm:p-8 flex items-center justify-end space-x-3">
                    <a href="{{ route('patients.actions.edit', [$patient->id, $actionRecord->id]) }}" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase rounded-xl transition-all shadow-lg shadow-indigo-600/20">
                        Edit Data
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-5 py-3">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 font-black text-[10px] rounded-full uppercase tracking-widest border border-indigo-100 shadow-sm">Modul Cathlab</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400 tracking-wider uppercase">Histori & Manajemen Tindakan</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Riwayat Tindakan: <span class="text-indigo-600 font-extrabold">{{ $patient->name }}</span>
                </h2>
            </div>
            <div class="flex items-center space-x-3.5">
                <a href="{{ route('patients.actions.create', $patient->id) }}" class="inline-flex items-center px-5.5 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-xl shadow-indigo-600/30 hover:shadow-indigo-600/50 transition-all duration-300 transform hover:-translate-y-0.5">
                    + Tambah Tindakan
                </a>
                <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex items-center px-5 py-3.5 bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-sm transition-all duration-300">
                    &larr; Detail Pasien
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- STATS / SUMMARY BAR -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <!-- Total Card -->
                <div class="bg-white p-7 rounded-[28px] border border-slate-100 shadow-xl shadow-slate-100/60 flex items-center justify-between transition-all duration-300 hover:shadow-2xl hover:border-indigo-100 group">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Total Prosedur</span>
                        <span class="text-4xl font-black text-slate-900 block group-hover:text-indigo-600 transition-colors">{{ $actions->count() }}</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-bold shadow-inner transition-transform duration-300 group-hover:scale-105">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    </div>
                </div>

                <!-- CITO Card -->
                <div class="bg-white p-7 rounded-[28px] border border-slate-100 shadow-xl shadow-slate-100/60 flex items-center justify-between transition-all duration-300 hover:shadow-2xl hover:border-rose-100 group">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Kasus CITO</span>
                        <span class="text-4xl font-black text-rose-600 block">{{ $actions->where('is_cito', true)->count() }}</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center font-bold shadow-inner transition-transform duration-300 group-hover:scale-105">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    </div>
                </div>

                <!-- Elektif Card -->
                <div class="bg-white p-7 rounded-[28px] border border-slate-100 shadow-xl shadow-slate-100/60 flex items-center justify-between transition-all duration-300 hover:shadow-2xl hover:border-emerald-100 group">
                    <div>
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-1.5">Kasus Elektif</span>
                        <span class="text-4xl font-black text-emerald-600 block">{{ $actions->where('is_cito', false)->count() }}</span>
                    </div>
                    <div class="w-16 h-16 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold shadow-inner transition-transform duration-300 group-hover:scale-105">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    </div>
                </div>
            </div>

            <!-- MAIN LIST CONTAINER -->
            <div class="space-y-5">
                <div class="flex items-center justify-between px-3">
                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Daftar Rekam Medis Prosedur</h3>
                    <span class="text-xs font-extrabold text-indigo-600 bg-indigo-50 px-4 py-1.5 rounded-xl border border-indigo-100/80 shadow-sm">Urutan Kronologis</span>
                </div>

                @forelse($actions as $act)
                <!-- MODULAR CARD ITEM -->
                <div class="bg-white rounded-[28px] border border-slate-100 shadow-xl shadow-slate-100/50 overflow-hidden transition-all duration-300 hover:shadow-2xl hover:border-indigo-100">
                    
                    <div class="p-6 sm:p-8 grid grid-cols-1 lg:grid-cols-12 gap-6 items-center">
                        
                        <!-- Kolom Kiri: Waktu & Identitas Tindakan -->
                        <div class="lg:col-span-5 flex items-start space-x-5">
                            <div class="w-14 h-14 rounded-2xl bg-slate-50 border border-slate-100 flex flex-col items-center justify-center text-center shrink-0 shadow-sm">
                                <span class="text-[10px] font-black text-indigo-600 uppercase tracking-wider">{{ ($act->action_date ?? $act->created_at)?->format('M') }}</span>
                                <span class="text-lg font-black text-slate-800 leading-none mt-0.5">{{ ($act->action_date ?? $act->created_at)?->format('d') }}</span>
                            </div>
                            <div class="space-y-1.5 min-w-0">
                                <div class="flex items-center space-x-2.5 flex-wrap gap-y-1">
                                    <h4 class="text-base sm:text-lg font-black text-slate-900 tracking-tight truncate">{{ $act->action->name ?? '—' }}</h4>
                                    @if($act->ring_count) 
                                        <span class="px-2.5 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-xl border border-indigo-100 shadow-sm">{{ $act->ring_count }} Ring</span> 
                                    @endif
                                    @if($act->is_cito)
                                        <span class="px-3 py-0.5 bg-rose-50 text-rose-700 border border-rose-100 text-[10px] font-black rounded-full uppercase tracking-wider animate-pulse shadow-sm">CITO</span>
                                    @else
                                        <span class="px-3 py-0.5 bg-slate-100 text-slate-600 text-[10px] font-black rounded-full uppercase tracking-wider shadow-sm">Elektif</span>
                                    @endif
                                </div>
                                <p class="text-xs font-bold text-slate-400">
                                    {{ ($act->action_date ?? $act->created_at)?->format('H:i') }} WIB <span class="text-slate-300 mx-1">•</span> Divisi: <span class="text-slate-700 font-extrabold">{{ $act->category->name ?? '—' }}</span> <span class="text-slate-300 mx-1">•</span> Ruang: <span class="text-slate-700 font-extrabold">{{ $act->origin_ward }}</span>
                                </p>
                            </div>
                        </div>

                        <!-- Kolom Tengah: Tim Medis & Diagnosa -->
                        <div class="lg:col-span-4 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-1 gap-3 border-t lg:border-t-0 lg:border-l border-slate-100 pt-4 lg:pt-0 lg:pl-6">
                            <div class="space-y-0.5">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">DPJP & Anestesi</span>
                                <div class="text-xs font-black text-slate-800 leading-snug">{{ $act->doctor->name ?? '—' }}</div>
                                <div class="text-[11px] font-medium text-slate-500">Anestesi: <span class="font-bold text-slate-700">{{ \App\Models\Doctor::find($act->anesthesia_doctor_id)?->name ?? '—' }}</span></div>
                            </div>

                            <div class="space-y-0.5">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Diagnosa Utama</span>
                                <div class="text-xs font-extrabold text-slate-700 truncate">{{ $act->diagnosis_1 }}</div>
                            </div>
                        </div>

                        <!-- Kolom Kanan: Tombol Aksi & Tombol Integrasi Form D2B (Door-to-Balloon) -->
                        <div class="lg:col-span-3 flex flex-wrap items-center justify-end gap-2 pt-4 lg:pt-0 border-t lg:border-t-0 border-slate-100">
                            
                            <!-- INTEGRASI TOMBOL LEMBAR AUDIT DOOR-TO-BALLOON UNTUK KASUS CITO -->
                            @if($act->is_cito)
                                @php
                                    $isD2bCompleted = !empty($act->d2b_igd_time) && !empty($act->d2b_balloon_dilatation_time);
                                @endphp
                                <a href="{{ route('patients.actions.door-to-balloon.edit', [$patient->id, $act->id]) }}" 
                                   class="inline-flex items-center justify-center px-3.5 py-2.5 {{ $isD2bCompleted ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-amber-500 hover:bg-amber-600 animate-pulse' }} text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-md">
                                    <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                    {{ $isD2bCompleted ? 'Audit D2B Selesai' : 'Isi Form D2B' }}
                                </a>
                            @endif

                            <a href="{{ route('patients.actions.show', [$patient->id, $act->id]) }}" class="inline-flex items-center justify-center px-3.5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-md shadow-indigo-600/30 hover:shadow-indigo-600/50">
                                <span>Detail</span>
                                <svg class="w-3.5 h-3.5 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                            </a>
                            
                            <a href="{{ route('patients.actions.edit', [$patient->id, $act->id]) }}" class="inline-flex items-center justify-center px-3.5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                Edit
                            </a>

                            <!-- Tombol Hapus dengan SweetAlert2 -->
                            <form action="{{ route('patients.actions.destroy', [$patient->id, $act->id]) }}" method="POST" class="inline-block delete-action-form">
                                @csrf @method('DELETE')
                                <button type="button" onclick="confirmDeleteAction(this)" class="inline-flex items-center justify-center px-3.5 py-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-black text-xs uppercase tracking-wider rounded-xl transition-all shadow-sm">
                                    Hapus
                                </button>
                            </form>
                        </div>

                    </div>

                </div>
                @empty
                <div class="bg-white rounded-[28px] border border-slate-100 shadow-xl p-16 text-center">
                    <div class="max-w-sm mx-auto space-y-4">
                        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto text-indigo-500 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2v0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                        </div>
                        <h4 class="text-sm font-black text-slate-800">Belum Ada Riwayat Tindakan</h4>
                        <p class="text-xs font-medium text-slate-500">Pasien ini belum memiliki catatan rekam medis prosedur kateterisasi.</p>
                        <a href="{{ route('patients.actions.create', $patient->id) }}" class="inline-block px-5 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-indigo-600/30 transition-all">
                            + Tambah Tindakan Sekarang
                        </a>
                    </div>
                </div>
                @endforelse
            </div>

        </div>
    </div>

    <!-- Script SweetAlert2 khusus untuk Hapus Tindakan -->
    <script>
        function confirmDeleteAction(button) {
            const form = button.closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Catatan tindakan medis ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[28px]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
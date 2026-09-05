<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-2">
                    <span class="px-3 py-1 bg-amber-50 text-amber-700 font-black text-[10px] rounded-full uppercase tracking-widest border border-amber-100">Cathlab Module</span>
                </div>
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Antrean Tindakan Pasien</h2>
                <p class="text-sm text-slate-500 font-medium">Daftar pasien yang bersedia dan siap untuk menjalani prosedur tindakan.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('patients.index') }}" class="inline-flex items-center px-5 py-3 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-widest rounded-2xl transition-all shadow-sm">
                    &larr; Kembali ke Manajemen Pasien
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- TABS NAVIGASI -->
            <div class="flex space-x-1 bg-slate-100 p-1.5 rounded-2xl max-w-4xl shadow-inner border border-slate-200/60">
                <a href="{{ route('patients.index', ['tab' => 'belum_dipanggil']) }}" class="flex-1 text-center py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 transition-all">
                    ⏳ Belum Dipanggil
                </a>

                <a href="{{ route('patients.action-queue') }}" class="flex-1 text-center py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all flex items-center justify-center space-x-1.5 bg-white text-amber-700 shadow-sm border border-slate-200/50">
                    <span>⚡ Antre Tindakan</span>
                    <span class="px-1.5 py-0.5 bg-amber-100 text-amber-800 text-[9px] rounded-full font-black">
                        {{ \App\Models\Patient::where('status', 'bersedia')->count() }}
                    </span>
                </a>
                
                <a href="{{ route('patients.index', ['tab' => 'sudah_dipanggil']) }}" class="flex-1 text-center py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 transition-all">
                    ✅ Selesai
                </a>

                <a href="{{ route('patients.index', ['tab' => 'menolak']) }}" class="flex-1 text-center py-2.5 text-[11px] font-black uppercase tracking-wider rounded-xl text-slate-500 hover:text-slate-700 hover:bg-slate-200/50 transition-all">
                    ❌ Menolak
                </a>
            </div>

            <!-- Search Bar Modern -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-5 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" id="searchInput" placeholder="Cari nama, No. RM, atau nomor telepon pasien di antrean tindakan..." class="w-full pl-14 pr-6 py-4 bg-slate-50 border-none rounded-2xl text-sm font-bold text-slate-800 focus:ring-2 focus:ring-amber-500 transition-all placeholder:text-slate-400">
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden" id="patientTableContainer">
                @include('patients.partials.table')
            </div>
        </div>
    </div>

    <script>
        // PENCARIAN AJAX UNTUK ANTREAN TINDAKAN
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            fetch(`{{ route('patients.action-queue') }}?search=${this.value}`, { headers: {'X-Requested-With': 'XMLHttpRequest'} })
                .then(res => res.text()).then(html => document.getElementById('patientTableContainer').innerHTML = html);
        });
    </script>
</x-app-layout>
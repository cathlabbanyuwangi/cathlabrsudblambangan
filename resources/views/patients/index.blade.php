<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 py-4">
            <div>
                <div class="flex items-center space-x-2.5 mb-2.5">
                    <span class="px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded-full uppercase tracking-widest border border-indigo-100 shadow-2xs">
                        ⚡ Cathlab Module
                    </span>
                </div>
                <h2 class="text-3xl lg:text-4xl font-black text-slate-900 tracking-tight">Manajemen Pasien</h2>
                <p class="text-xs sm:text-sm text-slate-500 font-medium mt-1">Kelola data rekam medis, penjadwalan presisi, dan riwayat tindakan intervensi pasien.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <button onclick="openPublicSheet()" class="relative inline-flex items-center space-x-2.5 px-5 py-3.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-extrabold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-2xs border border-indigo-100 group cursor-pointer">
                    <span>📥 Cek Pendaftaran Publik</span>
                    @php
                        $pendingPublicCount = \App\Models\PublicRegistration::where('status', 'pending')->count();
                    @endphp
                    <span class="px-2 py-0.5 {{ $pendingPublicCount > 0 ? 'bg-rose-500 text-white animate-pulse' : 'bg-indigo-200/70 text-indigo-800' }} text-[10px] rounded-full font-black shadow-2xs">
                        {{ $pendingPublicCount }}
                    </span>
                </button>

                {{-- Tombol Import Baru yang memicu Modal --}}
                <button onclick="openImportModal()" class="inline-flex items-center px-4 py-3.5 bg-white border border-slate-200/80 hover:border-indigo-500 hover:bg-indigo-50 text-indigo-600 font-extrabold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-2xs cursor-pointer">
                    📥 Import Data
                </button>

                <a href="{{ route('patients.export') }}" class="inline-flex items-center px-4 py-3.5 bg-white border border-slate-200/80 hover:border-emerald-500 hover:bg-emerald-50/30 text-emerald-600 font-extrabold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-2xs">📊 Export</a>
                <a href="{{ route('patients.create') }}" class="inline-flex items-center px-6 py-3.5 bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-500 hover:to-violet-500 text-white font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-indigo-600/25 transition-all transform hover:-translate-y-0.5">+ Daftar Pasien Baru</a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/60 min-h-screen text-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            {{-- ========================================== --}}
            {{-- AREA NOTIFIKASI ERROR / SUCCESS --}}
            {{-- ========================================== --}}
            @if (session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl text-xs font-bold shadow-2xs flex items-center justify-between">
                    <span>✅ {{ session('success') }}</span>
                    <button onclick="this.parentElement.style.display='none'" class="text-emerald-500 hover:text-emerald-700 text-lg cursor-pointer">&times;</button>
                </div>
            @endif

            @if (session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200 text-rose-700 rounded-2xl text-xs font-semibold shadow-2xs flex items-center justify-between">
                    <span>❌ {{ session('error') }}</span>
                    <button onclick="this.parentElement.style.display='none'" class="text-rose-500 hover:text-rose-700 text-lg cursor-pointer">&times;</button>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl text-xs shadow-2xs relative">
                    <button onclick="this.parentElement.style.display='none'" class="absolute top-3 right-4 text-amber-500 hover:text-amber-700 text-lg cursor-pointer">&times;</button>
                    <span class="font-bold block mb-1">Perhatian:</span>
                    <ul class="list-disc pl-4 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            {{-- ========================================== --}}

            <div class="flex flex-wrap lg:flex-nowrap space-x-1 bg-slate-200/70 p-1.5 rounded-2xl max-w-4xl shadow-inner border border-slate-300/50 backdrop-blur-sm">
                <a href="{{ route('patients.index', ['tab' => 'belum_dipanggil']) }}" class="flex-1 text-center py-3 px-3 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all {{ (!isset($activeTab) || $activeTab == 'belum_dipanggil') ? 'bg-white text-indigo-700 shadow-md scale-[1.02]' : 'text-slate-600 hover:bg-slate-300/50 hover:text-slate-900' }}">⏳ Belum Dipanggil</a>
                
                <a href="{{ route('patients.action-queue') }}" class="flex items-center justify-center space-x-1.5 flex-1 text-center py-3 px-3 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all {{ (isset($activeTab) && $activeTab == 'antre_tindakan') ? 'bg-white text-amber-700 shadow-md scale-[1.02]' : 'text-slate-600 hover:bg-slate-300/50 hover:text-slate-900' }}">
                    <span>⚡ Antre Tindakan</span>
                    @php
                        $actionQueueCount = \App\Models\Patient::where('status', 'bersedia')->count();
                    @endphp
                    <span class="px-1.5 py-0.2 {{ $actionQueueCount > 0 ? 'bg-amber-500 text-white' : 'bg-slate-300 text-slate-700' }} text-[9px] rounded-full shadow-2xs">
                        {{ $actionQueueCount }}
                    </span>
                </a>

                <a href="{{ route('patients.index', ['tab' => 'sudah_dipanggil']) }}" class="flex-1 text-center py-3 px-3 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all {{ (isset($activeTab) && $activeTab == 'sudah_dipanggil') ? 'bg-white text-emerald-700 shadow-md scale-[1.02]' : 'text-slate-600 hover:bg-slate-300/50 hover:text-slate-900' }}">✅ Selesai</a>
                <a href="{{ route('patients.index', ['tab' => 'menolak']) }}" class="flex-1 text-center py-3 px-3 text-[11px] font-black uppercase tracking-wider rounded-xl transition-all {{ (isset($activeTab) && $activeTab == 'menolak') ? 'bg-white text-rose-700 shadow-md scale-[1.02]' : 'text-slate-600 hover:bg-slate-300/50 hover:text-slate-900' }}">❌ Menolak</a>
            </div>

            <div class="bg-white rounded-[28px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.03)] p-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-5 text-slate-400">🔍</span>
                    <input type="text" id="searchInput" placeholder="Cari nama, No. RM, atau nomor telepon pasien..." class="w-full pl-12 pr-6 py-4 bg-slate-50 border border-slate-200/80 rounded-2xl text-sm font-bold text-slate-800 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all shadow-2xs placeholder:text-slate-400">
                </div>
            </div>

            <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.04)] overflow-hidden" id="patientTableContainer">
                @include('patients.partials.table')
            </div>
        </div>
    </div>

    {{-- MODAL PENGATURAN & SLIDER --}}

    {{-- 1. Public Sheet (Drawer) --}}
    <div id="publicSheetOverlay" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 transition-opacity"></div>
    <div id="publicSheet" class="fixed inset-y-0 right-0 max-w-xl w-full bg-white shadow-2xl z-50 transform translate-x-full transition-transform duration-300 ease-in-out flex flex-col border-l border-slate-200/80">
        <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
            <div>
                <h3 class="font-black text-slate-900 text-lg">Verifikasi Pendaftaran Publik</h3>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Daftar pasien yang mengajukan jadwal mandiri.</p>
            </div>
            <button onclick="closePublicSheet()" class="w-10 h-10 rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center font-black text-xl transition-all shadow-2xs cursor-pointer">&times;</button>
        </div>
        
        <div class="flex-1 overflow-y-auto p-6 space-y-4 bg-slate-50/40">
            @php
                $publicRegistrations = \App\Models\PublicRegistration::with(['insurance'])->where('status', 'pending')->latest()->get();
            @endphp

            @forelse($publicRegistrations as $pub)
                {{-- (Isi dari drawer sama seperti sebelumnya) --}}
                <div class="bg-white border border-slate-200/80 rounded-[28px] p-5 space-y-4 shadow-[0_8px_20px_rgba(0,0,0,0.03)] hover:shadow-md transition-all">
                    <div class="flex justify-between items-start">
                        <div>
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 font-black text-[10px] uppercase rounded-xl border border-indigo-100">{{ ucfirst($pub->source) }}</span>
                            <h4 class="font-black text-slate-900 text-base mt-2">
                                {{ $pub->name }}
                                @if($pub->source == 'rs_lain')
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-50 px-2.5 py-1 rounded-xl ml-2 border border-amber-100">
                                        🏥 {{ $pub->origin_hospital == 'Lainnya' ? $pub->origin_hospital_custom : $pub->origin_hospital }}
                                    </span>
                                @endif
                            </h4>
                        </div>
                        <span class="text-[11px] font-bold text-slate-400 bg-slate-100 px-3 py-1 rounded-xl">{{ $pub->created_at->diffForHumans() }}</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs font-medium text-slate-600 bg-slate-50 p-4 rounded-2xl border border-slate-100/80">
                        <div><span class="text-slate-400 block text-[10px] uppercase font-black">No. Telp / WA</span> <span class="font-bold text-slate-800">{{ $pub->patient_phone }}</span></div>
                        <div><span class="text-slate-400 block text-[10px] uppercase font-black">Penjamin</span> <span class="font-bold text-slate-800">{{ $pub->insurance->name ?? '-' }}</span></div>
                        <div><span class="text-slate-400 block text-[10px] uppercase font-black">Wilayah</span> <span class="font-bold text-slate-800">{{ $pub->district }}, {{ $pub->regency }}</span></div>
                        <div><span class="text-slate-400 block text-[10px] uppercase font-black">No. RM</span> <span class="font-bold text-slate-800">{{ $pub->medical_record_number ?? 'Belum ada' }}</span></div>
                    </div>

                    @if($pub->notes)
                        <p class="text-xs text-slate-600 bg-amber-50/60 p-3.5 rounded-2xl border border-amber-100"><span class="font-bold text-amber-800 uppercase text-[10px] block mb-0.5">Catatan:</span> {{ $pub->notes }}</p>
                    @endif

                    <div class="flex items-center justify-end gap-2.5 pt-3 border-t border-slate-100">
                        <form action="{{ route('public-registrations.destroy', $pub->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menolak?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="px-4 py-2.5 bg-white border border-rose-200 text-rose-600 hover:bg-rose-50 rounded-xl font-extrabold text-[11px] uppercase transition-all shadow-2xs cursor-pointer">Tolak</button>
                        </form>
                        <form action="{{ route('public-registrations.approve', $pub->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl font-extrabold text-[11px] uppercase transition-all shadow-sm cursor-pointer">Setujui & Masukkan Antrean</button>
                        </form>
                    </div>
                </div>
            @empty
                <div class="text-center py-24 space-y-3">
                    <div class="w-16 h-16 bg-white border border-slate-200 rounded-3xl flex items-center justify-center mx-auto text-2xl shadow-sm">📭</div>
                    <h5 class="font-black text-slate-700 text-sm">Tidak Ada Pengajuan</h5>
                    <p class="text-xs text-slate-400">Belum ada pengajuan pendaftaran jadwal mandiri baru dari pasien.</p>
                </div>
            @endforelse
        </div>
    </div>

    {{-- 2. Modal Import (Tengah Layar) --}}
    <div id="importModalOverlay" class="hidden fixed inset-0 bg-slate-900/50 backdrop-blur-sm z-50 transition-opacity"></div>
    <div id="importModal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6">
        {{-- Kontainer Modal dengan animasi pop-in --}}
        <div class="bg-white rounded-[28px] shadow-2xl max-w-md w-full overflow-hidden transform scale-95 opacity-0 transition-all duration-300" id="importModalContent">
            
            <div class="p-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/80">
                <div>
                    <h3 class="font-black text-slate-900 text-lg">Import Data Pasien</h3>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Unduh template lalu unggah data Anda.</p>
                </div>
                <button onclick="closeImportModal()" class="w-9 h-9 rounded-2xl bg-white border border-slate-200 text-slate-400 hover:text-slate-700 hover:bg-slate-100 flex items-center justify-center font-black text-lg transition-all shadow-2xs cursor-pointer">&times;</button>
            </div>

            <div class="p-6 space-y-6 bg-white">
                {{-- Bagian Download Template --}}
                <div class="bg-indigo-50/50 border border-indigo-100 rounded-2xl p-4 flex items-center justify-between">
                    <div>
                        <span class="block text-xs font-bold text-indigo-900">1. Unduh Template</span>
                        <span class="block text-[10px] text-indigo-600 mt-0.5">Pastikan sesuai format standar</span>
                    </div>
                    <a href="{{ route('patients.download-template') }}" class="px-4 py-2 bg-white text-indigo-600 border border-indigo-200 rounded-xl font-extrabold text-[10px] uppercase tracking-wider hover:bg-indigo-600 hover:text-white transition-all shadow-2xs">Unduh File</a>
                </div>
                
                {{-- Bagian Upload --}}
                <form action="{{ route('patients.import') }}" method="POST" enctype="multipart/form-data" class="space-y-4" onsubmit="submitImport(this)">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-700 mb-2">2. Unggah File Excel</label>
                        {{-- Custom File Input UI --}}
                        <input type="file" name="file" required accept=".xlsx, .xls, .csv" 
                            class="block w-full text-sm text-slate-600 
                            file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 
                            file:text-[11px] file:font-extrabold file:bg-emerald-50 file:text-emerald-700 
                            hover:file:bg-emerald-100 file:cursor-pointer file:transition-all 
                            bg-slate-50 border border-slate-200 rounded-2xl cursor-pointer">
                        <p class="text-[10px] text-slate-400 mt-1.5">Format didukung: .xlsx, .xls, .csv (Maks 10MB)</p>
                    </div>

                    <button type="submit" id="uploadBtn" class="w-full py-3.5 mt-2 bg-emerald-600 hover:bg-emerald-700 text-white font-extrabold text-xs uppercase tracking-wider rounded-xl shadow-lg shadow-emerald-600/20 transition-all cursor-pointer">
                        <span id="uploadBtnText">📤 Mulai Import Data</span>
                    </button>
                </form>
            </div>
        </div>
    </div>

    {{-- ==================== SCRIPT ==================== --}}
    <script>
        // Script untuk Public Sheet
        function openPublicSheet() {
            document.getElementById('publicSheetOverlay').classList.remove('hidden');
            document.getElementById('publicSheet').classList.remove('translate-x-full');
        }
        function closePublicSheet() {
            document.getElementById('publicSheet').classList.add('translate-x-full');
            document.getElementById('publicSheetOverlay').classList.add('hidden');
        }
        document.getElementById('publicSheetOverlay').addEventListener('click', closePublicSheet);

        // Script untuk Modal Import
        function openImportModal() {
            const modal = document.getElementById('importModal');
            const overlay = document.getElementById('importModalOverlay');
            const content = document.getElementById('importModalContent');
            
            modal.classList.remove('hidden');
            overlay.classList.remove('hidden');
            
            // Memberikan sedikit delay agar animasi pop-in berjalan mulus
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeImportModal() {
            const modal = document.getElementById('importModal');
            const overlay = document.getElementById('importModalOverlay');
            const content = document.getElementById('importModalContent');
            
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            
            setTimeout(() => {
                modal.classList.add('hidden');
                overlay.classList.add('hidden');
            }, 300); // Sesuaikan dengan durasi animasi di Tailwind (300ms)
        }
        document.getElementById('importModalOverlay').addEventListener('click', closeImportModal);

        // Script UX saat proses Upload berjalan
        function submitImport(form) {
            const btnText = document.getElementById('uploadBtnText');
            const btn = document.getElementById('uploadBtn');
            btnText.innerHTML = '⏳ Memproses...';
            btn.classList.replace('bg-emerald-600', 'bg-slate-400');
            btn.classList.replace('hover:bg-emerald-700', 'bg-slate-400');
            btn.style.pointerEvents = 'none';
        }

        // Script untuk Live Search Tab
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('input', function() {
            const activeTab = "{{ $activeTab ?? 'belum_dipanggil' }}";
            fetch(`{{ route('patients.index') }}?tab=${activeTab}&search=${this.value}`, { headers: {'X-Requested-With': 'XMLHttpRequest'} })
                .then(res => res.text()).then(html => document.getElementById('patientTableContainer').innerHTML = html);
        });
    </script>
</x-app-layout>
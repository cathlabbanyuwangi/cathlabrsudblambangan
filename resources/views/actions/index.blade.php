<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 py-1">
            <div>
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <span class="px-3.5 py-1 bg-indigo-50 text-indigo-700 font-black text-[10px] rounded-full uppercase tracking-widest border border-indigo-100/80 shadow-xs">Modul Cathlab</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400 tracking-wide uppercase">Global Log</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Riwayat Tindakan Global
                </h2>
            </div>
            
            <!-- TOMBOL AKSI: IMPORT (MODAL) & EXPORT -->
            <div class="flex flex-wrap items-center gap-3">
                
                <!-- Tombol Trigger Modal Import -->
                <button onclick="openImportModal()" type="button" class="inline-flex items-center px-4 py-3 bg-white border border-slate-200/80 hover:border-indigo-500 text-indigo-700 font-black text-xs uppercase tracking-widest rounded-2xl transition-all shadow-xs hover:shadow-md hover:shadow-indigo-500/10">
                    📁 Import Excel
                </button>

                <!-- Tombol Export Excel -->
                <a href="{{ route('actions.history.export') }}" class="inline-flex items-center px-4 py-3 bg-white border border-slate-200/80 hover:border-emerald-500 text-emerald-600 font-black text-xs uppercase tracking-widest rounded-2xl transition-all shadow-xs hover:shadow-md hover:shadow-emerald-500/10">
                    📊 Export Excel
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
            
            <!-- LIVE SEARCH BAR -->
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-4">
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-5 text-slate-400">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </span>
                    <input type="text" id="actionSearchInput" placeholder="Cari berdasarkan nama pasien, No. RM, atau nama dokter DPJP..." class="w-full pl-14 pr-6 py-4 bg-slate-50/60 border border-slate-200/60 rounded-2xl text-sm font-bold text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-400">
                </div>
            </div>

            <!-- Table Container -->
            <div class="bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 overflow-hidden" id="tableWrapper">
                @include('actions.partials.table')
            </div>
        </div>
    </div>

    <!-- MODAL IMPORT FILE & DOWNLOAD TEMPLATE -->
    <div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-md flex items-center justify-center p-4">
        <div class="bg-white rounded-3xl max-w-md w-full p-6 sm:p-8 shadow-2xl border border-slate-100 space-y-6 relative transform transition-all">
            <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-indigo-500 bg-indigo-50 px-2.5 py-1 rounded-lg">Unggah Data</span>
                    <h3 class="text-lg font-black text-slate-900 tracking-tight mt-1">Import Riwayat Tindakan</h3>
                </div>
                <button onclick="closeImportModal()" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 flex items-center justify-center font-black transition-colors">&times;</button>
            </div>

            <form action="{{ route('actions.history.import') }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Pilih Berkas Excel (.xlsx, .xls, .csv)</label>
                    <input type="file" name="file" accept=".xlsx, .xls, .csv" class="w-full text-xs text-slate-500 file:mr-4 file:py-3 file:px-4 file:rounded-2xl file:border-0 file:text-xs file:font-black file:uppercase file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer border border-slate-200/80 rounded-2xl bg-slate-50/60" required>
                </div>

                <!-- Pilihan Download Template di Bawahnya -->
                <div class="p-4 bg-slate-50/80 rounded-2xl border border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-black text-slate-800 block">Belum punya formatnya?</span>
                        <span class="text-[11px] text-slate-500 font-medium block">Unduh contoh format file Excel terlebih dahulu.</span>
                    </div>
                    <a href="{{ route('actions.history.template') }}" class="inline-flex items-center px-3.5 py-2 bg-white border border-slate-200/80 hover:border-indigo-300 text-indigo-600 font-black text-xs rounded-xl shadow-xs transition-all shrink-0">
                        📥 Template
                    </a>
                </div>

                <!-- Tombol Aksi Modal -->
                <div class="flex items-center justify-end space-x-3 pt-2">
                    <button type="button" onclick="closeImportModal()" class="px-5 py-3 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs uppercase tracking-widest rounded-2xl transition-all">
                        Batal
                    </button>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-lg shadow-indigo-600/25 transition-all">
                        Upload Sekarang
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT JS UNTUK MODAL & LIVE SEARCH -->
    <script>
        function openImportModal() {
            document.getElementById('importModal').classList.remove('hidden');
        }

        function closeImportModal() {
            document.getElementById('importModal').classList.add('hidden');
        }

        window.onclick = function(event) {
            const modal = document.getElementById('importModal');
            if (event.target === modal) {
                closeImportModal();
            }
        }

        // Live Search Otomatis via AJAX
        const searchInput = document.getElementById('actionSearchInput');
        const tableWrapper = document.getElementById('tableWrapper');

        searchInput.addEventListener('input', function() {
            const keyword = this.value;
            fetch(`{{ route('actions.history.index') }}?search=${keyword}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                tableWrapper.innerHTML = html;
            });
        });
    </script>
</x-app-layout>
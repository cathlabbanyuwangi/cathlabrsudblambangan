<x-app-layout>
    <div class="max-w-5xl mx-auto py-10 px-6">
        <div class="mb-8">
            <h1 class="text-2xl font-black text-slate-900">Filter & Cetak Laporan Kustom</h1>
            <p class="text-xs text-slate-500 mt-1">Pilih rentang bulan, divisi, serta jenis tindakan spesifik yang ingin Anda cetak.</p>
        </div>
        
        <form action="{{ route('reports.selection.print') }}" method="POST" target="_blank" class="space-y-6">
            @csrf

            <!-- 1. RENTANG BULAN -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4">1. Periode / Rentang Bulan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Dari Bulan</label>
                        <input type="month" name="start_month" class="w-full rounded-xl border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 mb-1">Sampai Bulan</label>
                        <input type="month" name="end_month" class="w-full rounded-xl border-slate-200 text-xs focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                </div>
            </div>

            <!-- 2. PILIH DIVISI / KATEGORI -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4">2. Pilih Divisi / Kategori</h2>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-3">
                    @foreach($categories as $category)
                    <label class="cursor-pointer bg-slate-50/60 hover:bg-slate-100/80 p-3 rounded-xl border border-slate-200/80 transition-all flex items-center gap-3">
                        <input type="checkbox" name="divisions[]" value="{{ $category->id }}" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-slate-700">{{ $category->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- 3. PILIH JENIS TINDAKAN -->
            <div class="bg-white p-6 rounded-2xl border border-slate-200 shadow-xs">
                <h2 class="text-sm font-black text-slate-900 uppercase tracking-wider mb-4">3. Pilih Jenis Tindakan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-60 overflow-y-auto p-1 custom-scrollbar">
                    @foreach($actions as $action)
                    <label class="cursor-pointer bg-slate-50/60 hover:bg-slate-100/80 p-3 rounded-xl border border-slate-200/80 transition-all flex items-center gap-3">
                        <input type="checkbox" name="actions[]" value="{{ $action->id }}" class="w-4 h-4 rounded text-indigo-600 focus:ring-indigo-500">
                        <span class="text-xs font-bold text-slate-700">{{ $action->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>

            <!-- TOMBOL AKSI -->
            <div class="flex items-center gap-4">
                <button type="submit" class="px-6 py-3 bg-indigo-600 text-white text-xs font-black rounded-xl hover:bg-indigo-700 transition-all flex items-center gap-2 shadow-md shadow-indigo-600/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Laporan Sesuai Filter
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
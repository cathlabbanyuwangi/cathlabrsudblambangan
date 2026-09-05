<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded-full uppercase tracking-widest border border-indigo-100 shadow-xs">Modul Farmasi & Alkes</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400 tracking-wider uppercase">Bahan Habis Pakai (BHP)</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    BHP per Resep: <span class="text-indigo-600 font-extrabold">{{ $patient->name }}</span>
                </h2>
            </div>
            <div>
                <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex items-center px-5 py-3.5 bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all duration-300">
                    &larr; Detail Pasien
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/60 min-h-screen text-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <div class="relative bg-gradient-to-br from-white via-indigo-50/40 to-white rounded-[36px] p-8 sm:p-10 shadow-[0_20px_50px_rgba(99,102,241,0.08)] overflow-hidden border border-indigo-100/80">
                <div class="absolute -right-20 -top-20 w-72 h-72 bg-indigo-500/10 rounded-full blur-[80px] pointer-events-none"></div>
                <div class="absolute -left-20 -bottom-20 w-72 h-72 bg-blue-500/5 rounded-full blur-[80px] pointer-events-none"></div>
                
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-5 space-y-3">
                        <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded-full uppercase tracking-widest border border-indigo-100 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
                            <span>Otomasi Sistem Cerdas</span>
                        </div>
                        <h3 class="text-2xl font-black tracking-tight text-slate-900">Import Nota BHP / Alkes via PDF</h3>
                        <p class="text-xs text-slate-600 font-medium leading-relaxed">
                            Unggah dokumen PDF nota pembelian obat atau alkes rumah sakit. Sistem cerdas kami akan mengekstrak item, kuantitas, harga satuan, dan subtotal secara instan dan presisi.
                        </p>
                    </div>

                    <div class="lg:col-span-7">
                        <form action="{{ route('patients.bhp.import-pdf', $patient->id) }}" method="POST" enctype="multipart/form-data" class="bg-white/90 backdrop-blur-xl p-4 sm:p-5 rounded-[28px] border border-slate-200/80 shadow-[0_10px_25px_rgba(0,0,0,0.03)] flex flex-col sm:flex-row items-center gap-3">
                            @csrf
                            <div class="w-full relative flex items-center">
                                <input type="file" name="pdf_file" accept=".pdf" class="w-full text-xs text-slate-600 file:mr-4 file:py-3.5 file:px-6 file:rounded-2xl file:border-0 file:text-xs file:font-black file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 cursor-pointer transition-all focus:outline-none shadow-xs" required>
                            </div>
                            <button type="submit" class="w-full sm:w-auto inline-flex items-center justify-center px-7 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-indigo-600/25 transition-all transform hover:-translate-y-0.5 active:translate-y-0 shrink-0">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                Proses PDF
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="flex items-center justify-between px-3">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-xs font-black text-slate-400 uppercase tracking-widest">Rekapitulasi Nota Resep & Alkes</h3>
                        <span class="text-[10px] font-black px-2.5 py-1 bg-slate-200/70 text-slate-700 rounded-lg border border-slate-300/40 shadow-2xs">{{ count($bhpsGrouped ?? []) }} Nota</span>
                    </div>
                    <span class="text-xs font-extrabold text-indigo-600 bg-indigo-50 px-4 py-1.5 rounded-xl border border-indigo-100 shadow-2xs">Berdasarkan Nomor Resep</span>
                </div>

                @forelse($bhpsGrouped ?? [] as $receiptNumber => $bhps)
                @php
                    $firstItem = $bhps->first();
                    $actionDate = $firstItem->action_date ?? now();
                    $totalSubtotal = $bhps->sum('subtotal');
                @endphp

                <div x-data="{ open: true, search: '' }" class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.04)] overflow-hidden transition-all duration-300">
                    
                    <div class="bg-gradient-to-r from-slate-50/80 via-white to-slate-50/50 px-6 sm:px-8 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center space-x-4 cursor-pointer select-none group" @click="open = !open">
                            <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center text-lg font-black shadow-inner border border-indigo-100 group-hover:scale-105 transition-all duration-300">
                                🧾
                            </div>
                            <div>
                                <div class="flex items-center space-x-2">
                                    <h4 class="text-sm font-black text-slate-900 group-hover:text-indigo-600 transition-colors">No. Resep: <span class="font-extrabold text-indigo-600">{{ $receiptNumber }}</span></h4>
                                </div>
                                <p class="text-[11px] font-bold text-slate-400 mt-1 flex items-center gap-2">
                                    <span>📅 {{ \Carbon\Carbon::parse($actionDate)->translatedFormat('d F Y') }}</span>
                                    <span>•</span>
                                    <span class="text-indigo-500 font-semibold">📦 {{ $bhps->count() }} Jenis Item</span>
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between sm:justify-end space-x-4 pt-3 sm:pt-0 border-t sm:border-t-0 border-slate-100">
                            <div class="text-left sm:text-right">
                                <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block">Total Biaya Nota</span>
                                <span class="text-base font-black text-indigo-600">Rp {{ number_format($totalSubtotal, 0, ',', '.') }}</span>
                            </div>

                            <div class="flex items-center space-x-2.5">
                                <form action="{{ route('patients.bhp.destroy', [$patient->id, $receiptNumber]) }}" method="POST" class="inline-block delete-bhp-form">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDeleteBhp(this)" class="w-10 h-10 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-100 flex items-center justify-center transition-all shadow-2xs" title="Hapus Nota Ini">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>

                                <button @click="open = !open" class="w-10 h-10 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 border border-slate-200 flex items-center justify-center transition-transform duration-300" :class="{ 'rotate-180': !open }">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div x-show="open" x-collapse class="p-4 sm:p-6 space-y-4">
                        
                        <div class="flex items-center justify-between gap-3">
                            <div class="relative w-full sm:max-w-md">
                                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 pointer-events-none text-slate-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                                </span>
                                <input type="text" x-model="search" placeholder="Cari nama barang, harga, atau subtotal..." class="w-full text-xs font-bold text-slate-800 bg-slate-50 border border-slate-200 rounded-2xl pl-10 pr-4 py-3 focus:bg-white focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-600 transition-all placeholder:text-slate-400 shadow-2xs">
                            </div>
                            <div class="hidden sm:block text-[11px] font-extrabold text-slate-400">
                                Ketik untuk memfilter item
                            </div>
                        </div>

                        <div class="overflow-x-auto rounded-2xl border border-slate-200/80 bg-white shadow-2xs">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="text-[10px] font-black text-slate-400 uppercase tracking-widest border-b border-slate-200/80 bg-slate-50">
                                        <th class="py-3 px-4">No</th>
                                        <th class="py-3 px-4">Nama Obat / Alkes</th>
                                        <th class="py-3 px-4 text-center">Jumlah</th>
                                        <th class="py-3 px-4 text-right">Harga Satuan</th>
                                        <th class="py-3 px-4 text-right">Subtotal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 text-xs font-bold text-slate-700">
                                    @foreach($bhps as $index => $bhp)
                                    <tr class="hover:bg-slate-50/80 transition-colors group"
                                        x-show="search === '' || 
                                                '{{ strtolower($bhp->item_name) }}'.includes(search.toLowerCase()) || 
                                                '{{ $bhp->unit_price }}'.includes(search) || 
                                                '{{ $bhp->subtotal }}'.includes(search) ||
                                                '{{ strtolower($bhp->unit) }}'.includes(search.toLowerCase())">
                                        <td class="py-4 px-4 text-slate-400 font-semibold">{{ $index + 1 }}</td>
                                        <td class="py-4 px-4 text-slate-900 font-black">
                                            {{ $bhp->item_name }}
                                        </td>
                                        <td class="py-4 px-4 text-center">
                                            <span class="px-3 py-1 bg-slate-100 text-slate-700 rounded-xl font-extrabold inline-block border border-slate-200/60 shadow-2xs">{{ $bhp->quantity }}</span>
                                        </td>
                                        <td class="py-4 px-4 text-right font-semibold text-slate-500">
                                            Rp {{ number_format($bhp->unit_price, 0, ',', '.') }}
                                        </td>
                                        <td class="py-4 px-4 text-right font-black text-indigo-600">
                                            Rp {{ number_format($bhp->subtotal, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                    </div>

                </div>
                @empty
                <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-xl p-16 text-center">
                    <div class="max-w-sm mx-auto space-y-4">
                        <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto text-indigo-600 border border-indigo-100 shadow-inner">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <h4 class="text-sm font-black text-slate-900">Belum Ada Catatan BHP</h4>
                        <p class="text-xs font-medium text-slate-500 leading-relaxed">Pencatatan Bahan Habis Pakai (BHP) belum tersedia. Silakan unggah nota PDF di atas untuk melakukan ekstraksi data otomatis secara instan.</p>
                    </div>
                </div>
                @endforelse

            </div>

        </div>
    </div>

    <script>
        function confirmDeleteBhp(button) {
            const form = button.closest('form');
            Swal.fire({
                title: 'Hapus Nota Resep Ini?',
                text: "Semua rincian item obat/alkes dalam nomor resep ini akan dihapus permanen dari sistem!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[28px] border border-slate-100 shadow-2xl'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
<div class="overflow-x-auto bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40" id="actionTableContainer">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50/80 border-b border-slate-100/80 text-slate-400 uppercase text-[10px] font-black tracking-widest">
            <tr>
                <th class="px-6 py-5">Waktu & Status</th>
                <th class="px-6 py-5">Identitas Pasien</th>
                <th class="px-6 py-5">Jenis Tindakan</th>
                <th class="px-6 py-5">Dokter DPJP</th>
                <th class="px-6 py-5 text-right">Aksi</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/80 text-sm">
            @forelse($records as $record)
            <tr class="hover:bg-indigo-50/30 transition-colors duration-150">
                <td class="px-6 py-4">
                    <div class="font-bold text-slate-800">
                        {{ ($record->action_date ?? $record->created_at)?->format('d M Y, H:i') ?? '-' }}
                    </div>
                    <div class="mt-1.5">
                        @if($record->is_cito)
                            <span class="inline-flex items-center px-2.5 py-0.5 bg-rose-50 text-rose-600 text-[10px] font-black rounded-lg border border-rose-100 shadow-xs animate-pulse">
                                CITO
                            </span>
                        @else
                            <span class="inline-flex items-center px-2.5 py-0.5 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg border border-emerald-100">
                                ELEKTIF
                            </span>
                        @endif
                    </div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-black text-slate-900">{{ $record->patient->name ?? 'Pasien Dihapus' }}</div>
                    <div class="text-xs font-semibold text-indigo-600 mt-0.5">RM: {{ $record->patient->medical_record_number ?? '—' }}</div>
                </td>
                <td class="px-6 py-4">
                    <div class="font-bold text-slate-800">{{ $record->action->name ?? '—' }}</div>
                    <div class="text-xs text-slate-400 font-medium mt-0.5">Asal: <span class="text-slate-600 font-semibold">{{ $record->origin_ward }}</span></div>
                </td>
                <td class="px-6 py-4 font-semibold text-slate-700">
                    <div class="inline-flex items-center px-3 py-1 bg-slate-50 border border-slate-200/60 rounded-xl text-xs text-slate-700">
                        {{ $record->doctor->name ?? '—' }}
                    </div>
                </td>
                <td class="px-6 py-4 text-right">
                    @if($record->patient)
                        <a href="{{ route('patients.actions-history', $record->patient->id) }}" class="inline-flex items-center px-4 py-2 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white font-extrabold text-xs rounded-xl transition-all shadow-xs hover:shadow-md hover:shadow-indigo-600/20">
                            Detail Pasien
                        </a>
                    @else
                        <span class="text-xs text-slate-400 font-medium italic">Data Tidak Tersedia</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="5" class="px-6 py-16 text-center text-slate-400 font-medium">
                    <div class="flex flex-col items-center justify-center space-y-2">
                        <svg class="w-10 h-10 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                        <p class="text-sm">Tidak ada riwayat tindakan yang ditemukan.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Footer -->
    @if($records->hasPages())
        <div class="p-6 border-t border-slate-100 bg-slate-50/60 backdrop-blur-md">
            {{ $records->links() }}
        </div>
    @endif
</div>
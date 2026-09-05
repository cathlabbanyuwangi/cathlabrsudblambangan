<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-3">
            <div>
                <span class="inline-flex items-center px-3.5 py-1.5 bg-sky-50 text-sky-700 font-black text-[10px] rounded-xl uppercase tracking-widest border border-sky-200/80 shadow-2xs">
                    Form Lembar Audit Medik Resmi
                </span>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 mt-1">
                    Door-to-Balloon Time (STEMI)
                </h2>
                <p class="text-xs sm:text-sm text-slate-500 mt-0.5">Pasien: <span class="font-bold text-slate-700">{{ $patient->name }}</span> | No. RM: <span class="font-bold text-slate-700">{{ $patient->medical_record_number }}</span></p>
            </div>
            
            <div class="flex items-center gap-3">
                <a href="{{ route('patients.actions.door-to-balloon.print', [$patient->id, $actionRecord->id]) }}" target="_blank" class="inline-flex items-center px-5 py-3 bg-slate-800 hover:bg-slate-900 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg transition-all">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                    Cetak Lembar PDF
                </a>
                <a href="{{ route('patients.actions-history', $patient->id) }}" class="inline-flex items-center px-5 py-3 bg-white border border-sky-200/80 text-sky-700 hover:bg-sky-50/60 font-black text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all">
                    &larr; Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('patients.actions.door-to-balloon.update', [$patient->id, $actionRecord->id]) }}" method="POST" class="bg-white/90 backdrop-blur-xl p-8 rounded-[32px] border border-sky-100/80 shadow-xl shadow-sky-950/5 space-y-8">
                @csrf
                @method('PUT')

                <!-- INFORMASI UTAMA PASIEN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 p-6 bg-sky-50/50 rounded-2xl border border-sky-100">
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Diagnosa Utama / Keterangan</label>
                        <input type="text" name="diagnosis_d2b" value="{{ old('diagnosis_d2b', $actionRecord->diagnosis_d2b ?? 'STEMI Inferior / Anterior') }}" class="w-full rounded-xl border-slate-200 text-sm font-bold text-slate-900 bg-white">
                    </div>
                </div>

                <!-- TABEL AUDIT 15 TAHAPAN KELINIK -->
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 border border-slate-200 rounded-2xl overflow-hidden">
                        <thead class="bg-sky-900 text-white font-black uppercase text-[10px] tracking-wider">
                            <tr>
                                <th class="p-4 border-b w-3/12">Kegiatan / Tahapan Klinis</th>
                                <th class="p-4 border-b w-3/12">Waktu (Tanggal & Jam)</th>
                                <th class="p-4 border-b w-3/12">Paraf & Nama Petugas</th>
                                <th class="p-4 border-b w-3/12">Keterangan / Hambatan & Masalah</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            @php
                                $steps = [
                                    ['label' => 'Masuk IGD', 'key' => 'igd'],
                                    ['label' => 'Triase', 'key' => 'triage'],
                                    ['label' => 'Jam EKG pertama diperiksa', 'key' => 'ecg'],
                                    ['label' => 'Pengkajian awal medik', 'key' => 'assessment'],
                                    ['label' => 'Diagnosis ditegakkan', 'key' => 'diagnosis_est'],
                                    ['label' => 'Konsulen PPCI diinformasikan', 'key' => 'ppci_consult'],
                                    ['label' => 'Informasi disampaikan ke keluarga/pasien untuk PPCI', 'key' => 'family_info'],
                                    ['label' => 'Keputusan persetujuan pasien/keluarga', 'key' => 'family_approval'],
                                    ['label' => 'Pasien diantar ke cathlab', 'key' => 'to_cathlab'],
                                    ['label' => 'Pasien sampai di cathlab', 'key' => 'arrival_cathlab'],
                                    ['label' => 'Prosedur dimulai', 'key' => 'proc_start'],
                                    ['label' => 'Tindakan lain selama PPCI', 'key' => 'other_action'],
                                    ['label' => 'Balloon dilatasi (Balloon Inflation)', 'key' => 'balloon_dilatation'],
                                    ['label' => 'Tindakan selesai', 'key' => 'proc_finish'],
                                    ['label' => 'Transfer ke ruangan', 'key' => 'room_transfer'],
                                ];
                            @endphp

                            @foreach($steps as $step)
                            <tr class="hover:bg-sky-50/30 transition-colors">
                                <td class="p-3.5 font-bold text-slate-900">{{ $step['label'] }}</td>
                                <td class="p-3.5">
                                    <input type="datetime-local" name="d2b_{{ $step['key'] }}_time" value="{{ old('d2b_' . $step['key'] . '_time', $actionRecord->{'d2b_' . $step['key'] . '_time'} ? \Carbon\Carbon::parse($actionRecord->{'d2b_' . $step['key'] . '_time'})->format('Y-m-d\TH:i') : '') }}" class="w-full text-xs rounded-xl border-slate-200">
                                </td>
                                <td class="p-3.5">
                                    <input type="text" name="d2b_{{ $step['key'] }}_officer" placeholder="Nama / Paraf" value="{{ old('d2b_' . $step['key'] . '_officer', $actionRecord->{'d2b_' . $step['key'] . '_officer'}) }}" class="w-full text-xs rounded-xl border-slate-200">
                                </td>
                                <td class="p-3.5">
                                    <input type="text" name="d2b_{{ $step['key'] }}_notes" placeholder="Hambatan..." value="{{ old('d2b_' . $step['key'] . '_notes', $actionRecord->{'d2b_' . $step['key'] . '_notes'}) }}" class="w-full text-xs rounded-xl border-slate-200">
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- CATATAN AKHIR & VERIFIKASI -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pt-4 border-t border-sky-100">
                    <div>
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">Catatan Umum / Kesimpulan Hambatan</label>
                        <textarea name="d2b_general_notes" rows="4" class="w-full rounded-2xl border-slate-200 text-xs" placeholder="Tulis catatan keseluruhan...">{{ old('d2b_general_notes', $actionRecord->d2b_general_notes) }}</textarea>
                    </div>
                    <div class="space-y-4 bg-slate-50/70 p-6 rounded-2xl border border-slate-200">
                        <h4 class="text-xs font-black text-slate-800 uppercase tracking-wider">Verifikasi Kepala Instalasi / Divisi</h4>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">Nama Lengkap & Gelar:</label>
                            <input type="text" name="d2b_verified_name" value="{{ old('d2b_verified_name', $actionRecord->d2b_verified_name) }}" class="w-full text-xs rounded-xl border-slate-200 bg-white">
                        </div>
                        <div>
                            <label class="block text-[11px] font-bold text-slate-600 mb-1">NIP / ID Pegawai:</label>
                            <input type="text" name="d2b_verified_nip" value="{{ old('d2b_verified_nip', $actionRecord->d2b_verified_nip) }}" class="w-full text-xs rounded-xl border-slate-200 bg-white">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-8 py-4 bg-sky-600 hover:bg-sky-700 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-xl shadow-sky-600/20 transition-all">
                        Simpan Lembar Audit Medik
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
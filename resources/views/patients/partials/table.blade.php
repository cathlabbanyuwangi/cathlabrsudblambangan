<div class="overflow-x-auto bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.03)] p-2">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50/80 text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-100 rounded-2xl">
            <tr>
                <th class="px-6 py-5 rounded-l-2xl">Pasien & Rekam Medis</th>
                <th class="px-6 py-5">Kontak & Alamat</th>
                <th class="px-6 py-5">Asal & Jaminan</th>
                
                {{-- Kolom Estimasi Panggilan --}}
                @if(!isset($activeTab) || $activeTab == 'belum_dipanggil')
                    <th class="px-6 py-5 text-indigo-600 bg-indigo-50/50">⏳ Estimasi Panggilan</th>
                @endif

                <th class="px-6 py-5">Status & Jadwal</th>
                <th class="px-6 py-5 text-right rounded-r-2xl">Kontrol</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/80 text-sm">
            @forelse($patients as $patient)
            <tr class="hover:bg-indigo-50/30 transition-all group relative">
                
                <!-- KOLOM 1: NAMA PASIEN & REKAM MEDIS (TANGGAL KALENDER DI KIRI) -->
                <td class="px-6 py-5 align-middle">
                    <div class="flex items-start gap-4">
                        
                        {{-- TANGGAL DAFTAR (Desain Kalender Modern di Kiri) --}}
                        @if(!isset($activeTab) || $activeTab == 'belum_dipanggil')
                            <div class="shrink-0 mt-0.5">
                                <div class="flex flex-col items-center justify-center px-3 py-1.5 bg-white border border-slate-200 rounded-xl shadow-sm min-w-[64px]">
                                    <span class="text-[8px] font-extrabold text-slate-400 uppercase tracking-widest mb-0.5">Daftar</span>
                                    <span class="text-sm font-black text-slate-800 leading-none mb-1">{{ \Carbon\Carbon::parse($patient->created_at ?? now())->format('d') }}</span>
                                    <span class="text-[9px] font-bold text-slate-500 leading-none">{{ \Carbon\Carbon::parse($patient->created_at ?? now())->format('M Y') }}</span>
                                </div>
                            </div>
                        @endif

                        {{-- INFO PASIEN --}}
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2.5 flex-wrap">
                                <a href="{{ route('patients.show', $patient->id) }}" class="font-black text-slate-900 text-sm hover:text-indigo-600 transition-colors inline-flex items-center gap-1.5 group-hover:no-underline">
                                    {{ $patient->name }}
                                    
                                    @if($patient->gender === 'P')
                                        <svg class="w-3.5 h-3.5 text-pink-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 11a4 4 0 100-8 4 4 0 000 8zm0 2v7m-3-3h6"/>
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l5 5m0 0l-5 5m5-5H10a5 5 0 100 10"/>
                                        </svg>
                                    @endif
                                </a>
                                
                                @if($patient->is_priority && $patient->status !== 'pernah_tindakan')
                                    <span class="px-2 py-1 bg-rose-50 text-rose-600 border border-rose-200 text-[9px] font-black rounded-md uppercase tracking-wider animate-pulse shadow-xs">
                                        🚨 Prioritas
                                    </span>
                                @endif
                            </div>
                            <div class="text-[10px] text-slate-500 font-medium tracking-wide flex items-center gap-2 flex-wrap">
                                <span class="bg-slate-100 px-2 py-0.5 rounded-md border border-slate-200/60">Tiket: <b class="text-indigo-600">{{ $patient->ticket_number ?? '-' }}</b></span>
                                <span>•</span>
                                <span>RM: <b class="text-slate-700">{{ $patient->medical_record_number }}</b></span>
                                <span>•</span>
                                <span>{{ $patient->gender }}</span>
                            </div>
                        </div>
                    </div>
                </td>

                <!-- KOLOM 2: KONTAK & ALAMAT -->
                <td class="px-6 py-5 align-middle">
                    <div class="flex flex-col gap-1.5">
                        <div class="text-xs font-bold text-slate-800">{{ $patient->patient_phone ?? '-' }}</div>
                        <div class="text-[10px] text-slate-500 max-w-[160px] leading-relaxed truncate">{{ $patient->address }}, {{ $patient->district }}</div>
                    </div>
                </td>
                
                <!-- KOLOM 3: ASAL & JAMINAN -->
                <td class="px-6 py-5 align-middle">
                    <div class="flex flex-col items-start gap-2">
                        <span class="px-2.5 py-1 bg-white text-slate-700 text-[10px] font-extrabold rounded-md uppercase border border-slate-200 shadow-sm">
                            {{ $patient->insurance->name ?? 'Umum' }}
                        </span>
                        <div class="text-[9px] text-slate-400 font-bold uppercase tracking-widest flex items-center gap-1">
                            <svg class="w-3 h-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                            {{ $patient->source }}
                        </div>
                    </div>
                </td>

                <!-- KOLOM 4: ESTIMASI PANGGILAN + CEK OVERDUE -->
                @if(!isset($activeTab) || $activeTab == 'belum_dipanggil')
                    @php
                        $daftarDate = $patient->created_at ?? now();
                        $minEst = \Carbon\Carbon::parse($daftarDate)->addDays(30);
                        $maxEst = \Carbon\Carbon::parse($daftarDate)->addDays(45);
                        $isOverdue = now()->isAfter($maxEst);
                    @endphp

                    <td class="px-6 py-5 align-middle relative {{ $isOverdue ? 'bg-rose-50/40' : 'bg-indigo-50/20' }}">
                        {{-- Indikator garis merah jika overdue --}}
                        @if($isOverdue)
                            <div class="absolute left-0 top-0 bottom-0 w-1 bg-rose-500 rounded-r-md"></div>
                        @endif
                        
                        <div class="flex flex-col gap-1.5">
                            <div class="font-extrabold {{ $isOverdue ? 'text-rose-700' : 'text-indigo-800' }} text-xs whitespace-nowrap">
                                {{ $minEst->translatedFormat('d M Y') }} <span class="text-slate-400 font-normal mx-0.5">s/d</span> {{ $maxEst->translatedFormat('d M Y') }}
                            </div>
                            
                            @if($isOverdue)
                                <span class="inline-flex items-center gap-1.5 px-2 py-1 bg-rose-100 border border-rose-200 text-rose-700 text-[9px] font-black rounded uppercase tracking-widest shadow-sm w-fit">
                                    <span class="relative flex h-2 w-2">
                                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-rose-400 opacity-75"></span>
                                      <span class="relative inline-flex rounded-full h-2 w-2 bg-rose-500"></span>
                                    </span>
                                    Lewat Estimasi
                                </span>
                            @else
                                <span class="text-[9px] text-slate-500 font-semibold tracking-wide">Jadwal Perkiraan Sistem</span>
                            @endif
                        </div>
                    </td>
                @endif
                
                <!-- KOLOM 5: STATUS DISPLAY -->
                <td class="px-6 py-5 align-middle">
                    <div class="flex flex-col items-start gap-1.5">
                        @if($patient->status == 'pending')
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 text-[9px] font-black rounded-full border border-amber-200/80 shadow-sm uppercase tracking-wider">Belum Dipanggil</span>
                        
                        @elseif($patient->status == 'bersedia')
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[9px] font-black rounded-full border border-emerald-200/80 shadow-sm uppercase tracking-wider">Bersedia</span>
                            <div class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-100 flex items-center gap-1.5">
                                📅 {{ $patient->scheduled_at ? \Carbon\Carbon::parse($patient->scheduled_at)->format('d M Y, H:i') : '-' }}
                            </div>
                            @if($patient->caller)
                                <div class="text-[9px] text-indigo-500 font-medium tracking-wide">
                                    Oleh: <b class="text-indigo-600">{{ $patient->caller->name }}</b>
                                </div>
                            @endif
                            
                        @elseif($patient->status == 'pernah_tindakan')
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[9px] font-black rounded-full border border-indigo-200/80 shadow-sm uppercase tracking-wider">Selesai</span>
                            @if($patient->action_date)
                                <div class="text-[10px] font-bold text-slate-600 bg-slate-50 px-2 py-0.5 rounded border border-slate-100 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-indigo-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    {{ \Carbon\Carbon::parse($patient->action_date)->format('d M Y') }}
                                </div>
                            @endif
                            
                        @else
                            <span class="px-3 py-1 bg-rose-50 text-rose-700 text-[9px] font-black rounded-full border border-rose-200/80 shadow-sm uppercase tracking-wider">Menolak</span>
                            <div class="text-[10px] text-slate-500 italic max-w-[140px] truncate bg-slate-50 px-2 py-1 rounded border border-slate-100" title="{{ $patient->unwillingness_reason }}">
                                "{{ $patient->unwillingness_reason ?? '-' }}"
                            </div>
                            @if($patient->caller)
                                <div class="text-[9px] text-indigo-500 font-medium tracking-wide">
                                    Oleh: <b class="text-indigo-600">{{ $patient->caller->name }}</b>
                                </div>
                            @endif
                        @endif
                    </div>
                </td>
                
                <!-- KOLOM 6: KONTROL AKSI (FLEX WRAP UNTUK MENCEGAH BERTUMPUK BERANTAKAN) -->
                <td class="px-6 py-5 align-middle text-right">
                    <div class="flex items-center justify-end gap-2 flex-wrap min-w-[180px]">
                        @if($patient->status == 'pending')
                            <button type="button" onclick="openCallModal('{{ $patient->id }}', '{{ addslashes($patient->name) }}')" class="px-4 py-2 bg-amber-500 text-white text-[10px] font-extrabold rounded-xl uppercase hover:bg-amber-600 shadow-md shadow-amber-500/20 transition-all cursor-pointer">
                                Panggil
                            </button>
                        
                        @elseif($patient->status == 'bersedia')
                            <a href="{{ route('patients.actions.create', $patient->id) }}" class="px-4 py-2 bg-indigo-600 text-white text-[10px] font-extrabold rounded-xl hover:bg-indigo-700 uppercase shadow-md shadow-indigo-600/20 transition-all">
                                + Tindakan
                            </a>
                            <button type="button" onclick="openCallModal('{{ $patient->id }}', '{{ addslashes($patient->name) }}')" class="px-3 py-2 bg-white text-slate-700 text-[10px] font-extrabold rounded-xl hover:bg-slate-50 uppercase cursor-pointer border border-slate-200 shadow-sm transition-all">
                                Reschedule
                            </button>
                        
                        @elseif($patient->status == 'pernah_tindakan')
                            <form action="{{ route('patients.reregister', $patient->id) }}" method="POST" class="inline-block reregister-form-{{ $patient->id }} m-0">
                                @csrf
                                <input type="hidden" name="is_priority" id="input-is-priority-{{ $patient->id }}" value="0">
                                <button type="button" onclick="confirmReregister('{{ $patient->id }}', '{{ addslashes($patient->name) }}')" class="px-4 py-2 bg-emerald-600 text-white text-[10px] font-extrabold rounded-xl hover:bg-emerald-700 uppercase shadow-md shadow-emerald-600/20 transition-all cursor-pointer">
                                    Daftar Ulang
                                </button>
                            </form>
                        
                        @else
                            <button type="button" onclick="openCallModal('{{ $patient->id }}', '{{ addslashes($patient->name) }}')" class="px-4 py-2 bg-amber-500 text-white text-[10px] font-extrabold rounded-xl hover:bg-amber-600 uppercase shadow-md shadow-amber-500/20 transition-all cursor-pointer">
                                Panggil Ulang
                            </button>
                        @endif
                        
                        <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="inline-block delete-patient-form m-0">
                            @csrf @method('DELETE')
                            <button type="button" onclick="confirmDeletePatient(this)" class="p-2 bg-white text-rose-500 hover:text-rose-700 hover:bg-rose-50 rounded-xl cursor-pointer border border-slate-200 shadow-sm transition-all" title="Hapus Pasien">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            </button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ (!isset($activeTab) || $activeTab == 'belum_dipanggil') ? 6 : 5 }}" class="px-6 py-16 text-center">
                    <div class="flex flex-col items-center justify-center space-y-3">
                        <div class="w-16 h-16 bg-slate-50 border border-slate-100 rounded-3xl flex items-center justify-center text-3xl shadow-sm">📭</div>
                        <h5 class="font-black text-slate-600 text-sm">Tidak ada data pasien</h5>
                        <p class="text-xs text-slate-400 max-w-sm mx-auto">Data pasien pada kategori ini kosong atau tidak ditemukan dalam pencarian.</p>
                    </div>
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>

<!-- Script Modal Panggil Interaktif -->
<script>
    function openCallModal(patientId, patientName) {
        let userOptions = `<option value="">-- Pilih User --</option>`;
        @foreach($users ?? [] as $u)
            userOptions += `<option value="{{ $u->id }}" {{ (auth()->id() == $u->id) ? 'selected' : '' }}>{{ $u->name }}</option>`;
        @endforeach

        Swal.fire({
            title: 'Panggil Pasien',
            html: `
                <div class="text-xs text-slate-600 mb-4 text-center">
                    Konfirmasi panggilan untuk pasien <b class="text-slate-900">${patientName}</b>:
                </div>
                <form id="call-form-${patientId}" action="/patients/${patientId}/call" method="POST" class="text-left space-y-4">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}'}">
                    
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Status Konfirmasi *</label>
                        <select id="swal-call-status" name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500 shadow-sm cursor-pointer transition-all" onchange="toggleCallInput(this.value)">
                            <option value="bersedia">Bersedia (Masuk Antre Tindakan)</option>
                            <option value="menolak">Menolak (Masuk Tab Menolak)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">User yang Memanggil *</label>
                        <select name="called_by" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500 shadow-sm cursor-pointer transition-all">
                            ${userOptions}
                        </select>
                    </div>

                    <div id="swal-wrap-bersedia" class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tanggal & Jam Tindakan *</label>
                        <input type="datetime-local" id="swal-scheduled-at" name="scheduled_at" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500 shadow-sm transition-all">
                    </div>

                    <div id="swal-wrap-menolak" class="space-y-1.5 hidden">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Alasan Penolakan *</label>
                        <textarea id="swal-reason" name="unwillingness_reason" rows="3" placeholder="Tuliskan alasan pasien menolak..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500 shadow-sm transition-all"></textarea>
                    </div>
                </form>
            `,
            showCancelButton: true,
            confirmButtonText: 'Simpan & Proses',
            cancelButtonText: 'Batal',
            confirmButtonColor: '#6366f1',
            cancelButtonColor: '#64748b',
            reverseButtons: true,
            customClass: {
                popup: 'rounded-[28px]'
            },
            didOpen: () => {
                window.toggleCallInput = function(val) {
                    const bersediaWrap = document.getElementById('swal-wrap-bersedia');
                    const menolakWrap = document.getElementById('swal-wrap-menolak');
                    if (val === 'bersedia') {
                        bersediaWrap.classList.remove('hidden');
                        menolakWrap.classList.add('hidden');
                    } else {
                        bersediaWrap.classList.add('hidden');
                        menolakWrap.classList.remove('hidden');
                    }
                }
            },
            preConfirm: () => {
                const status = document.getElementById('swal-call-status').value;
                if (status === 'bersedia') {
                    const scheduledAt = document.getElementById('swal-scheduled-at').value;
                    if (!scheduledAt) {
                        Swal.showValidationMessage('Mohon pilih tanggal dan jam tindakan terlebih dahulu!');
                        return false;
                    }
                } else {
                    const reason = document.getElementById('swal-reason').value;
                    if (!reason.trim()) {
                        Swal.showValidationMessage('Mohon isi alasan penolakan terlebih dahulu!');
                        return false;
                    }
                }
                return true;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const status = document.getElementById('swal-call-status').value;
                const form = document.getElementById(`call-form-${patientId}`);
                
                form.action = `/patients/${patientId}/call?tab=${status === 'bersedia' ? 'antre_tindakan' : 'menolak'}`;
                form.submit();
            }
        });
    }

    if (typeof confirmReregister !== 'function') {
        function confirmReregister(patientId, patientName) {
            Swal.fire({
                title: 'Konfirmasi Daftar Ulang',
                html: `
                    <div class="text-xs text-slate-600 mb-4 text-center">
                        Apakah Anda yakin ingin mendaftarkan ulang pasien <b class="text-slate-900">${patientName}</b> kembali ke antrean?
                    </div>
                    <div class="p-4 bg-slate-50 rounded-2xl border border-slate-200 flex items-center justify-between text-left">
                        <div>
                            <div class="text-xs font-bold text-slate-900">Pasien Prioritas / Diutamakan</div>
                            <div class="text-[10px] text-slate-500">Aktifkan jika pasien darurat/segera.</div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                          <input type="checkbox" id="swal-is-priority" value="1" class="sr-only peer">
                          <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-600"></div>
                        </label>
                    </div>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#059669',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Daftar Ulang!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[28px]'
                },
                preConfirm: () => {
                    return document.getElementById('swal-is-priority').checked ? 1 : 0;
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    const isPriorityVal = result.value;
                    const form = document.querySelector(`.reregister-form-${patientId}`);
                    const inputPriority = document.getElementById(`input-is-priority-${patientId}`);
                    
                    if (inputPriority) {
                        inputPriority.value = isPriorityVal;
                    }
                    if (form) form.submit();
                }
            });
        }
    }

    if (typeof confirmDeletePatient !== 'function') {
        function confirmDeletePatient(button) {
            const form = button.closest('form');
            Swal.fire({
                title: 'Hapus Pasien?',
                text: "Data pasien beserta seluruh riwayat terkait akan dihapus secara permanen!",
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
    }
</script>
<div class="overflow-x-auto bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.03)] p-2">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50/80 text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-100 rounded-2xl">
            <tr>
                <th class="px-6 py-5 rounded-l-2xl">Pasien & Rekam Medis</th>
                <th class="px-6 py-5">Kontak & Alamat</th>
                <th class="px-6 py-5">Asal & Jaminan</th>
                
                {{-- Kolom Estimasi Panggilan (Hanya muncul di tab Belum Dipanggil) --}}
                @if(!isset($activeTab) || $activeTab == 'belum_dipanggil')
                    <th class="px-6 py-5 text-indigo-600 bg-indigo-50/50">⏳ Estimasi Panggilan</th>
                @endif

                <th class="px-6 py-5">Status & Jadwal</th>
                <th class="px-6 py-5 text-right rounded-r-2xl">Kontrol</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100/80 text-sm">
            @forelse($patients as $patient)
            <tr class="hover:bg-indigo-50/30 transition-all group">
                
                <!-- KOLOM NAMA PASIEN BISA DIKLIK + BADGE PRIORITAS -->
                <td class="px-6 py-5">
                    <div class="flex items-center space-x-2">
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
                            <span class="px-2 py-0.5 bg-rose-600 text-white text-[9px] font-black rounded-md uppercase tracking-wider animate-pulse shadow-xs">
                                PRIORITAS
                            </span>
                        @endif
                    </div>
                    <div class="text-[10px] text-slate-400 font-bold tracking-wide mt-0.5">
                        Tiket: <span class="text-indigo-600 font-black">{{ $patient->ticket_number ?? '-' }}</span> • RM: {{ $patient->medical_record_number }} • {{ $patient->gender }}
                    </div>
                </td>

                <td class="px-6 py-5">
                    <div class="text-xs font-bold text-slate-700">{{ $patient->patient_phone ?? '-' }}</div>
                    <div class="text-[10px] text-slate-500 mt-1 max-w-[150px] truncate">{{ $patient->address }}, {{ $patient->district }}</div>
                </td>
                
                <td class="px-6 py-5">
                    <span class="px-2.5 py-1 bg-slate-100/80 text-slate-700 text-[10px] font-black rounded-lg uppercase border border-slate-200/50 shadow-2xs">{{ $patient->insurance->name ?? 'Umum' }}</span>
                    <div class="text-[10px] text-slate-400 font-bold mt-1.5 uppercase">{{ $patient->source }}</div>
                </td>

                {{-- ISI ESTIMASI PANGGILAN (1 - 1,5 Bulan setelah Tanggal Daftar) --}}
                @if(!isset($activeTab) || $activeTab == 'belum_dipanggil')
                    <td class="px-6 py-5 bg-indigo-50/20">
                        @php
                            $daftarDate = $patient->created_at ?? now();
                            $minEst = \Carbon\Carbon::parse($daftarDate)->addDays(30)->translatedFormat('d M Y');
                            $maxEst = \Carbon\Carbon::parse($daftarDate)->addDays(45)->translatedFormat('d M Y');
                        @endphp
                        <div class="font-extrabold text-indigo-900 text-xs">
                            {{ $minEst }} - {{ $maxEst }}
                        </div>
                        <span class="text-[9px] text-indigo-500 font-bold uppercase tracking-wider mt-0.5 block">Perkiraan Tanggal di Jadwalkan</span>
                    </td>
                @endif
                
                <!-- STATUS DISPLAY -->
                <td class="px-6 py-5">
                    @if($patient->status == 'pending')
                        <span class="px-3 py-1 bg-amber-50 text-amber-700 text-[10px] font-black rounded-full border border-amber-200/80 shadow-2xs">BELUM DIPANGGIL</span>
                    @elseif($patient->status == 'bersedia')
                        <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[10px] font-black rounded-full border border-emerald-200/80 shadow-2xs">BERSEDIA</span>
                        <div class="text-[10px] font-bold text-slate-500 mt-1.5">
                            📅 {{ $patient->scheduled_at ? \Carbon\Carbon::parse($patient->scheduled_at)->format('d M Y, H:i') : '-' }}
                        </div>
                        @if($patient->caller)
                            <div class="text-[9px] text-indigo-600 font-semibold mt-0.5">
                                👤 Oleh: {{ $patient->caller->name }}
                            </div>
                        @endif
                    @elseif($patient->status == 'pernah_tindakan')
                        <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded-full border border-indigo-200/80 shadow-2xs">SELESAI</span>
                        @if($patient->action_date)
                            <div class="text-[10px] font-bold text-slate-600 mt-1.5 flex items-center">
                                <svg class="w-3 h-3 mr-1 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                Tindakan: {{ \Carbon\Carbon::parse($patient->action_date)->format('d M Y') }}
                            </div>
                        @endif
                    @else
                        <span class="px-3 py-1 bg-rose-50 text-rose-700 text-[10px] font-black rounded-full border border-rose-200/80 shadow-2xs">MENOLAK</span>
                        <div class="text-[10px] text-slate-400 mt-1 italic max-w-[120px] truncate" title="{{ $patient->unwillingness_reason }}">"{{ $patient->unwillingness_reason ?? '-' }}"</div>
                        @if($patient->caller)
                            <div class="text-[9px] text-indigo-600 font-semibold mt-0.5">
                                👤 Oleh: {{ $patient->caller->name }}
                            </div>
                        @endif
                    @endif
                </td>
                
                <!-- KONTROL AKSI (TOMBOL CLAYMORPHISM) -->
                <td class="px-6 py-5 text-right space-x-1.5 whitespace-nowrap">
                    @if($patient->status == 'pending')
                        <button type="button" onclick="openCallModal('{{ $patient->id }}', '{{ addslashes($patient->name) }}')" class="px-3.5 py-2 bg-amber-500 text-white text-[10px] font-black rounded-xl uppercase hover:bg-amber-600 shadow-[0_4px_12px_rgba(245,158,11,0.3)] transition-all cursor-pointer">Panggil</button>
                    
                    @elseif($patient->status == 'bersedia')
                        <a href="{{ route('patients.actions.create', $patient->id) }}" class="px-3.5 py-2 bg-indigo-600 text-white text-[10px] font-black rounded-xl hover:bg-indigo-500 uppercase inline-block shadow-[0_4px_12px_rgba(99,102,241,0.3)] transition-all">+ Tindakan</a>
                        <button type="button" onclick="openCallModal('{{ $patient->id }}', '{{ addslashes($patient->name) }}')" class="px-3.5 py-2 bg-slate-100 text-slate-700 text-[10px] font-black rounded-xl hover:bg-slate-200 uppercase cursor-pointer border border-slate-200/60 shadow-2xs transition-all">Jadwal Ulang</button>
                    
                    @elseif($patient->status == 'pernah_tindakan')
                        <form action="{{ route('patients.reregister', $patient->id) }}" method="POST" class="inline-block reregister-form-{{ $patient->id }}">
                            @csrf
                            <input type="hidden" name="is_priority" id="input-is-priority-{{ $patient->id }}" value="0">
                            <button type="button" onclick="confirmReregister('{{ $patient->id }}', '{{ addslashes($patient->name) }}')" class="px-3.5 py-2 bg-emerald-600 text-white text-[10px] font-black rounded-xl hover:bg-emerald-500 uppercase shadow-[0_4px_12px_rgba(16,185,129,0.3)] transition-all cursor-pointer">Daftar Ulang</button>
                        </form>
                    
                    @else
                        <button type="button" onclick="openCallModal('{{ $patient->id }}', '{{ addslashes($patient->name) }}')" class="px-3.5 py-2 bg-amber-500 text-white text-[10px] font-black rounded-xl hover:bg-amber-600 uppercase shadow-[0_4px_12px_rgba(245,158,11,0.3)] transition-all cursor-pointer">Panggil Kembali</button>
                    @endif
                    
                    <form action="{{ route('patients.destroy', $patient->id) }}" method="POST" class="inline-block delete-patient-form">
                        @csrf @method('DELETE')
                        <button type="button" onclick="confirmDeletePatient(this)" class="px-3.5 py-2 bg-rose-50 text-rose-600 text-[10px] font-black rounded-xl hover:bg-rose-100 uppercase cursor-pointer border border-rose-200/60 shadow-2xs transition-all">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="{{ (!isset($activeTab) || $activeTab == 'belum_dipanggil') ? 6 : 5 }}" class="px-6 py-16 text-center text-slate-400 font-medium">Tidak ada data pasien yang ditemukan.</td>
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
                        <select id="swal-call-status" name="status" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500 shadow-2xs cursor-pointer" onchange="toggleCallInput(this.value)">
                            <option value="bersedia">Bersedia (Masuk Antre Tindakan)</option>
                            <option value="menolak">Menolak (Masuk Tab Menolak)</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">User yang Memanggil *</label>
                        <select name="called_by" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500 shadow-2xs cursor-pointer">
                            ${userOptions}
                        </select>
                    </div>

                    <div id="swal-wrap-bersedia" class="space-y-1.5">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Tanggal & Jam Tindakan *</label>
                        <input type="datetime-local" id="swal-scheduled-at" name="scheduled_at" class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500 shadow-2xs">
                    </div>

                    <div id="swal-wrap-menolak" class="space-y-1.5 hidden">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-1.5">Alasan Penolakan *</label>
                        <textarea id="swal-reason" name="unwillingness_reason" rows="3" placeholder="Tuliskan alasan pasien menolak..." class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-2xl text-sm font-bold text-slate-800 focus:outline-none focus:border-indigo-500 shadow-2xs"></textarea>
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
                            <div class="text-[10px] text-slate-500">Aktifkan jika pasien memerlukan penanganan darurat/segera.</div>
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
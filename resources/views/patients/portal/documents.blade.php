<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pasien - RSUD Blambangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .clay-card {
            background: #ffffff;
            border-radius: 28px;
            box-shadow: 0 20px 40px -15px rgba(15, 23, 42, 0.06),
                        0 0 0 1px rgba(255, 255, 255, 0.9) inset;
        }
        .clay-button {
            box-shadow: 0 10px 20px -5px rgba(14, 165, 233, 0.35);
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .clay-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 24px -5px rgba(14, 165, 233, 0.45);
        }
        .accordion-content {
            transition: max-height 0.3s ease-in-out, opacity 0.3s ease-in-out;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-50 via-sky-50/30 to-blue-50/50 min-h-screen py-10 px-4 sm:px-6 font-sans text-slate-700">

    <div class="max-w-5xl mx-auto space-y-8">
        
        <!-- Header Profil Pasien -->
        <div class="clay-card p-6 sm:p-8 flex flex-col md:flex-row md:items-center md:justify-between gap-6 relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-48 h-48 bg-sky-100/50 rounded-full blur-2xl pointer-events-none"></div>
            
            <div class="flex items-center gap-5 relative z-10">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-sky-600 to-cyan-500 flex items-center justify-center text-white text-2xl font-black shadow-lg shadow-sky-500/30 shrink-0">
                    🏥
                </div>
                <div class="space-y-1">
                    <span class="px-3 py-1 bg-sky-50 text-sky-700 font-extrabold text-[10px] rounded-full uppercase tracking-wider border border-sky-100 shadow-2xs">
                        Pasien Terverifikasi RSUD Blambangan
                    </span>
                    <h2 class="text-2xl sm:text-3xl font-black text-slate-900 tracking-tight">{{ session('patient_name') }}</h2>
                    <p class="text-xs font-bold text-slate-400 tracking-wide uppercase">No. Rekam Medis: <span class="text-sky-600 font-black tracking-widest">{{ session('patient_rm') }}</span></p>
                </div>
            </div>
            
            <div class="flex items-center gap-3 relative z-10">
                <form action="{{ route('patient.portal.logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-5 py-3 bg-rose-50 hover:bg-rose-100 text-rose-600 font-black text-xs uppercase tracking-wider rounded-2xl transition-all border border-rose-100/60 cursor-pointer">
                        Keluar Sesi
                    </button>
                </form>
            </div>
        </div>

        @if(session('error'))
        <div class="bg-red-50 text-red-600 p-5 rounded-2xl text-xs font-bold border border-red-100 shadow-sm flex items-center gap-3">
            <svg class="w-5 h-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
            <span>{{ session('error') }}</span>
        </div>
        @endif

        <div class="flex items-center justify-between px-2">
            <div>
                <h3 class="text-base font-black text-slate-900 tracking-tight">Riwayat Tindakan & Wadah Berkas Medis</h3>
                <p class="text-xs text-slate-400 font-bold">Setiap tindakan medis bertindak sebagai jangkar yang mewadahi dokumen dan hasil pemeriksaan terkait.</p>
            </div>
        </div>

        <!-- CONTAINER UTAMA: SETIAP JANGKAR MEMILIKI FORM DAN TOMBOL DOWNLOAD DI ATAS -->
        <div class="space-y-6">
            @forelse($patient->actionRecords as $index => $act)
            @php
                $actionDateStr = \Carbon\Carbon::parse($act->action_date ?? $act->created_at)->format('Y-m-d');
                $formattedDate = \Carbon\Carbon::parse($act->action_date ?? $act->created_at)->translatedFormat('d F Y H:i');
                $anchorId = 'anchor-' . $act->id;

                // Cari dokumen berdasarkan tanggal dokumen (document_date)
                $relatedDocs = $patient->documents
                    ->filter(function($doc) use ($actionDateStr) {
                        if (empty($doc->document_date)) {
                            return false;
                        }
                        return \Carbon\Carbon::parse($doc->document_date)->format('Y-m-d') === $actionDateStr;
                    })
                    ->sortByDesc(function($doc) {
                        return \Carbon\Carbon::parse($doc->document_date)->timestamp;
                    })
                    ->values();
            @endphp

            <!-- Form Mandiri Per Jangkar Tanggal -->
            <form action="{{ route('patient.portal.download') }}" method="POST" class="clay-card overflow-hidden border border-white transition-all">
                @csrf

                <!-- HEADER JANGKAR (TINDAKAN MEDIS UTAMA & TOMBOL DOWNLOAD DI ATAS) -->
                <div class="p-6 bg-white border-b border-slate-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-start gap-4">
                        <!-- Checkbox Jangkar Utama (Tindakan) -->
                        <input type="checkbox" name="selected_actions[]" value="{{ $act->id }}" data-anchor-group="{{ $act->id }}" onchange="toggleAnchorGroup('{{ $act->id }}', this.checked)" class="w-5 h-5 text-sky-600 rounded-lg border-slate-300 focus:ring-sky-500 cursor-pointer mt-1" title="Pilih tindakan dan wadah dokumen ini" checked>
                        
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="px-3 py-0.5 bg-sky-50 text-sky-700 font-extrabold text-[10px] rounded-md uppercase tracking-wider border border-sky-100">Jangkar Tindakan</span>
                                <span class="text-xs font-bold text-slate-400">📅 {{ $formattedDate }} WIB</span>
                            </div>
                            <h4 class="text-lg font-black text-slate-900">{{ $act->action->name ?? 'Tindakan Medis' }}</h4>
                            <p class="text-xs font-semibold text-slate-600"><strong class="text-slate-800">Diagnosa/Kesimpulan:</strong> {{ $act->conclusion ?? '-' }}</p>
                            <p class="text-xs font-bold text-sky-700">👨‍⚕️ Dokter DPJP: {{ $act->doctor->name ?? '-' }}</p>
                        </div>
                    </div>

                    <!-- Tombol Download & Toggle Wadah di Header (Atas) -->
                    <div class="flex items-center gap-2.5 self-end md:self-center flex-wrap">
                        <span class="px-3 py-2 bg-slate-100 text-slate-700 font-black text-xs rounded-xl">
                            📁 {{ count($relatedDocs) }} Berkas
                        </span>

                        <!-- Tombol Download Sesi Ini di Atas -->
                        <button type="submit" class="clay-button px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-black text-xs uppercase tracking-wider rounded-xl transition-all cursor-pointer flex items-center gap-1.5">
                            <span>📥 Download Sesi</span>
                        </button>

                        <button type="button" onclick="toggleAccordion('{{ $anchorId }}')" class="px-3.5 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs rounded-xl transition-all cursor-pointer flex items-center gap-1.5">
                            <span>Lihat Berkas</span>
                            <span id="icon-{{ $anchorId }}" class="transition-transform duration-300 inline-block">▼</span>
                        </button>
                    </div>
                </div>

                <!-- KONTEN WADAH: DOKUMEN DALAM KOLAPS -->
                <div id="{{ $anchorId }}" class="accordion-content max-h-0 overflow-hidden bg-slate-50/50">
                    <div class="p-6 space-y-4">
                        <h5 class="text-xs font-black text-slate-500 uppercase tracking-wider flex items-center gap-2">
                            <span>📂 Dokumen & Hasil Pemeriksaan dalam Wadah Ini</span>
                        </h5>

                        @if($relatedDocs->count() > 0)
                        <div class="bg-white rounded-2xl border border-slate-100 overflow-hidden shadow-2xs">
                            <table class="w-full text-left text-sm text-slate-600">
                                <thead class="bg-slate-100/60 text-[10px] font-black text-slate-700 uppercase">
                                    <tr>
                                        <th class="p-3.5 text-center w-12">Pilih</th>
                                        <th class="p-3.5">Waktu Dokumen</th>
                                        <th class="p-3.5">Nama / Judul Dokumen</th>
                                        <th class="p-3.5">Format</th>
                                        <th class="p-3.5 text-right">Aksi Cepat</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @foreach($relatedDocs as $doc)
                                    @php
                                        $fileUrl = URL::temporarySignedRoute('patient.portal.secure-preview', now()->addMinutes(15), ['patientDocument' => $doc->id]);
                                        $fileTypeLower = strtolower($doc->file_type ?? $doc->extension ?? '');
                                    @endphp
                                    <tr class="hover:bg-sky-50/30">
                                        <td class="p-3.5 text-center">
                                            <input type="checkbox" name="selected_documents[]" value="{{ $doc->id }}" class="anchor-child-{{ $act->id }} w-4 h-4 text-sky-600 rounded border-slate-300 focus:ring-sky-500 cursor-pointer" checked>
                                        </td>
                                        <td class="p-3.5 font-bold text-slate-800 text-xs whitespace-nowrap">
                                            {{ $doc->document_date ? \Carbon\Carbon::parse($doc->document_date)->format('H:i') : '-' }} WIB
                                        </td>
                                        <td class="p-3.5 font-black text-slate-900 text-xs">
                                            {{ $doc->document_name }}
                                        </td>
                                        <td class="p-3.5 text-xs font-bold text-slate-700 uppercase">
                                            <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-[10px]">{{ $doc->file_type }}</span>
                                        </td>
                                        <td class="p-3.5 text-right space-x-2">
                                            <button type="button" 
                                                onclick="openPreview('{{ $fileUrl }}', '{{ $fileTypeLower }}', '{{ addslashes($doc->document_name) }}')"
                                                class="inline-flex items-center px-3 py-1.5 bg-slate-800 hover:bg-slate-900 text-white font-black text-[10px] uppercase rounded-xl transition-all cursor-pointer">
                                                👁️ Preview
                                            </button>
                                            <a href="{{ URL::temporarySignedRoute('patient.portal.secure-download', now()->addMinutes(15), ['patientDocument' => $doc->id]) }}" 
                                               class="inline-flex items-center px-3 py-1.5 bg-sky-600 hover:bg-sky-700 text-white font-black text-[10px] uppercase rounded-xl transition-all">
                                                📥 Download
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="p-6 bg-white rounded-2xl border border-slate-100 text-center text-slate-400 italic text-xs">
                            Belum ada berkas atau dokumen yang diunggah untuk wadah tindakan ini.
                        </div>
                        @endif
                    </div>
                </div>

            </form>
            @empty
            <div class="clay-card p-12 text-center text-slate-400 italic text-xs">
                Belum ada riwayat tindakan medis atau jangkar data yang tersedia.
            </div>
            @endforelse
        </div>

    </div>

    <!-- MODAL PREVIEW BERKAS -->
    <div id="previewModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-sm p-4 hidden">
        <div class="clay-card w-full max-w-4xl max-h-[92vh] flex flex-col overflow-hidden border border-white animate-in fade-in zoom-in duration-200 shadow-2xl">
            <div class="px-6 py-4 bg-white border-b border-slate-100 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-sky-600">Pratinjau Berkas Medis Aman</span>
                    <h3 id="previewTitle" class="text-sm font-black text-slate-900 truncate max-w-xl">Judul Dokumen</h3>
                </div>
                <button type="button" onclick="closePreview()" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold flex items-center justify-center transition-all cursor-pointer">
                    ✕
                </button>
            </div>

            <div class="p-4 bg-slate-950 flex-1 flex items-center justify-center overflow-auto min-h-[480px]" id="previewContainer"></div>

            <div class="px-6 py-4 bg-white border-t border-slate-100 flex justify-end">
                <button type="button" onclick="closePreview()" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-black text-xs uppercase tracking-wider rounded-xl transition-all cursor-pointer">
                    Tutup Pratinjau
                </button>
            </div>
        </div>
    </div>

    <!-- SCRIPT INTERAKSI JANGKAR & WADAH -->
    <script>
        // Buka/Tutup Accordion Wadah Dokumen di dalam Jangkar
        function toggleAccordion(id) {
            const content = document.getElementById(id);
            const icon = document.getElementById('icon-' + id);
            
            if (content.style.maxHeight && content.style.maxHeight !== '0px') {
                content.style.maxHeight = '0px';
                icon.style.transform = 'rotate(0deg)';
            } else {
                content.style.maxHeight = content.scrollHeight + 'px';
                icon.style.transform = 'rotate(180deg)';
            }
        }

        // Jika jangkar (tindakan) dicentang, otomatis mencentang semua dokumen di dalam wadahnya
        function toggleAnchorGroup(actionId, isChecked) {
            const childCheckboxes = document.querySelectorAll(`.anchor-child-${actionId}`);
            childCheckboxes.forEach(cb => cb.checked = isChecked);
        }

        // Fungsi Modal Preview
        function openPreview(url, fileType, title) {
            const modal = document.getElementById('previewModal');
            const titleEl = document.getElementById('previewTitle');
            const container = document.getElementById('previewContainer');

            titleEl.textContent = title;
            container.innerHTML = ''; 

            let content = '';
            if (fileType.includes('image') || fileType.includes('jpg') || fileType.includes('jpeg') || fileType.includes('png')) {
                content = `<img src="${url}" alt="${title}" class="max-h-[72vh] max-w-full object-contain rounded-2xl shadow-2xl">`;
            } else if (fileType.includes('video') || fileType.includes('mp4')) {
                content = `<video src="${url}" controls autoplay class="max-h-[72vh] max-w-full rounded-2xl shadow-2xl"></video>`;
            } else {
                content = `<iframe src="${url}" class="w-full h-[72vh] rounded-2xl bg-white border-0 shadow-inner"></iframe>`;
            }

            container.innerHTML = content;
            modal.classList.remove('hidden');
        }

        function closePreview() {
            const modal = document.getElementById('previewModal');
            const container = document.getElementById('previewContainer');
            modal.classList.add('hidden');
            container.innerHTML = ''; 
        }

        document.getElementById('previewModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closePreview();
            }
        });
    </script>

</body>
</html>
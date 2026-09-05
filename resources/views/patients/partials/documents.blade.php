<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-4">
            <div>
                <div class="flex items-center space-x-3 mb-2">
                    <span class="px-4 py-1.5 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded-full uppercase tracking-widest border border-indigo-100 shadow-xs">Modul Rekam Medis</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400 tracking-wider uppercase">Arsip Berkas & Lampiran</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Dokumen Pasien: <span class="text-indigo-600 font-extrabold">{{ $patient->name }}</span>
                </h2>
            </div>
            <div>
                <a href="{{ route('patients.show', $patient->id) }}" class="inline-flex items-center px-5 py-3.5 bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all duration-300">
                    &larr; Detail Pasien
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10" x-data="{
        previewModal: false,
        previewUrl: '',
        previewTitle: '',
        previewType: 'other',
        dateModal: false,
        dateAction: '',
        dateTitle: '',
        documentDate: ''
    }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
            
            <!-- SECTION: FORM UPLOAD DOKUMEN BARU -->
            <div class="relative bg-gradient-to-br from-slate-950 via-indigo-950 to-slate-900 text-white rounded-[32px] p-8 sm:p-10 shadow-2xl overflow-hidden border border-indigo-900/40">
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-indigo-500/15 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute -left-16 -bottom-16 w-64 h-64 bg-blue-500/10 rounded-full blur-3xl pointer-events-none"></div>
                
                <div class="relative z-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <div class="lg:col-span-4 space-y-3">
                        <div class="inline-flex items-center space-x-2 px-3.5 py-1 bg-white/10 backdrop-blur-md text-indigo-200 font-extrabold text-[10px] rounded-full uppercase tracking-widest border border-white/10">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Manajemen Arsip</span>
                        </div>
                        <h3 class="text-2xl font-black tracking-tight text-white">Unggah Dokumen Baru</h3>
                        <p class="text-xs text-indigo-200/80 font-medium leading-relaxed">
                            Unggah berkas penunjang medis seperti hasil lab, rontgen, surat rujukan, foto, atau video hasil pemeriksaan (Format: PDF, JPG, JPEG, PNG, WEBP, MP4, WEBM. Maks: 50MB).
                        </p>
                    </div>

                    <div class="lg:col-span-8">
                        <form action="{{ route('patients.documents.store', $patient->id) }}" method="POST" enctype="multipart/form-data" class="bg-white/10 backdrop-blur-xl p-6 rounded-3xl border border-white/15 shadow-inner space-y-4">
                            @csrf
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-black text-indigo-200 uppercase tracking-wider">Nama / Judul Dokumen</label>
                                    <input type="text" name="document_name" placeholder="Contoh: Hasil Lab Darah / Rontgen Thorax" class="w-full text-xs font-bold text-slate-900 bg-white border-0 rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-indigo-400" required>
                                </div>
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-black text-indigo-200 uppercase tracking-wider">Pilih File Berkas (Maks. 50MB)</label>
                                    <input type="file" name="document_file" accept=".pdf,.jpg,.jpeg,.png,.webp,.gif,.mp4,.webm,.ogg,.mov,application/pdf,image/*,video/*" class="w-full text-xs text-indigo-100 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-white file:text-indigo-950 hover:file:bg-indigo-50 cursor-pointer transition-all" required>
                                </div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-black text-indigo-200 uppercase tracking-wider">Tanggal Dokumen</label>
                                    <input
                                        type="datetime-local"
                                        name="document_date"
                                        value="{{ old('document_date', now()->format('Y-m-d\TH:i')) }}"
                                        class="w-full text-xs font-bold text-slate-900 bg-white border-0 rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-indigo-400">
                                    <p class="text-[10px] text-indigo-200/70 font-medium">Tanggal ini dapat diedit lagi setelah dokumen disimpan.</p>
                                </div>

                                <div class="space-y-1.5">
                                    <label class="text-[11px] font-black text-indigo-200 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                                    <input type="text" name="notes" placeholder="Keterangan singkat mengenai dokumen ini..." class="w-full text-xs font-bold text-slate-900 bg-white border-0 rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-indigo-400">
                                </div>
                            </div>
                            <div class="flex justify-end pt-2">
                                <button type="submit" class="inline-flex items-center justify-center px-7 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-lg shadow-indigo-600/30 transition-all active:scale-95">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                                    Simpan & Unggah Dokumen
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- SECTION: DAFTAR ARSIP DOKUMEN -->
            <div class="space-y-6">
                <div class="flex items-center justify-between px-3">
                    <div class="flex items-center space-x-2">
                        <h3 class="text-xs font-black text-slate-800 uppercase tracking-widest">Daftar Arsip Dokumen Pasien</h3>
                        <span class="text-[10px] font-black px-2 py-0.5 bg-slate-200 text-slate-700 rounded-md">{{ count($patient->documents ?? []) }} Berkas</span>
                    </div>
                    <span class="text-xs font-extrabold text-indigo-600 bg-indigo-50 px-4 py-1.5 rounded-xl border border-indigo-100 shadow-xs">Penyimpanan Aman</span>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @forelse($patient->documents ?? [] as $doc)
                    @php
                        $fileUrl = route('patients.documents.preview', [
                            'patient' => $patient->id,
                            'document' => $doc->id,
                        ]);

                        $downloadUrl = route('patients.documents.download', [
                            'patient' => $patient->id,
                            'document' => $doc->id,
                        ]);

                        $dateUpdateUrl = route('patients.documents.date.update', [
                            'patient' => $patient->id,
                            'document' => $doc->id,
                        ]);

                        $effectiveDocumentDate = $doc->document_date
                            ? \Carbon\Carbon::parse($doc->document_date)
                            : $doc->created_at;

                        $documentDateInput = $effectiveDocumentDate
                            ? $effectiveDocumentDate->format('Y-m-d\TH:i')
                            : '';

                        $documentDateDisplay = $effectiveDocumentDate
                            ? $effectiveDocumentDate->translatedFormat('d F Y • H:i')
                            : '-';

                        $rawType = strtolower(trim((string) ($doc->file_type ?? '')));
                        $extension = strtolower(pathinfo((string) $doc->file_path, PATHINFO_EXTENSION));

                        $imageExtensions = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];
                        $videoExtensions = ['mp4', 'webm', 'ogg', 'mov', 'm4v'];
                        $pdfExtensions   = ['pdf'];

                        $isImg =
                            str_starts_with($rawType, 'image/') ||
                            in_array($rawType, $imageExtensions, true) ||
                            in_array($extension, $imageExtensions, true);

                        $isVideo =
                            str_starts_with($rawType, 'video/') ||
                            in_array($rawType, $videoExtensions, true) ||
                            in_array($extension, $videoExtensions, true);

                        $isPdf =
                            $rawType === 'application/pdf' ||
                            $rawType === 'pdf' ||
                            in_array($extension, $pdfExtensions, true);

                        $previewType =
                            $isImg ? 'image' :
                            ($isVideo ? 'video' :
                            ($isPdf ? 'pdf' : 'other'));

                        $displayType =
                            $isImg ? 'IMAGE' :
                            ($isVideo ? 'VIDEO' :
                            ($isPdf ? 'PDF' :
                            (strtoupper($extension ?: ($rawType ?: 'FILE')))));
                    @endphp
                    <div class="bg-white rounded-[28px] border border-slate-100 shadow-xl shadow-slate-100/60 p-6 flex flex-col justify-between space-y-4 hover:shadow-2xl transition-all duration-300">
                        <div class="space-y-3">
                            
                            <!-- Header Card & Preview Kotak Kecil -->
                            <div class="flex items-start justify-between gap-3">
                                <!-- Kotak Preview Thumbnail / Ikon -->
                                <div @click="previewModal = true; previewUrl = @js($fileUrl); previewTitle = @js($doc->document_name); previewType = @js($previewType)" class="w-16 h-16 rounded-2xl bg-slate-100 overflow-hidden flex items-center justify-center text-xl shrink-0 shadow-inner cursor-pointer relative group border border-slate-200" title="Klik untuk Preview">
                                    @if($isImg)
                                        <img src="{{ $fileUrl }}"
                                             class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                             alt="Thumbnail {{ $doc->document_name }}">

                                    @elseif($isVideo)
                                        <video
                                            src="{{ $fileUrl }}#t=0.1"
                                            class="w-full h-full object-cover bg-black"
                                            muted
                                            preload="metadata"
                                            playsinline>
                                        </video>

                                        <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                            <div class="w-8 h-8 rounded-full bg-black/55 text-white flex items-center justify-center backdrop-blur-sm">
                                                <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M8 5v14l11-7z"/>
                                                </svg>
                                            </div>
                                        </div>

                                    @elseif($isPdf)
                                        <div class="flex flex-col items-center justify-center text-rose-600 font-black">
                                            <span class="text-[10px] uppercase">PDF</span>
                                            <svg class="w-6 h-6 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                            </svg>
                                        </div>

                                    @else
                                        <div class="flex flex-col items-center justify-center text-slate-600 font-black px-1">
                                            <span class="text-[9px] uppercase text-center break-all">{{ $displayType }}</span>
                                            <svg class="w-6 h-6 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12h6m-6 4h6M9 8h2m2-5H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V7l-4-4z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center text-white text-[10px] font-extrabold uppercase tracking-tighter">
                                        Preview
                                    </div>
                                </div>

                                <div class="flex items-center space-x-2">
                                    <span class="px-2.5 py-1 bg-slate-100 text-slate-700 font-extrabold text-[10px] uppercase rounded-lg">{{ $displayType }}</span>
                                    
                                    <!-- Tombol Edit Tanggal Dokumen -->
                                    <button
                                        type="button"
                                        @click="
                                            dateModal = true;
                                            dateAction = @js($dateUpdateUrl);
                                            dateTitle = @js($doc->document_name);
                                            documentDate = @js($documentDateInput);
                                        "
                                        class="w-8 h-8 rounded-xl bg-amber-50 hover:bg-amber-100 text-amber-600 flex items-center justify-center transition-all shadow-2xs"
                                        title="Edit Tanggal Dokumen">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.4"
                                                  d="M8 7V3m8 4V3M5 11h14M7 21h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v12a2 2 0 002 2zm3-6h4m-2-2v4"/>
                                        </svg>
                                    </button>

                                    <!-- Tombol Hapus Dokumen -->
                                    <form action="{{ route('patients.documents.destroy', [$patient->id, $doc->id]) }}" method="POST" class="inline-block delete-doc-form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" onclick="confirmDeleteDoc(this)" class="w-8 h-8 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-600 flex items-center justify-center transition-all shadow-2xs" title="Hapus Dokumen">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
                            </div>

                            <div>
                                <h4 class="text-sm font-black text-slate-900 line-clamp-1" title="{{ $doc->document_name }}">{{ $doc->document_name }}</h4>

                                <div class="mt-2 space-y-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <p class="text-[11px] font-extrabold text-indigo-600">
                                            📅 Tanggal Dokumen: {{ $documentDateDisplay }}
                                        </p>

                                        <button
                                            type="button"
                                            @click="
                                                dateModal = true;
                                                dateAction = @js($dateUpdateUrl);
                                                dateTitle = @js($doc->document_name);
                                                documentDate = @js($documentDateInput);
                                            "
                                            class="text-[10px] font-black text-amber-600 hover:text-amber-700 uppercase tracking-wider"
                                            title="Edit Tanggal Dokumen">
                                            Edit
                                        </button>
                                    </div>

                                    <p class="text-[10px] font-bold text-slate-400">
                                        🕘 Diunggah ke sistem: {{ $doc->created_at->translatedFormat('d F Y • H:i') }}
                                    </p>
                                </div>
                                @if($doc->notes)
                                <p class="text-xs font-medium text-slate-600 bg-slate-50 p-3 rounded-xl mt-2 line-clamp-2">💬 {{ $doc->notes }}</p>
                                @endif
                            </div>
                        </div>

                        <!-- Tombol Aksi Preview / Download -->
                        <div class="pt-2 border-t border-slate-50 flex items-center gap-2">
                            <button @click="previewModal = true; previewUrl = @js($fileUrl); previewTitle = @js($doc->document_name); previewType = @js($previewType)" class="flex-1 inline-flex items-center justify-center px-4 py-2.5 bg-indigo-50 hover:bg-indigo-600 text-indigo-600 hover:text-white font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all shadow-2xs group">
                                <svg class="w-4 h-4 mr-1.5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                Lihat
                            </button>
                            <a href="{{ $downloadUrl }}" class="inline-flex items-center justify-center px-3 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs rounded-xl transition-all shadow-2xs" title="Unduh Berkas">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                            </a>
                        </div>
                    </div>
                    @empty
                    <div class="col-span-full bg-white rounded-[32px] border border-slate-100 shadow-xl p-16 text-center">
                        <div class="max-w-sm mx-auto space-y-4">
                            <div class="w-16 h-16 bg-indigo-50 rounded-2xl flex items-center justify-center mx-auto text-indigo-500 shadow-xs">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"/></svg>
                            </div>
                            <h4 class="text-sm font-black text-slate-800">Belum Ada Dokumen</h4>
                            <p class="text-xs font-medium text-slate-500 leading-relaxed">Belum ada arsip berkas atau lampiran yang diunggah untuk pasien ini. Silakan gunakan form di atas untuk mengunggah dokumen baru.</p>
                        </div>
                    </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- MODAL PREVIEW DOKUMEN -->
        <div x-show="previewModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">
            <div @click.away="previewModal = false" class="bg-white rounded-[32px] max-w-4xl w-full shadow-2xl overflow-hidden border border-slate-100 flex flex-col max-h-[90vh]">
                
                <!-- Modal Header -->
                <div class="px-6 py-4 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-sm font-black text-slate-900 truncate" x-text="previewTitle"></h3>
                    <button @click="previewModal = false" class="w-8 h-8 rounded-full bg-slate-200/80 hover:bg-rose-100 hover:text-rose-600 flex items-center justify-center text-slate-600 transition-colors">
                        ✕
                    </button>
                </div>

                <!-- Modal Body: IMAGE / VIDEO / PDF / FILE LAIN -->
                <div class="p-6 overflow-y-auto flex-1 flex items-center justify-center bg-slate-900/5 min-h-[400px]">

                    <!-- IMAGE -->
                    <template x-if="previewType === 'image'">
                        <img
                            :src="previewUrl"
                            class="max-w-full max-h-[70vh] rounded-2xl shadow-md object-contain bg-black"
                            alt="Preview Dokumen">
                    </template>

                    <!-- VIDEO -->
                    <template x-if="previewType === 'video'">
                        <video
                            :src="previewUrl"
                            class="w-full max-h-[70vh] rounded-2xl shadow-xl bg-black"
                            controls
                            preload="metadata"
                            playsinline>
                            Browser tidak mendukung pemutaran video.
                        </video>
                    </template>

                    <!-- PDF -->
                    <template x-if="previewType === 'pdf'">
                        <iframe
                            :src="previewUrl"
                            class="w-full h-[70vh] rounded-2xl border-0 shadow-inner bg-white">
                        </iframe>
                    </template>

                    <!-- FILE LAIN -->
                    <template x-if="previewType === 'other'">
                        <div class="w-full max-w-md text-center bg-white rounded-3xl border border-slate-200 shadow-sm p-10">
                            <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 text-slate-500 flex items-center justify-center mb-4">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6M9 8h2m2-5H7a2 2 0 00-2 2v14a2 2 0 002 2h10a2 2 0 002-2V7l-4-4z"/>
                                </svg>
                            </div>

                            <h4 class="text-sm font-black text-slate-800">
                                Preview tidak tersedia
                            </h4>

                            <p class="text-xs text-slate-500 mt-2 leading-relaxed">
                                Browser tidak dapat menampilkan jenis file ini secara langsung.
                                Gunakan tombol buka tab baru atau download.
                            </p>
                        </div>
                    </template>

                </div>

                <!-- Modal Footer -->
                <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex items-center justify-end gap-3">
                    <a :href="previewUrl" target="_blank" class="inline-flex items-center px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                        Buka di Tab Baru ↗
                    </a>
                    <button @click="previewModal = false" class="inline-flex items-center px-5 py-2.5 bg-slate-200 hover:bg-slate-300 text-slate-700 font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all">
                        Tutup
                    </button>
                </div>

            </div>
        </div>

        <!-- MODAL EDIT TANGGAL DOKUMEN -->
        <div
            x-show="dateModal"
            x-cloak
            @keydown.escape.window="dateModal = false"
            class="fixed inset-0 z-[60] overflow-y-auto bg-slate-950/80 backdrop-blur-md flex items-center justify-center p-4">

            <div
                @click.away="dateModal = false"
                class="bg-white rounded-[30px] max-w-md w-full shadow-2xl overflow-hidden border border-slate-100">

                <div class="px-6 py-5 bg-slate-50 border-b border-slate-100 flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <p class="text-[10px] font-black uppercase tracking-widest text-amber-600">
                            Edit Tanggal Dokumen
                        </p>
                        <h3 class="text-sm font-black text-slate-900 truncate mt-1" x-text="dateTitle"></h3>
                    </div>

                    <button
                        type="button"
                        @click="dateModal = false"
                        class="w-8 h-8 shrink-0 rounded-full bg-slate-200/80 hover:bg-rose-100 hover:text-rose-600 flex items-center justify-center text-slate-600 transition-colors">
                        ✕
                    </button>
                </div>

                <form :action="dateAction" method="POST" class="p-6 space-y-5">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-2">
                        <label class="text-[11px] font-black text-slate-700 uppercase tracking-wider">
                            Tanggal & Jam Dokumen
                        </label>

                        <input
                            type="datetime-local"
                            name="document_date"
                            x-model="documentDate"
                            required
                            class="w-full text-sm font-bold text-slate-900 bg-white border border-slate-200 rounded-2xl px-4 py-3.5 focus:ring-2 focus:ring-amber-400 focus:border-amber-400">

                        <p class="text-[11px] text-slate-500 font-medium leading-relaxed">
                            Yang diubah hanya <strong>tanggal dokumen</strong>.
                            Waktu file diunggah ke sistem tetap disimpan pada <code>created_at</code>.
                        </p>
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-2">
                        <button
                            type="button"
                            @click="dateModal = false"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs uppercase tracking-wider rounded-xl transition-all">
                            Batal
                        </button>

                        <button
                            type="submit"
                            class="px-5 py-2.5 bg-amber-500 hover:bg-amber-400 text-white font-black text-xs uppercase tracking-wider rounded-xl shadow-md shadow-amber-500/20 transition-all">
                            Simpan Tanggal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Script SweetAlert2 untuk Konfirmasi Hapus Dokumen -->
    <script>
        function confirmDeleteDoc(button) {
            const form = button.closest('form');
            Swal.fire({
                title: 'Hapus Dokumen Ini?',
                text: "Berkas arsip akan dihapus permanen dari sistem!",
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
    </script>
</x-app-layout>
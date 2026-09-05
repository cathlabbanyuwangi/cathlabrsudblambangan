<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-3">
            <div>
                <div class="flex items-center space-x-2.5 mb-1">
                    <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 font-black text-[10px] rounded-xl uppercase tracking-widest border border-indigo-100 shadow-2xs">
                        Modul Pencitraan
                    </span>
                    <span class="text-indigo-300 font-bold">•</span>
                    <span class="text-xs font-extrabold text-slate-400 tracking-wider uppercase">Import Angiografi</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Upload Berkas DICOM
                </h2>
            </div>

            <a href="{{ route('patients.dicom.index', ['patient' => $patient]) }}" 
               class="inline-flex items-center px-5 py-3.5 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all">
                <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/60 min-h-screen text-slate-800">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- PASIEN TUJUAN --}}
            <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.03)] p-6 sm:p-8">
                <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4 mb-6">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black shadow-inner border border-indigo-100">
                        👤
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Pasien Tujuan</h3>
                        <p class="text-[11px] font-bold text-slate-400 mt-0.5">Identitas subjek penerima berkas pencitraan medis</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                        <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Nama Pasien</span>
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider block">{{ $patient->name }}</span>
                    </div>

                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                        <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">No. Rekam Medis</span>
                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider block">{{ $patient->medical_record_number ?? '-' }}</span>
                    </div>

                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                        <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Tanggal Lahir</span>
                        <span class="text-xs font-black text-slate-900 block">
                            @if($patient->date_of_birth)
                                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d-m-Y') }}
                            @else
                                -
                            @endif
                        </span>
                    </div>
                </div>

                <div class="p-4 bg-amber-50/80 border border-amber-200/80 rounded-2xl text-amber-900 text-xs font-bold flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                    <span>Pastikan file DICOM yang akan diupload memang benar milik pasien di atas.</span>
                </div>
            </div>

            {{-- SESSION ERROR --}}
            @if(session('error'))
                <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-2xl text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs">
                    <div class="flex items-center gap-2.5">
                        <svg class="w-4 h-4 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-rose-600 font-bold">&times;</button>
                </div>
            @endif

            {{-- VALIDATION ERRORS --}}
            @if($errors->any())
                <div class="p-5 bg-rose-50 border border-rose-200/80 rounded-2xl text-rose-900 text-xs shadow-xs space-y-2">
                    <strong class="font-black uppercase tracking-wider block text-rose-950">File tidak dapat diupload:</strong>
                    <ul class="list-disc list-inside space-y-1 font-medium">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- FORM UPLOAD DICOM --}}
            <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.03)] p-6 sm:p-8">
                <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4 mb-6">
                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black shadow-inner border border-indigo-100">
                        🩻
                    </div>
                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">File Berkas DICOM</h3>
                        <p class="text-[11px] font-bold text-slate-400 mt-0.5">Pilih berkas citra medis untuk diimpor ke sistem Orthanc</p>
                    </div>
                </div>

                <form action="{{ route('patients.dicom.store', ['patient' => $patient]) }}" 
                      method="POST" 
                      enctype="multipart/form-data" 
                      id="dicomUploadForm"
                      class="space-y-6">
                    @csrf

                    <div>
                        <label for="dicom_files" class="block text-xs font-black text-slate-700 uppercase tracking-wider mb-2">
                            Pilih File DICOM *
                        </label>

                        <div class="flex items-center justify-center w-full">
                            <label class="flex flex-col items-center justify-center w-full h-36 border-2 border-slate-300 border-dashed rounded-3xl cursor-pointer bg-slate-50/50 hover:bg-slate-50 transition-all">
                                <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4 text-center">
                                    <svg class="w-8 h-8 mb-3 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                    <p class="mb-1 text-xs font-bold text-slate-700"><span class="font-black text-indigo-600">Klik untuk memilih file</span> atau seret kemari</p>
                                    <p class="text-[10px] text-slate-400 font-medium">Mendukung: <strong class="text-slate-600">.dcm</strong>, DICOM tanpa ekstensi, Philips XA multi-frame/cine, & ZIP</p>
                                </div>
                                <input type="file" name="dicom_files[]" id="dicom_files" multiple required class="hidden" />
                            </label>
                        </div>
                    </div>

                    {{-- DAFTAR FILE TERPILIH --}}
                    <div id="selectedFiles" class="bg-slate-50 border border-slate-200/80 rounded-2xl p-5 space-y-3" style="display: none;">
                        <div class="flex items-center justify-between border-b border-slate-200/60 pb-3">
                            <strong class="text-xs font-black text-slate-900 uppercase tracking-wider">File Dipilih</strong>
                            <span class="px-2.5 py-1 bg-indigo-600 text-white text-[10px] font-black rounded-lg shadow-2xs" id="fileCount">0</span>
                        </div>

                        <div id="selectedFilesList" class="space-y-1.5 text-xs font-medium text-slate-600 max-h-48 overflow-y-auto custom-scrollbar pr-2"></div>

                        <div class="border-t border-slate-200/60 pt-3 flex justify-between items-center text-xs">
                            <span class="font-bold text-slate-500 uppercase text-[10px] tracking-wider">Total Ukuran:</span>
                            <strong class="font-black text-slate-900 text-sm" id="totalFileSize">0 MB</strong>
                        </div>
                    </div>

                    <div class="p-4 bg-indigo-50/70 border border-indigo-100 rounded-2xl flex items-start gap-3">
                        <svg class="w-5 h-5 text-indigo-600 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="text-xs text-indigo-950 font-medium leading-relaxed">
                            File DICOM asli akan disimpan di <strong class="font-black text-indigo-700">Orthanc</strong>. Berkas tidak akan diubah menjadi JPG sehingga data cine/multi-frame Philips tetap dipertahankan secara utuh.
                        </p>
                    </div>

                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-100">
                        <a href="{{ route('patients.dicom.index', ['patient' => $patient]) }}" 
                           class="px-5 py-3.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-black uppercase tracking-wider rounded-2xl transition-all shadow-2xs">
                            Batal
                        </a>

                        <button type="submit" 
                                id="uploadButton"
                                class="px-6 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-black uppercase tracking-wider rounded-2xl shadow-[0_4px_14px_rgba(99,102,241,0.35)] transition-all inline-flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                            Upload ke Orthanc
                        </button>
                    </div>
                </form>
            </div>

        </div>
    </div>

    {{-- SCRIPT INTERAKSI FILE (Mempertahankan ID dan fungsionalitas asli) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const input = document.getElementById('dicom_files');
            const selectedBox = document.getElementById('selectedFiles');
            const selectedList = document.getElementById('selectedFilesList');
            const fileCount = document.getElementById('fileCount');
            const totalSize = document.getElementById('totalFileSize');
            const form = document.getElementById('dicomUploadForm');
            const uploadButton = document.getElementById('uploadButton');

            input.addEventListener('change', function () {
                selectedList.innerHTML = '';
                let totalBytes = 0;

                if (!this.files.length) {
                    selectedBox.style.display = 'none';
                    fileCount.innerText = '0';
                    totalSize.innerText = '0 MB';
                    return;
                }

                selectedBox.style.display = 'block';
                fileCount.innerText = this.files.length;

                Array.from(this.files).forEach(function (file, index) {
                    totalBytes += file.size;
                    const sizeMB = (file.size / 1024 / 1024).toFixed(2);

                    const item = document.createElement('div');
                    item.className = 'flex items-center justify-between py-1.5 px-3 bg-white rounded-xl border border-slate-200/60 shadow-2xs';
                    item.innerHTML = '<div class="flex items-center gap-2 truncate"><svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg><span class="font-bold text-slate-700 truncate">' + (index + 1) + '. ' + file.name + '</span></div>' +
                                     '<span class="text-[10px] font-black text-slate-400 shrink-0 font-mono">' + sizeMB + ' MB</span>';

                    selectedList.appendChild(item);
                });

                totalSize.innerText = (totalBytes / 1024 / 1024).toFixed(2) + ' MB';
            });

            form.addEventListener('submit', function () {
                uploadButton.disabled = true;
                uploadButton.innerHTML = '<svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg> Mengupload DICOM...';
            });
        });
    </script>
</x-app-layout>
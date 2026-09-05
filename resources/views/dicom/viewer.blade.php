<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-3">
            <div>
                <div class="flex items-center space-x-2.5 mb-1">
                    <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 font-black text-[10px] rounded-xl uppercase tracking-widest border border-indigo-100 shadow-2xs">
                        Modul Pencitraan
                    </span>
                    <span class="text-indigo-300 font-bold">•</span>
                    <span class="text-xs font-extrabold text-slate-400 tracking-wider uppercase">OHIF Viewer</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    DICOM Viewer
                </h2>
                <div class="text-xs font-extrabold text-slate-500 mt-0.5 tracking-wide">
                    {{ $patient->name }}
                    @if($patient->medical_record_number)
                        <span class="text-indigo-600 font-black">- RM {{ $patient->medical_record_number }}</span>
                    @endif
                </div>
            </div>

            <div>
                <a href="{{ route('patients.dicom.index', ['patient' => $patient]) }}"
                   class="inline-flex items-center px-5 py-3.5 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all">
                    <svg class="w-4 h-4 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10 bg-slate-50/60 min-h-screen text-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            {{-- Pesan loading --}}
            <div id="viewerLoading" class="p-4 bg-indigo-50 border border-indigo-200/80 rounded-2xl text-indigo-900 text-xs font-bold flex items-center gap-3 shadow-xs">
                <svg class="w-4 h-4 text-indigo-600 animate-spin shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span>Memuat DICOM Viewer...</span>
            </div>

            <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.03)] overflow-hidden">

                <div class="p-6 sm:p-8 border-b border-slate-100 bg-white">
                    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Pasien</span>
                            <span class="text-xs font-black text-slate-900 uppercase tracking-wider block truncate">{{ $patient->name }}</span>
                        </div>

                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">No. RM</span>
                            <span class="text-xs font-black text-slate-900 uppercase tracking-wider block">{{ $patient->medical_record_number ?? '-' }}</span>
                        </div>

                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Tanggal</span>
                            <span class="text-xs font-black text-slate-900 block">
                                @if($dicomStudy->study_date)
                                    {{ $dicomStudy->study_date->format('d-m-Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>

                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">
                            <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Pemeriksaan</span>
                            <span class="text-xs font-black text-slate-900 block truncate">{{ $dicomStudy->study_description ?: '-' }}</span>
                        </div>

                        <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100 col-span-2 sm:col-span-1">
                            <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">Modalitas</span>
                            <span class="text-xs font-black text-slate-900 block">{{ $dicomStudy->modality ?: '-' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card-body p-0 position-relative bg-black">
                    <iframe
                        id="dicomViewer"
                        src="{{ $viewerUrl }}"
                        title="DICOM Viewer"
                        style="
                            display:block;
                            width:100%;
                            height:calc(100vh - 220px);
                            min-height:700px;
                            border:0;
                            background:#000;
                        "
                        allow="fullscreen"
                        allowfullscreen>
                    </iframe>
                </div>

            </div>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const iframe = document.getElementById('dicomViewer');
            const loading = document.getElementById('viewerLoading');

            iframe.addEventListener('load', function () {
                loading.style.display = 'none';
            });

        });
    </script>
</x-app-layout>
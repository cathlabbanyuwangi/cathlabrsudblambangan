<x-app-layout>

    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-3">

            <div>
                <div class="flex items-center space-x-2.5 mb-1">

                    <span class="inline-flex items-center px-3 py-1 bg-indigo-50 text-indigo-700 font-black text-[10px] rounded-xl uppercase tracking-widest border border-indigo-100 shadow-2xs">
                        Modul Pencitraan
                    </span>

                    <span class="text-indigo-300 font-bold">•</span>

                    <span class="text-xs font-extrabold text-slate-400 tracking-wider uppercase">
                        Angiografi & DICOM
                    </span>

                </div>

                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Pemeriksaan DICOM Pasien
                </h2>
            </div>


            <a href="{{ route('patients.dicom.create', ['patient' => $patient]) }}"
               class="inline-flex items-center justify-center px-5 py-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-[0_4px_14px_rgba(99,102,241,0.35)] transition-all transform active:scale-95 shrink-0">

                <svg class="w-4 h-4 mr-2"
                     fill="none"
                     stroke="currentColor"
                     viewBox="0 0 24 24">

                    <path stroke-linecap="round"
                          stroke-linejoin="round"
                          stroke-width="2.5"
                          d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                </svg>

                Upload DICOM
            </a>

        </div>
    </x-slot>


    <div class="py-10 bg-slate-50/60 min-h-screen text-slate-800">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">


            {{-- =========================================================
                 FLASH SUCCESS
            ========================================================== --}}
            @if(session('success'))

                <div class="p-4 bg-emerald-50 border border-emerald-200/80 rounded-2xl text-emerald-800 text-xs font-bold flex items-center justify-between shadow-xs">

                    <div class="flex items-center gap-2.5">

                        <svg class="w-4 h-4 text-emerald-600 shrink-0"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2.5"
                                  d="M5 13l4 4L19 7"/>
                        </svg>

                        <span>
                            {{ session('success') }}
                        </span>

                    </div>

                    <button
                        type="button"
                        onclick="this.parentElement.remove()"
                        class="text-emerald-400 hover:text-emerald-600 font-bold">
                        &times;
                    </button>

                </div>

            @endif



            {{-- =========================================================
                 FLASH ERROR
            ========================================================== --}}
            @if(session('error'))

                <div class="p-4 bg-rose-50 border border-rose-200/80 rounded-2xl text-rose-800 text-xs font-bold flex items-center justify-between shadow-xs">

                    <div class="flex items-center gap-2.5">

                        <svg class="w-4 h-4 text-rose-600 shrink-0"
                             fill="none"
                             stroke="currentColor"
                             viewBox="0 0 24 24">

                            <path stroke-linecap="round"
                                  stroke-linejoin="round"
                                  stroke-width="2.5"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>

                        <span>
                            {{ session('error') }}
                        </span>

                    </div>

                    <button
                        type="button"
                        onclick="this.parentElement.remove()"
                        class="text-rose-400 hover:text-rose-600 font-bold">
                        &times;
                    </button>

                </div>

            @endif



            {{-- =========================================================
                 DATA PASIEN
            ========================================================== --}}
            <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.03)] p-6 sm:p-8">

                <div class="flex items-center space-x-3.5 border-b border-slate-100 pb-4 mb-6">

                    <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black shadow-inner border border-indigo-100">
                        👤
                    </div>

                    <div>
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">
                            Informasi Ringkas Pasien
                        </h3>

                        <p class="text-[11px] font-bold text-slate-400 mt-0.5">
                            Identitas rekam medis subjek pemeriksaan
                        </p>
                    </div>

                </div>


                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">


                    {{-- NAMA --}}
                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">

                        <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">
                            Nama Pasien
                        </span>

                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider block">
                            {{ $patient->name }}
                        </span>

                    </div>


                    {{-- NO RM --}}
                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">

                        <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">
                            No. Rekam Medis
                        </span>

                        <span class="text-xs font-black text-slate-900 uppercase tracking-wider block">
                            {{ $patient->medical_record_number ?? '-' }}
                        </span>

                    </div>


                    {{-- TANGGAL LAHIR --}}
                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">

                        <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">
                            Tanggal Lahir
                        </span>

                        <span class="text-xs font-black text-slate-900 block">

                            @if($patient->date_of_birth)

                                {{ \Carbon\Carbon::parse($patient->date_of_birth)->format('d-m-Y') }}

                                <span class="text-indigo-600 font-black">
                                    ({{ \Carbon\Carbon::parse($patient->date_of_birth)->age }} Th)
                                </span>

                            @else
                                -
                            @endif

                        </span>

                    </div>


                    {{-- JENIS KELAMIN --}}
                    <div class="bg-slate-50/80 p-4 rounded-2xl border border-slate-100">

                        <span class="block text-[10px] font-black text-indigo-600 uppercase tracking-widest mb-1">
                            Jenis Kelamin
                        </span>

                        <span class="text-xs font-black text-slate-900 block">

                            @if($patient->gender === 'L')
                                Laki-laki
                            @elseif($patient->gender === 'P')
                                Perempuan
                            @else
                                -
                            @endif

                        </span>

                    </div>


                </div>

            </div>



            {{-- =========================================================
                 LIST DICOM
            ========================================================== --}}
            <div class="bg-white rounded-[32px] border border-slate-200/80 shadow-[0_10px_30px_rgba(0,0,0,0.03)] overflow-hidden">


                {{-- HEADER LIST --}}
                <div class="p-6 sm:p-8 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

                    <div class="flex items-center space-x-3.5">

                        <div class="w-10 h-10 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black shadow-inner border border-indigo-100">
                            🩻
                        </div>

                        <div>

                            <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">
                                Daftar Pemeriksaan DICOM
                            </h3>

                            <p class="text-[11px] font-bold text-slate-400 mt-0.5">
                                Arsip citra medis dan angiografi tersimpan
                            </p>

                        </div>

                    </div>


                    <span class="px-3 py-1.5 bg-slate-100 text-slate-700 text-xs font-black rounded-xl border border-slate-200/60 self-start sm:self-auto">
                        Total: {{ $studies->total() }} Study
                    </span>

                </div>



                {{-- =====================================================
                     TABLE
                ====================================================== --}}
                @if($studies->count() > 0)

                    <div class="overflow-x-auto p-2">

                        <table class="w-full text-left border-collapse">


                            {{-- TABLE HEADER --}}
                            <thead class="bg-slate-50/80 text-slate-400 uppercase text-[10px] font-black tracking-widest border-b border-slate-100">

                                <tr>

                                    <th class="px-6 py-4 rounded-l-2xl"
                                        style="width:60px;">
                                        No
                                    </th>

                                    <th class="px-6 py-4">
                                        Tanggal & Waktu
                                    </th>

                                    <th class="px-6 py-4">
                                        Pemeriksaan
                                    </th>

                                    <th class="px-6 py-4">
                                        Modality
                                    </th>

                                    <th class="px-6 py-4 text-center">
                                        Series
                                    </th>

                                    <th class="px-6 py-4 text-center">
                                        Instance
                                    </th>

                                    <th class="px-6 py-4">
                                        Data DICOM
                                    </th>

                                    <th class="px-6 py-4 text-right rounded-r-2xl"
                                        style="min-width:350px;">
                                        Aksi
                                    </th>

                                </tr>

                            </thead>



                            {{-- TABLE BODY --}}
                            <tbody class="divide-y divide-slate-100/80 text-sm">


                                @foreach($studies as $study)

                                    <tr class="hover:bg-indigo-50/30 transition-all group">


                                        {{-- NO --}}
                                        <td class="px-6 py-4 text-xs font-bold text-slate-400 align-top">

                                            {{ $studies->firstItem() + $loop->index }}

                                        </td>



                                        {{-- TANGGAL --}}
                                        <td class="px-6 py-4 align-top">

                                            @if($study->study_date)

                                                <strong class="text-xs font-black text-slate-800 block">

                                                    {{ $study->study_date->format('d-m-Y') }}

                                                </strong>


                                                @if($study->study_time)

                                                    <span class="text-[10px] font-bold text-slate-400 mt-0.5 block font-mono">

                                                        {{ substr($study->study_time, 0, 2) }}:{{ substr($study->study_time, 2, 2) }}

                                                    </span>

                                                @endif

                                            @else

                                                <span class="text-xs text-slate-400">
                                                    -
                                                </span>

                                            @endif

                                        </td>



                                        {{-- PEMERIKSAAN --}}
                                        <td class="px-6 py-4 align-top">

                                            <strong class="text-xs font-black text-slate-900 block">

                                                {{ $study->study_description ?: 'DICOM Study' }}

                                            </strong>


                                            @if($study->accession_number)

                                                <span class="text-[10px] font-bold text-slate-400 mt-0.5 block">

                                                    Accession:
                                                    {{ $study->accession_number }}

                                                </span>

                                            @endif

                                        </td>



                                        {{-- MODALITY --}}
                                        <td class="px-6 py-4 align-top">

                                            @if($study->modality)

                                                <span class="px-2.5 py-1 bg-sky-50 text-sky-700 text-[10px] font-black rounded-lg uppercase border border-sky-100">

                                                    {{ $study->modality }}

                                                </span>

                                            @else

                                                <span class="text-xs text-slate-400">
                                                    -
                                                </span>

                                            @endif

                                        </td>



                                        {{-- SERIES --}}
                                        <td class="px-6 py-4 text-center align-top">

                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-[10px] font-black rounded-lg">

                                                {{ $study->series_count ?? 0 }}

                                            </span>

                                        </td>



                                        {{-- INSTANCE --}}
                                        <td class="px-6 py-4 text-center align-top">

                                            <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-[10px] font-black rounded-lg">

                                                {{ $study->instance_count ?? 0 }}

                                            </span>

                                        </td>



                                        {{-- DATA DICOM --}}
                                        <td class="px-6 py-4 align-top">

                                            <strong class="text-xs font-black text-slate-800 block">

                                                {{ $study->dicom_patient_name ?: '-' }}

                                            </strong>

                                            <span class="text-[10px] font-bold text-slate-400 mt-0.5 block">

                                                ID:
                                                {{ $study->dicom_patient_id ?: '-' }}

                                            </span>

                                        </td>



                                        {{-- =================================================
                                             AKSI
                                        ================================================== --}}
                                        <td class="px-6 py-4 text-right whitespace-nowrap align-top">

                                            <div class="inline-flex items-center justify-end gap-1.5">


                                                {{-- =========================================
                                                     DETAIL
                                                ========================================== --}}
                                                <a
                                                    href="{{ route('patients.dicom.show', [
                                                        'patient' => $patient,
                                                        'dicomStudy' => $study
                                                    ]) }}"

                                                    class="p-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all shadow-2xs"

                                                    title="Detail DICOM"
                                                >

                                                    <svg
                                                        class="w-3.5 h-3.5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                                                        />

                                                    </svg>

                                                </a>



                                                {{-- =========================================
                                                     VIEWER
                                                ========================================== --}}
                                                <a
                                                    href="{{ route('patients.dicom.viewer', [
                                                        'patient' => $patient,
                                                        'dicomStudy' => $study
                                                    ]) }}"

                                                    class="px-3 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-[10px] font-black rounded-xl uppercase inline-flex items-center gap-1.5 shadow-[0_2px_8px_rgba(99,102,241,0.25)] transition-all"

                                                    title="Buka DICOM Viewer"
                                                >

                                                    <svg
                                                        class="w-3.5 h-3.5"
                                                        fill="none"
                                                        stroke="currentColor"
                                                        viewBox="0 0 24 24"
                                                    >

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                                        />

                                                        <path
                                                            stroke-linecap="round"
                                                            stroke-linejoin="round"
                                                            stroke-width="2.5"
                                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                                        />

                                                    </svg>

                                                    Viewer

                                                </a>



                                                {{-- =========================================
                                                     EXPORT KE DOKUMEN
                                                ========================================== --}}
                                                <form
                                                    action="{{ route('patients.dicom.export', [
                                                        'patient' => $patient,
                                                        'dicomStudy' => $study
                                                    ]) }}"

                                                    method="POST"

                                                    class="inline-block"

                                                    onsubmit="
                                                        if (!confirm('Ekspor Study DICOM ini ke Dokumen pasien? Gambar statis akan dibuat sebagai JPEG dan cine/multi-frame akan dibuat sebagai MP4.')) {
                                                            return false;
                                                        }

                                                        const btn = this.querySelector('.export-button');
                                                        btn.disabled = true;

                                                        btn.querySelector('.export-normal').classList.add('hidden');
                                                        btn.querySelector('.export-loading').classList.remove('hidden');

                                                        return true;
                                                    "
                                                >

                                                    @csrf


                                                    <button
                                                        type="submit"

                                                        class="export-button px-3 py-2 bg-emerald-600 hover:bg-emerald-500 disabled:bg-emerald-300 disabled:cursor-wait text-white text-[10px] font-black rounded-xl uppercase inline-flex items-center gap-1.5 shadow-[0_2px_8px_rgba(5,150,105,0.25)] transition-all"

                                                        title="Ekspor JPEG / MP4 ke Dokumen Pasien"
                                                    >


                                                        {{-- NORMAL --}}
                                                        <span class="export-normal inline-flex items-center gap-1.5">

                                                            <svg
                                                                class="w-3.5 h-3.5"
                                                                fill="none"
                                                                stroke="currentColor"
                                                                viewBox="0 0 24 24"
                                                            >

                                                                <path
                                                                    stroke-linecap="round"
                                                                    stroke-linejoin="round"
                                                                    stroke-width="2.5"
                                                                    d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5l5 5v11a2 2 0 01-2 2z"
                                                                />

                                                            </svg>

                                                            Ekspor

                                                        </span>



                                                        {{-- LOADING --}}
                                                        <span class="export-loading hidden items-center gap-1.5">

                                                            <svg
                                                                class="animate-spin w-3.5 h-3.5"
                                                                xmlns="http://www.w3.org/2000/svg"
                                                                fill="none"
                                                                viewBox="0 0 24 24"
                                                            >

                                                                <circle
                                                                    class="opacity-25"
                                                                    cx="12"
                                                                    cy="12"
                                                                    r="10"
                                                                    stroke="currentColor"
                                                                    stroke-width="4"
                                                                />

                                                                <path
                                                                    class="opacity-75"
                                                                    fill="currentColor"
                                                                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                                                                />

                                                            </svg>

                                                            Proses...

                                                        </span>

                                                    </button>

                                                </form>



                                                {{-- =========================================
                                                     HAPUS
                                                ========================================== --}}
                                                <form
                                                    action="{{ route('patients.dicom.destroy', [
                                                        'patient' => $patient,
                                                        'dicomStudy' => $study
                                                    ]) }}"

                                                    method="POST"

                                                    class="inline-block"

                                                    onsubmit="return confirm('Yakin ingin menghapus Study DICOM ini? File juga akan dihapus dari server Orthanc.');"
                                                >

                                                    @csrf

                                                    @method('DELETE')


                                                    <button
                                                        type="submit"

                                                        class="p-2.5 bg-rose-50 hover:bg-rose-100 text-rose-600 rounded-xl transition-all border border-rose-100 shadow-2xs"

                                                        title="Hapus Study DICOM"
                                                    >

                                                        <svg
                                                            class="w-3.5 h-3.5"
                                                            fill="none"
                                                            stroke="currentColor"
                                                            viewBox="0 0 24 24"
                                                        >

                                                            <path
                                                                stroke-linecap="round"
                                                                stroke-linejoin="round"
                                                                stroke-width="2.5"
                                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                                            />

                                                        </svg>

                                                    </button>

                                                </form>


                                            </div>

                                        </td>

                                    </tr>

                                @endforeach


                            </tbody>

                        </table>

                    </div>


                @else


                    {{-- =================================================
                         EMPTY STATE
                    ================================================== --}}
                    <div class="text-center py-16 px-4">

                        <div class="w-16 h-16 bg-slate-50 text-slate-300 rounded-3xl flex items-center justify-center mx-auto mb-4 border border-slate-100 shadow-inner">

                            <svg
                                class="w-8 h-8"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                            >

                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2z"
                                />

                            </svg>

                        </div>


                        <h5 class="text-sm font-black text-slate-800 uppercase tracking-wide">

                            Belum Ada Pemeriksaan DICOM

                        </h5>


                        <p class="text-xs text-slate-400 mt-1 max-w-sm mx-auto font-medium">

                            Belum ada pemeriksaan angiography atau DICOM
                            yang terhubung dengan pasien ini.

                        </p>


                        <div class="mt-6">

                            <a
                                href="{{ route('patients.dicom.create', ['patient' => $patient]) }}"

                                class="inline-flex items-center px-5 py-3 bg-indigo-600 hover:bg-indigo-500 text-white font-black text-xs uppercase tracking-wider rounded-2xl shadow-md transition-all"
                            >

                                <svg
                                    class="w-4 h-4 mr-2"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                >

                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2.5"
                                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"
                                    />

                                </svg>

                                Upload DICOM Sekarang

                            </a>

                        </div>

                    </div>

                @endif



                {{-- =====================================================
                     PAGINATION
                ====================================================== --}}
                @if($studies->hasPages())

                    <div class="p-6 bg-slate-50/50 border-t border-slate-100">

                        {{ $studies->links() }}

                    </div>

                @endif


            </div>



            {{-- =========================================================
                 KEMBALI
            ========================================================== --}}
            <div>

                <a
                    href="{{ route('patients.actions-history', ['patient' => $patient]) }}"

                    class="inline-flex items-center px-5 py-3.5 bg-white border border-slate-200/80 hover:bg-slate-50 text-slate-700 font-black text-xs uppercase tracking-wider rounded-2xl shadow-xs transition-all"
                >

                    <svg
                        class="w-4 h-4 mr-2 text-indigo-600"
                        fill="none"
                        stroke="currentColor"
                        viewBox="0 0 24 24"
                    >

                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            stroke-width="2.5"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18"
                        />

                    </svg>

                    Kembali ke Riwayat Tindakan

                </a>

            </div>


        </div>

    </div>

</x-app-layout>
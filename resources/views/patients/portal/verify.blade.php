<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Pasien - Cathlab RSUD Blambangan</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="max-w-md w-full bg-white rounded-[24px] shadow-xl shadow-slate-200/50 p-8 border border-slate-100">
        
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-sky-50 text-sky-600 mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <h2 class="text-2xl font-black text-slate-900">Portal Dokumen Medis</h2>
            <p class="text-sm text-slate-500 mt-2">Cathlab RSUD Blambangan</p>
        </div>

        <!-- Alert Error -->
        @if(session('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-bold mb-6 border border-red-100">
            {{ session('error') }}
        </div>
        @endif

        <!-- Form Input -->
        <form action="{{ route('patient.portal.auth') }}" method="POST" class="space-y-5">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">No. Rekam Medis (RM)</label>
                <input type="text" name="medical_record_number" required placeholder="Contoh: 123456" 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all text-slate-700 font-medium">
                @error('medical_record_number') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-2">Token Akses (6 Digit)</label>
                <input type="text" name="portal_token" required maxlength="6" placeholder="Masukkan 6 digit angka" 
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-sky-500 focus:border-sky-500 outline-none transition-all text-slate-700 font-mono font-black tracking-widest text-center text-lg">
                @error('portal_token') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
            </div>

            <button type="submit" 
                class="w-full py-3.5 px-4 bg-sky-600 hover:bg-sky-700 text-white font-black text-sm uppercase tracking-wider rounded-xl shadow-lg shadow-sky-600/20 transition-all cursor-pointer">
                Cek Dokumen Saya
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-[11px] text-slate-400">Token 6 digit dapat diminta melalui petugas admin RSUD Blambangan.</p>
        </div>
    </div>

</body>
</html>
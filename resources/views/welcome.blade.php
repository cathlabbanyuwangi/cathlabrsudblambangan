<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instalasi Terpadu (Cathlab, Neuro, Radiologi) | RSUD Blambangan Banyuwangi</title>

    <link rel="icon" type="image/png" href="{{ asset('images/IMGLOGO.png') }}">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Alpine.js untuk Modal Pop-up Interaktif -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        pastel: {
                            blue: '#F0F5FA',
                            'blue-light': '#E2ECF5',
                            'blue-med': '#9BB1C8',
                            'blue-dark': '#1D3557',
                            'blue-accent': '#457B9D',
                            sky: '#A8DADC',
                            white: '#FFFFFF',
                            muted: '#64748B'
                        }
                    }
                }
            }
        }
    </script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background-color: #F8FBFF;
            color: #1D3557;
        }
        .font-serif {
            font-family: 'Playfair Display', serif;
        }
        [x-cloak] { display: none !important; }
        .glass-card {
            background: rgba(255, 255, 255, 0.85);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(69, 123, 157, 0.15);
        }
        .parallax-section {
            background-image: linear-gradient(rgba(29, 53, 87, 0.88), rgba(69, 123, 157, 0.88)), url('https://images.unsplash.com/photo-1519494026892-80bbd2d6fd0d?auto=format&fit=crop&w=1600&q=80');
            background-attachment: fixed;
            background-position: center;
            background-repeat: no-repeat;
            background-size: cover;
        }
        
        /* CSS Khusus untuk Print Tiket */
        @media print {
            body > *:not(.print-only-ticket) { display: none !important; }
            .print-only-ticket { 
                display: block !important; 
                position: absolute; 
                left: 0; 
                top: 0; 
                width: 100%; 
                background: white; 
                padding: 20px;
            }
            .swal2-container, .fixed.inset-0 { display: none !important; }
            @page { margin: 0; size: auto; }
        }
    </style>
</head>

<!-- LOGIKA ANTI-BERTUMPUK -->
@php
    $showRegisterModal = false;
    
    if (!session('success') && !session('checked_patient') && !session('is_awaiting_verification')) {
        if ($errors->any()) {
            $showRegisterModal = true;
        }
        if (session('error') && (str_contains(session('error'), 'terdaftar') || str_contains(session('error'), 'mengajukan'))) {
            $showRegisterModal = true;
        }
        if (session('need_confirmation')) {
            $showRegisterModal = true;
        }
    }

    $tglMulai = \Carbon\Carbon::now()->addDays(14)->format('d M Y');
    $tglSelesai = \Carbon\Carbon::now()->addDays(45)->format('d M Y');
    $rentangTanggal = $tglMulai . ' - ' . $tglSelesai;
@endphp

<body class="bg-pastel-blue antialiased selection:bg-pastel-blue-accent selection:text-white" 
    x-data="{ openRegisterModal: {{ ($showRegisterModal && !session('success') && !session('checked_patient')) ? 'true' : 'false' }} }">

    <!-- NAVBAR -->
    <header class="sticky top-0 z-40 bg-white/90 backdrop-blur-xl border-b border-pastel-blue-light shadow-2xs">
        <div class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
            <div class="flex items-center space-x-3">
    <div class="w-10 h-10 flex items-center justify-center">
        <img 
            src="{{ asset('images/IMGLOGO.png') }}" 
            alt="Logo Cathlab RSUD Blambangan"
            class="w-10 h-10 object-contain"
        >
    </div>

    <div>
        <span class="font-serif text-lg font-bold tracking-tight text-pastel-blue-dark block leading-none">
            CATHLAB RSUD BLAMBANGAN
        </span>
        <span class="text-[10px] uppercase tracking-widest text-pastel-blue-accent font-semibold">
            Pusat Intervensi Terpadu
        </span>
    </div>
</div>
            <nav class="hidden md:flex items-center space-x-8 text-sm font-semibold text-pastel-blue-dark">
                <a href="#cek-jadwal" class="hover:text-pastel-blue-accent transition-colors">Cek Jadwal Mandiri</a>
                <a href="#layanan" class="hover:text-pastel-blue-accent transition-colors">Layanan Utama</a>
                <a href="#tim" class="hover:text-pastel-blue-accent transition-colors">Tim Medis</a>
                <a href="#alur" class="hover:text-pastel-blue-accent transition-colors">Alur Pelayanan</a>
                <a href="#kontak" class="hover:text-pastel-blue-accent transition-colors">Kontak</a>
            </nav>
            <div class="flex items-center space-x-4">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="bg-pastel-blue-dark hover:bg-pastel-blue-accent text-white px-5 py-2.5 rounded-full text-xs font-bold transition-all shadow-sm">
                            Dashboard Admin
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-xs font-bold text-pastel-blue-dark hover:text-pastel-blue-accent transition-colors">
                            Login Staf
                        </a>
                    @endauth
                @endif
                <button @click="openRegisterModal = true" class="bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white px-5 py-2.5 rounded-full text-xs font-bold transition-all shadow-sm">
                    Daftar Mandiri
                </button>
            </div>
        </div>
    </header>

    <!-- HERO SECTION -->
    <section class="relative overflow-hidden py-12 lg:py-20 max-w-7xl mx-auto px-6">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
            <div class="lg:col-span-7 space-y-6">
                <span class="inline-flex items-center space-x-2 px-4 py-1.5 bg-pastel-blue-light text-pastel-blue-dark font-bold text-xs rounded-full uppercase tracking-widest border border-pastel-blue-accent/30 shadow-2xs">
                    <span class="w-2 h-2 rounded-full bg-pastel-blue-accent animate-pulse"></span>
                    <span>Kardiovaskular • Neuroscience • Radiologi Intervensi</span>
                </span>
                <h1 class="font-serif text-4xl sm:text-5xl lg:text-6xl font-bold text-pastel-blue-dark tracking-tight leading-[1.15]">
                    Keunggulan Medis Terpadu Untuk <span class="italic font-normal text-pastel-blue-accent">Kesembuhan</span> Anda.
                </h1>
                <p class="text-base sm:text-lg text-pastel-muted max-w-xl font-light leading-relaxed">
                    Instalasi intervensi modern RSUD Blambangan Banyuwangi memadukan teknologi presisi tinggi untuk penanganan komprehensif penyakit jantung, gangguan saraf/otak (neuro), serta radiologi diagnostik lanjutan.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 pt-2">
                    <a href="#cek-jadwal" class="bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white text-center px-6 py-3.5 rounded-2xl font-bold text-sm transition-all shadow-md">
                        Cek Perkiraan Jadwal Antrean
                    </a>
                    <button @click="openRegisterModal = true" class="bg-white hover:bg-pastel-blue-light text-pastel-blue-dark text-center px-6 py-3.5 rounded-2xl font-bold text-sm transition-all border border-pastel-blue-accent/30 shadow-xs">
                        Pendaftaran Pasien Cathlab
                    </button>
                </div>
            </div>

            <div class="lg:col-span-5">
                <div class="glass-card p-4 rounded-3xl shadow-xl shadow-blue-900/10 space-y-4">
                    <div class="relative rounded-2xl overflow-hidden aspect-[4/3] bg-pastel-blue-light">
                        <img src="https://images.unsplash.com/photo-1579684385127-1ef15d508118?auto=format&fit=crop&w=800&q=80" alt="Medical Center" class="w-full h-full object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-pastel-blue-dark/85 via-transparent to-transparent flex items-end p-6">
                            <div class="text-white">
                                <span class="text-xs font-semibold uppercase tracking-wider text-pastel-sky">Teknologi Mutakhir</span>
                                <h4 class="font-serif text-lg font-bold">Pusat Intervensi & Pencitraan Modern</h4>
                            </div>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-3 text-center">
                        <div class="bg-white p-3 rounded-2xl border border-pastel-blue-light shadow-2xs">
                            <h5 class="font-serif text-sm font-bold text-pastel-blue-dark">Jantung</h5>
                            <p class="text-[10px] text-pastel-muted uppercase mt-0.5">Cathlab / PCI</p>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-pastel-blue-light shadow-2xs">
                            <h5 class="font-serif text-sm font-bold text-pastel-blue-accent">Neuro</h5>
                            <p class="text-[10px] text-pastel-muted uppercase mt-0.5">DSA / Saraf</p>
                        </div>
                        <div class="bg-white p-3 rounded-2xl border border-pastel-blue-light shadow-2xs">
                            <h5 class="font-serif text-sm font-bold text-pastel-blue-dark">Radiologi</h5>
                            <p class="text-[10px] text-pastel-muted uppercase mt-0.5">Intervensi</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- SECTION: CEK PERKIRAAN JADWAL ANTREAN MANDIRI -->
    <section id="cek-jadwal" class="py-16 max-w-4xl mx-auto px-6">
        <div class="glass-card p-8 sm:p-12 rounded-3xl shadow-2xl shadow-blue-900/15 border border-pastel-blue-accent/30 bg-white relative overflow-hidden">
            <div class="absolute -right-20 -bottom-20 w-60 h-60 bg-pastel-blue-accent/10 rounded-full blur-3xl pointer-events-none"></div>
            
            <div class="relative z-10 space-y-8">
                <div class="text-center space-y-3 max-w-xl mx-auto">
                    <span class="px-3.5 py-1.5 bg-pastel-blue-light text-pastel-blue-accent font-extrabold text-[10px] rounded-full uppercase tracking-widest border border-pastel-blue-accent/20">Cek Mandiri Pasien</span>
                    <h3 class="font-serif text-3xl sm:text-4xl font-bold text-pastel-blue-dark tracking-tight">Perkiraan Jadwal Antrean</h3>
                    <p class="text-xs sm:text-sm text-pastel-muted">Masukkan Nomor Tiket (contoh: REG-26001), No. Rekam Medis (RM), atau No. Telepon terdaftar untuk melihat estimasi waktu tunggu tindakan Anda secara instan.</p>
                </div>

                @if(session('error') && !str_contains(session('error'), 'terdaftar') && !str_contains(session('error'), 'mengajukan'))
                    <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-xs sm:text-sm font-bold text-rose-600 text-center max-w-xl mx-auto shadow-sm">
                        {{ session('error') }}
                    </div>
                @endif

                <form action="{{ route('queue.check') }}" method="POST" class="space-y-4 max-w-xl mx-auto">
                    @csrf
                    <div>
                        <div class="relative">
                            <input type="text" name="keyword" value="{{ old('keyword') }}" placeholder="Contoh: REG-26001 atau RM012345" required
                                class="w-full px-5 py-4 pl-12 bg-pastel-blue/60 border border-pastel-blue-light rounded-2xl text-sm font-bold text-pastel-blue-dark focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs">
                            <span class="absolute left-4 top-4 text-lg">🔍</span>
                        </div>
                        @error('keyword')
                            <p class="text-xs text-rose-600 font-bold mt-1.5 ml-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="w-full py-4 bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white font-bold text-sm uppercase tracking-wider rounded-2xl shadow-lg shadow-pastel-blue-accent/30 transition-all">
                        Cek Perkiraan Jadwal Sekarang
                    </button>
                </form>
                <div class="text-center pt-2">
                    <button @click="openRegisterModal = true" class="text-xs font-bold text-pastel-blue-accent hover:text-pastel-blue-dark underline transition-colors">
                        Belum punya jadwal? Klik di sini untuk mengajukan pendaftaran jadwal mandiri &rarr;
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- MODAL POPUP STATUS & TIKET (MODERN, ELEGAN & PROFESIONAL) -->
    @if(session('checked_patient') || session('success'))
        @php 
            $p = session('checked_patient'); 
            $statusType = session('status_type');
            $isArr = is_array($p);
            
            $mrNumber = $isArr ? ($p['medical_record_number'] ?? 'Menunggu Panggilan') : ($p->medical_record_number ?? 'Menunggu Panggilan');
            $patName  = $isArr ? ($p['name'] ?? '-') : ($p->name ?? '-');
            
            // Ekstraksi ID dengan aman baik dari array maupun object
            $patientId = $isArr ? ($p['id'] ?? 0) : ($p->id ?? 0);
            
            $estRange = session('estimation_range') ?: $rentangTanggal;
            
            // Ambil nomor tiket atau buat format pendek dari ID pasien / pendaftaran
            $ticketNo = session('registration_id') ?? ($isArr ? ($p['ticket_number'] ?? ('REG-' . date('y') . str_pad($patientId, 4, '0', STR_PAD_LEFT))) : ($p->ticket_number ?? ('REG-' . date('y') . str_pad($patientId, 4, '0', STR_PAD_LEFT))));
            
            // Perbaikan: Hitung nomor urut berdasarkan ID pasien yang valid
            $queueNumber = $patientId > 0 ? \App\Models\Patient::where('id', '<=', $patientId)->count() : 1;
        @endphp
        <div x-data="{ openModal: true }" x-show="openModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-opacity" x-cloak>
            <div @click.away="openModal = false" class="bg-white rounded-3xl max-w-md w-full p-8 shadow-2xl border border-pastel-blue-light space-y-6 relative transform transition-all printable-ticket text-left">
                
                <button @click="openModal = false" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-pastel-blue-light hover:bg-pastel-blue-med text-pastel-blue-dark flex items-center justify-center font-bold text-xs transition-colors no-print">
                    ✕
                </button>

                <!-- Header RSUD -->
                <div class="text-center space-y-1.5 border-b border-stone-100 pb-5">
                    <div class="w-12 h-12 bg-pastel-blue-dark text-white rounded-2xl flex items-center justify-center mx-auto text-lg shadow-sm font-serif font-bold">RS</div>
                    <h3 class="font-serif text-xl font-bold text-pastel-blue-dark tracking-tight">RSUD Blambangan Banyuwangi</h3>
                    <p class="text-[11px] uppercase tracking-wider text-pastel-muted font-semibold">Bukti Resmi Status & Jadwal Antrean</p>
                </div>

                @if(session('success'))
                    <!-- KARTU NOMOR TIKET & URUTAN ANTREAN (Dibuat Besar Seimbang) -->
                    <div class="grid grid-cols-2 gap-3 bg-gradient-to-br from-pastel-blue to-white p-4 rounded-2xl border border-pastel-blue-accent/30 text-center shadow-xs">
                        <div class="p-2 border-r border-pastel-blue-light/60">
                            <span class="text-[9px] font-black uppercase tracking-widest text-pastel-blue-accent block">Nomor Tiket</span>
                            <div class="text-2xl sm:text-3xl font-extrabold font-mono text-pastel-blue-dark tracking-wide mt-1">{{ $ticketNo }}</div>
                        </div>
                        <div class="p-2">
                            <span class="text-[9px] font-black uppercase tracking-widest text-pastel-blue-accent block">No. Urut Antrean</span>
                            <div class="text-2xl sm:text-3xl font-extrabold font-mono text-pastel-blue-dark tracking-wide mt-1">#{{ str_pad($queueNumber, 3, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>

                    <!-- PESAN SOPAN & PROFESIONAL -->
                    <div class="space-y-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        <p class="font-semibold text-pastel-blue-dark">Yth. Bapak/Ibu <span class="capitalize">{{ $patName }}</span>,</p>
                        <p class="text-justify font-light text-stone-600">
                            Pendaftaran Anda telah berhasil dicatat dan masuk ke dalam sistem verifikasi kami. Perkiraan jadwal pelaksanaan tindakan medis Anda adalah pada rentang tanggal <strong class="text-pastel-blue-dark font-semibold">{{ $estRange }}</strong>.
                        </p>
                        <div class="p-3.5 bg-amber-50 rounded-xl border border-amber-100 text-xs text-amber-800 space-y-2 font-medium">
                            <p class="font-bold">ℹ️ Informasi Penting:</p>
                            <p>Mohon menunggu konfirmasi atau panggilan resmi dari petugas medis RSUD Blambangan. Simpan nomor tiket Anda untuk keperluan pengecekan.</p>
                            <p class="text-justify font-light text-stone-600">
                                Jika belum dihubungi oleh pihak RS, Anda dapat menghubungi nomor kontak pengaduan Cathlab di bawah ini.
                            </p>
                        </div>
                    </div>

                    <!-- KONTAK & TOMBOL AKSI -->
                    <div class="space-y-3 pt-2 no-print">
                        <div class="text-[11px] text-center text-stone-400 bg-slate-50 p-3 rounded-2xl border border-stone-100">
                            Pusat Layanan Informasi & Pengaduan: <br>
                            <h3 class="mt-1"><strong class="text-pastel-blue-dark font-mono text-base">+62 813-3628-9900</strong></h3>
                        </div>
                        <button onclick="window.print()" class="w-full py-3.5 bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-md flex items-center justify-center space-x-2">
                            <span>🖨️ Cetak Bukti Pendaftaran</span>
                        </button>
                        <button @click="openModal = false" class="w-full py-3 bg-stone-100 hover:bg-stone-200 text-stone-600 font-bold text-xs uppercase tracking-wider rounded-2xl transition-all">
                            Tutup & Mengerti
                        </button>
                    </div>

                @elseif($statusType === 'completed')
                    <!-- MODAL DAFTAR ULANG INSTAN -->
                    <div class="space-y-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        <p class="font-semibold text-pastel-blue-dark">Halo {{ session('honorific') }} <span class="capitalize">{{ $patName }}</span>,</p>
                        <p class="text-justify font-light text-stone-600">
                            Berdasarkan data rekam medis kami (<strong class="font-mono text-pastel-blue-dark">{{ $mrNumber }}</strong>), Anda tercatat sudah pernah menyelesaikan tindakan medis sebelumnya. 
                        </p>
                        <p class="text-justify font-light text-stone-600">
                            Apakah Anda ingin mendaftar kembali untuk tindakan baru dengan mudah tanpa harus mengisi ulang formulir dari awal?
                        </p>
                    </div>

                    <div class="space-y-2.5 pt-2 no-print">
                        <form action="{{ route('queue.reregister', $patientId) }}" method="POST">
                            @csrf
                            <button type="submit" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-md flex items-center justify-center space-x-2">
                                <span>✨ Ya, Daftar Ulang Sekarang</span>
                            </button>
                        </form>
                        <button @click="openModal = false" class="w-full py-3 bg-stone-100 hover:bg-stone-200 text-stone-600 font-bold text-xs uppercase tracking-wider rounded-2xl transition-all">
                            Batal
                        </button>
                    </div>

                @elseif($statusType === 'approved')
                    <!-- STATUS DISETUJUI -->
                    <div class="grid grid-cols-2 gap-3 bg-pastel-blue p-4 rounded-2xl border border-pastel-blue-light text-center">
                        <div class="p-1 border-r border-pastel-blue-light">
                            <span class="text-[9px] font-black uppercase tracking-widest text-pastel-blue-accent block">Tiket Aktif</span>
                            <div class="text-xl sm:text-2xl font-extrabold font-mono text-pastel-blue-dark mt-1">{{ $ticketNo }}</div>
                        </div>
                        <div class="p-1">
                            <span class="text-[9px] font-black uppercase tracking-widest text-pastel-blue-accent block">No. Urut</span>
                            <div class="text-xl sm:text-2xl font-extrabold font-mono text-pastel-blue-dark mt-1">#{{ str_pad($queueNumber, 3, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        <p class="font-semibold text-pastel-blue-dark">Yth. Bapak/Ibu <span class="capitalize">{{ $patName }}</span>,</p>
                        <p class="text-[11px] text-stone-400">No. Rekam Medis: <strong class="font-mono text-stone-600">{{ $mrNumber }}</strong></p>
                        <p class="text-justify font-light">
                            Pendaftaran Anda telah diverifikasi oleh tim admin rumah sakit. Estimasi jadwal waktu tunggu tindakan intervensi Anda adalah antara tanggal <strong class="text-pastel-blue-dark font-semibold">{{ session('estimation_range') }}</strong>.
                        </p>
                        <p class="text-justify font-light text-stone-600">
                            Jika belum dihubungi oleh pihak RS, Anda dapat menghubungi nomor kontak pengaduan di bawah ini.
                        </p>
                    </div>

                    <div class="text-[11px] text-center text-stone-400 bg-slate-50 p-3 rounded-2xl border border-stone-100">
                        Pusat Layanan Informasi & Pengaduan: <br>
                        <h3 class="mt-1"><strong class="text-pastel-blue-dark font-mono text-base">+62 813-3628-9900</strong></h3>
                    </div>

                    <div class="space-y-2.5 pt-2 no-print">
                        <button onclick="window.print()" class="w-full py-3.5 bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-md flex items-center justify-center space-x-2">
                            <span>🖨️ Cetak Tiket Antrean</span>
                        </button>
                        <button @click="openModal = false" class="w-full py-3 bg-stone-100 hover:bg-stone-200 text-stone-600 font-bold text-xs uppercase tracking-wider rounded-2xl transition-all">
                            Tutup
                        </button>
                    </div>
                @else
                    <!-- DEFAULT / STATUS UMUM -->
                    <div class="grid grid-cols-2 gap-3 bg-pastel-blue p-4 rounded-2xl border border-pastel-blue-light text-center">
                        <div class="p-1 border-r border-pastel-blue-light">
                            <span class="text-[9px] font-black uppercase tracking-widest text-pastel-blue-accent block">Nomor Tiket</span>
                            <div class="text-xl sm:text-2xl font-extrabold font-mono text-pastel-blue-dark mt-1">{{ $ticketNo }}</div>
                        </div>
                        <div class="p-1">
                            <span class="text-[9px] font-black uppercase tracking-widest text-pastel-blue-accent block">No. Urut</span>
                            <div class="text-xl sm:text-2xl font-extrabold font-mono text-pastel-blue-dark mt-1">#{{ str_pad($queueNumber, 3, '0', STR_PAD_LEFT) }}</div>
                        </div>
                    </div>

                    <div class="space-y-3 text-xs sm:text-sm text-stone-600 leading-relaxed">
                        <p class="font-semibold text-pastel-blue-dark">Yth. Bapak/Ibu <span class="capitalize">{{ $patName }}</span>,</p>
                        <p class="text-justify font-light">
                            Data Anda sudah terdaftar di sistem kami. Mohon menunggu informasi selanjutnya dari petugas. Perkiraan tanggal tindakan Anda berada pada rentang <strong class="text-pastel-blue-dark font-semibold">{{ $estRange }}</strong>.
                        </p>
                    </div>

                    <div class="space-y-2.5 pt-2 no-print">
                        <button onclick="window.print()" class="w-full py-3.5 bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-md flex items-center justify-center space-x-2">
                            <span>🖨️ Cetak Bukti Pendaftaran</span>
                        </button>
                        <button @click="openModal = false" class="w-full py-3 bg-stone-100 hover:bg-stone-200 text-stone-600 font-bold text-xs uppercase tracking-wider rounded-2xl transition-all">
                            Tutup
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- TEMPLATE TIKET UNTUK DIPRINT -->
    @if(session('checked_patient') || session('success'))
        @php
            $pTicket = session('checked_patient');
            $isArrTicket = is_array($pTicket);
            $printTicketId = $isArrTicket ? ($pTicket['id'] ?? 0) : ($pTicket->id ?? 0);
            $printTicketNo = session('registration_id') ?? ($isArrTicket ? ($pTicket['ticket_number'] ?? ('REG-' . date('y') . str_pad($printTicketId, 4, '0', STR_PAD_LEFT))) : ($pTicket->ticket_number ?? ('REG-' . date('y') . str_pad($printTicketId, 4, '0', STR_PAD_LEFT))));
            $printQueueNo = $printTicketId > 0 ? \App\Models\Patient::where('id', '<=', $printTicketId)->count() : 1;
        @endphp
        <div class="print-only-ticket" style="display: none;">
            <div style="border: 2px dashed #1D3557; padding: 25px; text-align: center; font-family: sans-serif; max-width: 350px; margin: 0 auto; color: #1D3557;">
                <h2 style="margin-bottom: 5px; font-size: 20px;">RSUD BLAMBANGAN</h2>
                <p style="font-size: 12px; margin-top: 0; color: #64748B;">Pusat Intervensi Terpadu</p>
                <hr style="border-top: 1px solid #E2ECF5; margin: 15px 0;">
                <h3 style="font-size: 15px; margin-bottom: 5px; color: #1D3557;">BUKTI PENDAFTARAN MANDIRI</h3>
                <p style="font-size: 11px; font-weight: bold; background: #E2ECF5; display: inline-block; padding: 4px 12px; border-radius: 20px; color: #1D3557;">STATUS: TERDAFTAR</p>
                
                <div style="margin: 20px 0; text-align: left; padding: 12px; background: #F8FAFC; border-radius: 10px; border: 1px solid #cbd5e1;">
                    <div style="display: flex; justify-content: space-between; gap: 10px;">
                        <div>
                            <p style="font-size: 10px; margin-bottom: 2px; color: #64748B;">NO. TIKET:</p>
                            <h2 style="font-size: 18px; font-weight: bold; font-family: monospace; margin: 0 0 8px 0; color: #1D3557;">{{ $printTicketNo }}</h2>
                        </div>
                        <div>
                            <p style="font-size: 10px; margin-bottom: 2px; color: #64748B;">NO. ANTREAN:</p>
                            <h2 style="font-size: 18px; font-weight: bold; font-family: monospace; margin: 0 0 8px 0; color: #1D3557;">#{{ str_pad($printQueueNo, 3, '0', STR_PAD_LEFT) }}</h2>
                        </div>
                    </div>
                    <p style="font-size: 11px; margin-top: 8px; margin-bottom: 0; border-top: 1px solid #e2e8f0; padding-top: 6px;"><strong>Estimasi Jadwal:</strong> <br><span style="font-size: 12px; font-weight: bold; color: #1D3557;">{{ $rentangTanggal }}</span></p>
                </div>
                
                <hr style="border-top: 1px solid #E2ECF5; margin: 15px 0;">
                <p style="font-size: 11px; margin-bottom: 2px;">Pusat Pengaduan / Informasi:</p>
                <p style="font-size: 13px; font-weight: bold; margin-top: 0;">+62 813-3628-9900</p>
                <p style="font-size: 10px; margin-top: 15px; color: #64748B; font-style: italic;">Harap simpan bukti ini saat berkunjung. Terima kasih.</p>
            </div>
        </div>
    @endif

    <!-- MODAL FORM PENDAFTARAN MANDIRI -->
    @if(!session('success') && !session('checked_patient'))
    <div x-show="openRegisterModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-md transition-opacity" x-cloak>
        <div @click.away="openRegisterModal = false" class="bg-white rounded-3xl max-w-2xl w-full p-6 sm:p-8 shadow-2xl border border-pastel-blue-light space-y-6 relative max-h-[90vh] overflow-y-auto text-left" x-data="publicForm()">
            
            <button @click="openRegisterModal = false" class="absolute top-5 right-5 w-8 h-8 rounded-full bg-pastel-blue-light hover:bg-pastel-blue-med text-pastel-blue-dark flex items-center justify-center font-bold text-xs transition-colors">
                ✕
            </button>

            @if(session('error') && (str_contains(session('error'), 'terdaftar') || str_contains(session('error'), 'mengajukan')))
                <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-xs sm:text-sm font-bold text-rose-600 text-center shadow-sm">
                    ⚠️ {{ session('error') }}
                </div>
            @endif
            
            @if($errors->any())
                <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl text-xs sm:text-sm font-bold text-amber-700 text-center shadow-sm">
                    Mohon periksa kembali isian form Anda. Pastikan semua kolom bertanda * terisi dengan benar.
                </div>
            @endif

            <div class="space-y-1">
                <h3 class="font-serif text-2xl font-bold text-pastel-blue-dark">Formulir Pengajuan Jadwal Mandiri</h3>
                <p class="text-xs text-pastel-muted">Isi data lengkap di bawah ini untuk dikirim ke sistem verifikasi rumah sakit.</p>
            </div>

            <form action="{{ route('public.register.store') }}" method="POST" class="space-y-5" id="publicRegistrationForm">
                @csrf
                <input type="hidden" name="confirmed" id="form_confirmed" value="0">

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="relative" x-data="{ open: false }">
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Sumber Rujukan *</label>
                        <input type="hidden" name="source" x-model="source" required>
                        <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm text-left font-medium text-pastel-blue-dark flex items-center justify-between focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs">
                            <span x-text="sourceLabel" :class="source ? 'text-pastel-blue-dark font-semibold' : 'text-stone-400'"></span>
                            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-xl border border-pastel-blue-light overflow-hidden py-1">
                            <div @click="source = 'mandiri'; sourceLabel = 'Mandiri / Pasien Sendiri'; open = false" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors">Mandiri / Pasien Sendiri</div>
                            <div @click="source = 'poli'; sourceLabel = 'Poliklinik RSUD Blambangan'; open = false" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors">Poliklinik RSUD Blambangan</div>
                            <div @click="source = 'rs_lain'; sourceLabel = 'Rumah Sakit Lain'; open = false" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors">Rumah Sakit Lain</div>
                        </div>
                    </div>

                    <div x-show="source === 'poli'" x-transition>
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Nomor Rekam Medis *</label>
                        <input type="text" name="medical_record_number" value="{{ old('medical_record_number') }}" :required="source === 'poli'" class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm font-medium text-pastel-blue-dark focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs" placeholder="RM-XXXXX">
                    </div>
                </div>

                <div x-show="source === 'rs_lain'" x-transition class="space-y-4 p-4 bg-pastel-blue/20 rounded-2xl border border-pastel-blue-light">
                    <div class="relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Pilih Rumah Sakit Perujuk *</label>
                        <input type="hidden" name="origin_hospital" x-model="hospital" :required="source === 'rs_lain'">
                        <button type="button" @click="hospitalOpen = !hospitalOpen" @click.away="hospitalOpen = false" class="w-full px-4 py-3.5 bg-white border border-pastel-blue-light rounded-2xl text-sm text-left font-medium text-pastel-blue-dark flex items-center justify-between focus:outline-none focus:border-pastel-blue-accent transition-all shadow-2xs">
                            <span x-text="hospitalLabel" :class="hospital ? 'text-pastel-blue-dark font-semibold' : 'text-stone-400'"></span>
                            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="hospitalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="hospitalOpen" x-transition class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-xl border border-pastel-blue-light overflow-hidden max-h-48 overflow-y-auto py-1">
                            <template x-for="hosp in banyuwangiHospitals" :key="hosp">
                                <div @click="hospital = hosp; hospitalLabel = hosp; hospitalOpen = false" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors" x-text="hosp"></div>
                            </template>
                        </div>
                    </div>
                    <div x-show="hospital === 'Lainnya'" x-transition>
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Masukan Nama Rumah Sakit *</label>
                        <input type="text" name="origin_hospital_custom" value="{{ old('origin_hospital_custom') }}" :required="hospital === 'Lainnya'" class="w-full px-4 py-3.5 bg-white border border-pastel-blue-light rounded-2xl text-sm font-medium text-pastel-blue-dark focus:outline-none focus:border-pastel-blue-accent transition-all shadow-2xs" placeholder="Ketik nama rumah sakit...">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Nama Lengkap Pasien (Sesuai KTP) *</label>
                    <input type="text" name="name" value="{{ old('name') }}" required class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm font-medium text-pastel-blue-dark focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs" placeholder="Masukkan nama lengkap pasien">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Tanggal Lahir *</label>
                        <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" required class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm font-medium text-pastel-blue-dark focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs">
                    </div>
                    <div class="relative" x-data="{ open: false }">
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Jenis Kelamin *</label>
                        <input type="hidden" name="gender" x-model="gender" required>
                        <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm text-left font-medium text-pastel-blue-dark flex items-center justify-between focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs">
                            <span x-text="genderLabel" :class="gender ? 'text-pastel-blue-dark font-semibold' : 'text-stone-400'"></span>
                            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-xl border border-pastel-blue-light overflow-hidden py-1">
                            <div @click="gender = 'L'; genderLabel = 'Laki-laki'; open = false" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors">Laki-laki</div>
                            <div @click="gender = 'P'; genderLabel = 'Perempuan'; open = false" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors">Perempuan</div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">No. Telepon / WhatsApp Pasien *</label>
                        <input type="text" name="patient_phone" value="{{ old('patient_phone') }}" required class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm font-medium text-pastel-blue-dark focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs" placeholder="08xxxxxxxxxx">
                    </div>
                    <div class="relative" x-data="{ open: false }">
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Jaminan / Pembiayaan *</label>
                        <input type="hidden" name="insurance_id" x-model="insuranceId" required>
                        <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm text-left font-medium text-pastel-blue-dark flex items-center justify-between focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs">
                            <span x-text="insuranceLabel" :class="insuranceId ? 'text-pastel-blue-dark font-semibold' : 'text-stone-400'"></span>
                            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-xl border border-pastel-blue-light overflow-hidden max-h-52 overflow-y-auto py-1">
                            @foreach(\App\Models\Insurance::all() as $ins)
                                <div @click="insuranceId = '{{ $ins->id }}'; insuranceLabel = '{{ $ins->name }}'; open = false" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors">{{ $ins->name }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="relative" x-data="{ open: false }">
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Kabupaten *</label>
                        <input type="hidden" name="regency" x-model="regency" required>
                        <button type="button" @click="open = !open" @click.away="open = false" class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm text-left font-medium text-pastel-blue-dark flex items-center justify-between focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs">
                            <span x-text="regency" class="text-pastel-blue-dark font-semibold"></span>
                            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="open" x-transition class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-xl border border-pastel-blue-light overflow-hidden py-1">
                            <template x-for="reg in ['Banyuwangi', 'Jember', 'Bondowoso', 'Situbondo', 'Lainnya']">
                                <div @click="selectRegency(reg); open = false" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors" x-text="reg"></div>
                            </template>
                        </div>
                    </div>

                    <div class="relative">
                        <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Kecamatan *</label>
                        <input type="hidden" name="district" x-model="district" required>
                        <button type="button" @click="districtOpen = !districtOpen" @click.away="districtOpen = false" class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm text-left font-medium text-pastel-blue-dark flex items-center justify-between focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs">
                            <span x-text="districtLabel" :class="district ? 'text-pastel-blue-dark font-semibold' : 'text-stone-400'"></span>
                            <svg class="w-4 h-4 text-stone-400 transition-transform duration-200" :class="districtOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                        </button>
                        <div x-show="districtOpen" x-transition class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-pastel-blue-light overflow-hidden py-1">
                            <div class="p-2.5 border-b border-stone-100 bg-stone-50" @click.stop>
                                <input type="text" x-model="districtSearch" placeholder="Cari kecamatan..." class="w-full px-3 py-2 bg-white border border-stone-200 rounded-xl text-xs text-stone-800 focus:outline-none focus:border-pastel-blue-accent">
                            </div>
                            <div class="max-h-52 overflow-y-auto">
                                <template x-for="dist in filteredDistricts" :key="dist">
                                    <div @click="selectDistrict(dist)" class="px-4 py-3 text-sm text-stone-700 hover:bg-pastel-blue/50 hover:text-pastel-blue-dark cursor-pointer transition-colors" x-text="dist"></div>
                                </template>
                                <div x-show="filteredDistricts.length === 0" class="px-4 py-3 text-xs text-stone-400 text-center">
                                    Kecamatan tidak ditemukan
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Alamat Lengkap Domisili *</label>
                    <textarea name="address" rows="2" required class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm font-medium text-pastel-blue-dark focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs" placeholder="Nama Jalan, Dusun, RT/RW, Desa/Kelurahan...">{{ old('address') }}</textarea>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-2">Pemeriksaan Penunjang Medis (Opsional)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach(\App\Models\SupportingOption::all() as $opt)
                            <label class="relative flex items-center p-3 bg-pastel-blue/30 border border-pastel-blue-light rounded-2xl cursor-pointer hover:border-pastel-blue-accent hover:bg-pastel-blue/60 transition-all text-xs font-medium text-pastel-blue-dark group">
                                <input type="checkbox" name="supporting_options[]" value="{{ $opt->id }}" class="w-4 h-4 text-pastel-blue-accent border-stone-300 rounded focus:ring-pastel-blue-accent">
                                <span class="ml-2.5">{{ $opt->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-pastel-muted mb-1.5">Keterangan / Catatan Tambahan (Opsional)</label>
                    <textarea name="notes" rows="2" class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl text-sm font-medium text-pastel-blue-dark focus:outline-none focus:border-pastel-blue-accent focus:bg-white transition-all shadow-2xs" placeholder="Riwayat keluhan medis singkat...">{{ old('notes') }}</textarea>
                </div>

                <div class="pt-2">
                    <button type="submit" class="w-full py-4 bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-md">
                        Kirim Pengajuan Pendaftaran Jadwal
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Script form Alpine.js dengan inisialisasi old values -->
    <script>
        function publicForm() {
            return {
                source: '{{ old('source', '') }}', 
                sourceLabel: '{{ old('source') == 'mandiri' ? 'Mandiri / Pasien Sendiri' : (old('source') == 'poli' ? 'Poliklinik RSUD Blambangan' : (old('source') == 'rs_lain' ? 'Rumah Sakit Lain' : '-- Pilih Sumber Rujukan --')) }}',
                gender: '{{ old('gender', '') }}', 
                genderLabel: '{{ old('gender') == 'L' ? 'Laki-laki' : (old('gender') == 'P' ? 'Perempuan' : '-- Pilih Jenis Kelamin --') }}',
                insuranceId: '{{ old('insurance_id', '') }}', 
                insuranceLabel: '{{ old('insurance_id') ? optional(\App\Models\Insurance::find(old('insurance_id')))->name ?? '-- Pilih Jaminan --' : '-- Pilih Jaminan --' }}',
                hospital: '{{ old('origin_hospital', '') }}', 
                hospitalLabel: '{{ old('origin_hospital', '-- Pilih Rumah Sakit --') }}', 
                hospitalOpen: false,
                banyuwangiHospitals: ["RSUD Genteng", "RS Al Huda Genteng", "RS Fatimah Banyuwangi", "RSU Yasmin Banyuwangi", "RS PKU Muhammadiyah Rogojampi", "RS Bhakti Husada Krikilan", "Lainnya"],
                regency: '{{ old('regency', 'Banyuwangi') }}', 
                district: '{{ old('district', '') }}', 
                districtLabel: '{{ old('district', '-- Pilih Kecamatan --') }}', 
                districtOpen: false, 
                districtSearch: '',
                districtsData: {
                    "Banyuwangi": ["Banyuwangi", "Kalipuro", "Giri", "Glagah", "Kabat", "Rogojampi", "Blimbingsari", "Srono", "Muncar", "Cluring", "Gambiran", "Tegalsari", "Mempuro", "Genteng", "Glenmore", "Kalibaru", "Sempu", "Songgon", "Singojuruh", "Licin", "Wongsorejo", "Pesanggaran", "Siliragung", "Bangorejo"],
                    "Jember": ["Jember Kota", "Patrang", "Kaliwates", "Sumbersari", "Batu Puteh", "Ajung", "Ambulu", "Jenggawah"],
                    "Bondowoso": ["Bondowoso", "Tamanan", "Wringin", "Prajekan", "Tenggarang"],
                    "Situbondo": ["Situbondo", "Panji", "Bungatan", "Kendit", "Asembagus"],
                    "Lainnya": ["Lainnya"]
                },
                get filteredDistricts() {
                    if (!this.regency || !this.districtsData[this.regency]) return [];
                    if (!this.districtSearch) return this.districtsData[this.regency];
                    return this.districtsData[this.regency].filter(d => d.toLowerCase().includes(this.districtSearch.toLowerCase()));
                },
                selectRegency(reg) {
                    this.regency = reg; this.district = ''; this.districtLabel = '-- Pilih Kecamatan --'; this.districtSearch = '';
                },
                selectDistrict(dist) {
                    this.district = dist; this.districtLabel = dist; this.districtOpen = false;
                }
            }
        }
    </script>

    <section class="parallax-section py-16 text-white my-12">
        <div class="max-w-7xl mx-auto px-6 text-center space-y-4">
            <span class="text-xs font-semibold text-pastel-sky uppercase tracking-widest bg-white/10 px-4 py-1.5 rounded-full backdrop-blur-md">Komitmen Pelayanan Global</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold tracking-tight">Siaga 24 Jam Menyelamatkan Jiwa dengan Standar Paripurna</h2>
            <p class="text-sm sm:text-base text-pastel-blue-light max-w-2xl mx-auto font-light leading-relaxed">
                Didukung oleh peralatan medis digital berteknologi tinggi dan integrasi penuh dengan jaminan kesehatan BPJS untuk masyarakat Banyuwangi dan sekitarnya.
            </p>
        </div>
    </section>

    <section id="layanan" class="py-16 max-w-7xl mx-auto px-6">
        <div class="space-y-3 mb-10 max-w-2xl">
            <span class="text-xs font-semibold text-pastel-blue-accent uppercase tracking-widest">Spesialisasi Klinis</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-pastel-blue-dark">3 Pilar Layanan Unggulan Terpadu</h2>
            <p class="text-sm text-pastel-muted">Menghadirkan penanganan multidisiplin berstandar tinggi untuk kasus kardiovaskular, neurologi, dan pencitraan medis.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="glass-card p-8 rounded-3xl shadow-xs hover:shadow-md transition-all duration-300 space-y-4 group bg-white">
                <div class="w-12 h-12 rounded-2xl bg-pastel-blue-light text-pastel-blue-dark flex items-center justify-center text-xl font-bold group-hover:bg-pastel-blue-accent group-hover:text-white transition-colors">🫀</div>
                <h3 class="font-serif text-xl font-bold text-pastel-blue-dark">Kardiovaskular & Cathlab</h3>
                <p class="text-sm text-pastel-muted leading-relaxed">Pemeriksaan angiografi koroner (CAG), pemasangan ring jantung (PCI), serta penanganan kegawatdaruratan serangan jantung akut 24/7.</p>
            </div>
            <div class="glass-card p-8 rounded-3xl shadow-xs hover:shadow-md transition-all duration-300 space-y-4 group bg-white">
                <div class="w-12 h-12 rounded-2xl bg-pastel-blue-light text-pastel-blue-accent flex items-center justify-center text-xl font-bold group-hover:bg-pastel-blue-dark group-hover:text-white transition-colors">🧠</div>
                <h3 class="font-serif text-xl font-bold text-pastel-blue-dark">Neurologi & Intervensi Saraf</h3>
                <p class="text-sm text-pastel-muted leading-relaxed">Layanan diagnostik dan terapeutik gangguan pembuluh darah otak (stroke, aneurisma) menggunakan prosedur canggih seperti DSA dan coiling.</p>
            </div>
            <div class="glass-card p-8 rounded-3xl shadow-xs hover:shadow-md transition-all duration-300 space-y-4 group bg-white">
                <div class="w-12 h-12 rounded-2xl bg-pastel-blue-light text-pastel-blue-dark flex items-center justify-center text-xl font-bold group-hover:bg-pastel-sky group-hover:text-pastel-blue-dark transition-colors">🩻</div>
                <h3 class="font-serif text-xl font-bold text-pastel-blue-dark">Radiologi Diagnostik & Intervensi</h3>
                <p class="text-sm text-pastel-muted leading-relaxed">Pencitraan medis presisi tinggi (X-ray, CT-Scan lanjutan, fluoroskopi) serta tindakan intervensi non-bedah panduan citra radiologi.</p>
            </div>
        </div>
    </section>

    <section id="alur" class="py-16 max-w-7xl mx-auto px-6 space-y-10">
        <div class="text-center max-w-2xl mx-auto space-y-3">
            <span class="text-xs font-semibold text-pastel-blue-accent uppercase tracking-widest">Langkah Cepat & Terstruktur</span>
            <h2 class="font-serif text-3xl sm:text-4xl font-bold text-pastel-blue-dark">Alur Pelayanan Pasien Terpadu</h2>
            <p class="text-sm text-pastel-muted">Prosedur penanganan rujukan dan poli spesialis menuju ruang tindakan intervensi.</p>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="glass-card p-6 rounded-3xl space-y-3 bg-white">
                <span class="text-pastel-blue-accent font-serif font-bold text-3xl">01</span>
                <h3 class="font-bold text-pastel-blue-dark text-sm">Pemeriksaan Poliklinik</h3>
                <p class="text-xs text-pastel-muted leading-relaxed">Konsultasi awal di Poli Jantung, Poli Saraf, atau Poli Terkait di RSUD Blambangan.</p>
            </div>
            <div class="glass-card p-6 rounded-3xl space-y-3 bg-white">
                <span class="text-pastel-blue-accent font-serif font-bold text-3xl">02</span>
                <h3 class="font-bold text-pastel-blue-dark text-sm">Skrining & Penjadwalan</h3>
                <p class="text-xs text-pastel-muted leading-relaxed">Evaluasi klinis oleh dokter spesialis dan penentuan jadwal tindakan intervensi.</p>
            </div>
            <div class="glass-card p-6 rounded-3xl space-y-3 bg-white">
                <span class="text-pastel-blue-accent font-serif font-bold text-3xl">03</span>
                <h3 class="font-bold text-pastel-blue-dark text-sm">Konfirmasi & Persiapan</h3>
                <p class="text-xs text-pastel-muted leading-relaxed">Verifikasi data administrasi, persiapan penunjang, dan jaminan pembiayaan BPJS.</p>
            </div>
            <div class="glass-card p-6 rounded-3xl space-y-3 bg-white">
                <span class="text-pastel-blue-accent font-serif font-bold text-3xl">04</span>
                <h3 class="font-bold text-pastel-blue-dark text-sm">Pelaksanaan Tindakan</h3>
                <p class="text-xs text-pastel-muted leading-relaxed">Prosedur kateterisasi / neuro-intervensi / radiologi oleh tim multidisiplin.</p>
            </div>
        </div>
    </section>

    <section id="kontak" class="py-16 max-w-4xl mx-auto px-6">
        <div class="glass-card p-8 sm:p-12 rounded-3xl shadow-xl shadow-blue-900/10 space-y-8 bg-white">
            <div class="text-center space-y-3">
                <span class="text-xs font-semibold text-pastel-blue-accent uppercase tracking-widest">Pusat Layanan Informasi</span>
                <h2 class="font-serif text-3xl font-bold text-pastel-blue-dark">Hubungi Instalasi Terpadu RSUD Blambangan</h2>
                <p class="text-sm text-pastel-muted">Sampaikan pertanyaan atau koordinasi jadwal pelayanan kepada tim administrasi kami.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-2">
                <div class="space-y-4 bg-pastel-blue p-6 rounded-2xl border border-pastel-blue-light">
                    <h3 class="font-serif font-bold text-pastel-blue-dark text-lg">Informasi Kontak</h3>
                    <div class="space-y-3 text-sm text-pastel-muted">
                        <p><strong class="text-pastel-blue-dark">Alamat:</strong> Jl. Letkol Istiqlah No. 49, Singonegaran, Kec. Banyuwangi, Kab. Banyuwangi, Jawa Timur</p>
                        <p><strong class="text-pastel-blue-dark">Telepon:</strong> (0333) 421118</p>
                        <p><strong class="text-pastel-blue-dark">Jam Operasional:</strong> Senin - Sabtu (Jam Kerja) & Layanan Darurat 24 Jam</p>
                    </div>
                </div>
                <form class="space-y-4" onsubmit="event.preventDefault(); Swal.fire({ title: 'Terkirim!', text: 'Terima kasih. Pesan Anda telah dikirim ke bagian administrasi.', icon: 'success', confirmButtonColor: '#457B9D' });">
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-pastel-muted mb-2">Nama Pasien / Keluarga</label>
                        <input type="text" required class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl focus:outline-none focus:border-pastel-blue-accent text-sm bg-white" placeholder="Masukkan nama...">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-pastel-muted mb-2">Nomor Rekam Medis (Opsional)</label>
                        <input type="text" class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl focus:outline-none focus:border-pastel-blue-accent text-sm bg-white" placeholder="Contoh: 352xxx">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold uppercase tracking-wider text-pastel-muted mb-2">Pesan / Pertanyaan</label>
                        <textarea rows="3" required class="w-full px-4 py-3.5 bg-pastel-blue/40 border border-pastel-blue-light rounded-2xl focus:outline-none focus:border-pastel-blue-accent text-sm bg-white" placeholder="Tuliskan pertanyaan seputar jadwal atau layanan (Jantung / Neuro / Radiologi)..."></textarea>
                    </div>
                    <button type="submit" class="w-full bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white font-semibold py-3.5 rounded-2xl text-sm transition-all shadow-sm">
                        Kirim Pesan Informasi
                    </button>
                </form>
            </div>
        </div>
    </section>

    <footer class="bg-pastel-blue-dark text-white/85 py-16">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-10 text-sm">
            <div class="space-y-3">
                <h3 class="font-serif text-xl font-bold text-white tracking-wide">RSUD Blambangan</h3>
                <p class="text-xs text-white/70 leading-relaxed">Instalasi Terpadu (Jantung, Neuro, & Radiologi) Kabupaten Banyuwangi. Mengutamakan ketepatan, keselamatan, dan pelayanan penuh empati.</p>
            </div>
            <div class="space-y-3">
                <h4 class="font-semibold text-white uppercase tracking-wider text-xs">Lokasi & Wilayah</h4>
                <p class="text-xs text-white/70">Banyuwangi, Jawa Timur</p>
                <p class="text-xs text-white/70">Email: rsudblambangan@banyuwangikab.go.id</p>
            </div>
            <div class="space-y-3">
                <h4 class="font-semibold text-white uppercase tracking-wider text-xs">Gawat Darurat</h4>
                <p class="text-xs text-white/70">IGD & Tim Intervensi Siaga 24/7</p>
                <p class="text-xs text-white/70">Terintegrasi rujukan daerah.</p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 border-t border-white/10 mt-12 pt-6 text-center text-xs text-white/50">
            &copy; 2026 Instalasi Terpadu RSUD Blambangan Banyuwangi. All Rights Reserved.
        </div>
    </footer>

    <!-- SCRIPT SWEETALERT UNTUK KONFIRMASI PENDAFTARAN ULANG PASIEN LAMA -->
    @if(session('need_confirmation'))
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            Swal.fire({
                title: 'Konfirmasi Pendaftaran Ulang',
                text: "{{ session('confirmation_message') }}",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Daftar Lagi',
                cancelButtonText: 'Batal',
                confirmButtonColor: '#457B9D',
                cancelButtonColor: '#1D3557',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    let targetForm = document.getElementById('publicRegistrationForm');
                    if(targetForm) {
                        document.getElementById('form_confirmed').value = '1';
                        targetForm.submit();
                    } else {
                        let hiddenInput = document.createElement('input');
                        hiddenInput.type = 'hidden';
                        hiddenInput.name = 'confirmed';
                        hiddenInput.value = '1';
                        
                        let fallbackForm = document.createElement('form');
                        fallbackForm.method = 'POST';
                        fallbackForm.action = "{{ route('public.register.store') }}";
                        
                        let csrfToken = document.createElement('input');
                        csrfToken.type = 'hidden';
                        csrfToken.name = '_token';
                        csrfToken.value = '{{ csrf_token() }}';
                        
                        fallbackForm.appendChild(csrfToken);
                        fallbackForm.appendChild(hiddenInput);
                        document.body.appendChild(fallbackForm);
                        fallbackForm.submit();
                    }
                }
            });
        });
    </script>
    @endif
</body>
</html>
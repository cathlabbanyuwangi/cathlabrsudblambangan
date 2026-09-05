<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir Pengajuan Pendaftaran Mandiri | RSUD Blambangan</title>

    <link rel="icon" type="image/png" href="{{ asset('images/IMGLOGO.png') }}">
    
    <!-- Tailwind CSS & Alpine.js CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
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
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Playfair+Display:wght@600;700&display=swap');
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
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(69, 123, 157, 0.15);
        }
    </style>
</head>
<body class="bg-pastel-blue antialiased min-h-screen py-10 px-4 sm:px-6">

    <div class="max-w-3xl mx-auto space-y-6">
        
        <!-- HEADER KEMBALI -->
        <div class="flex items-center justify-between">
            <a href="{{ url('/') }}" class="inline-flex items-center space-x-2 px-4 py-2 bg-white hover:bg-pastel-blue-light text-pastel-blue-dark font-bold text-xs uppercase tracking-wider rounded-2xl transition-all border border-pastel-blue-accent/30 shadow-xs">
                <span>← Kembali ke Beranda</span>
            </a>
            <span class="text-xs text-pastel-muted font-medium">Instalasi Terpadu RSUD Blambangan</span>
        </div>

        @if(session('error'))
        <div class="p-4 bg-rose-50 border border-rose-100 rounded-2xl text-xs sm:text-sm font-bold text-rose-600 shadow-sm flex items-center gap-2">
            <span>⚠️</span> <span>{{ session('error') }}</span>
        </div>
        @endif
        
        @if($errors->any())
        <div class="p-4 bg-amber-50 border border-amber-100 rounded-2xl text-xs sm:text-sm font-bold text-amber-700 shadow-sm flex items-center gap-2">
            <span>⚠️</span> <span>Mohon periksa kembali isian form Anda. Pastikan semua kolom bertanda * terisi dengan benar.</span>
        </div>
        @endif

        <!-- KARTU FORMULIR UTAMA -->
        <div class="glass-card p-6 sm:p-10 rounded-3xl shadow-2xl space-y-6 bg-white border border-pastel-blue-accent/20" x-data="publicForm()">
            <div class="space-y-1.5 border-b border-pastel-blue-light pb-5">
                <span class="px-3 py-1 bg-pastel-blue-light text-pastel-blue-dark font-bold text-[10px] rounded-full uppercase tracking-widest border border-pastel-blue-accent/20">
                    Portal Pendaftaran Mandiri
                </span>
                <h2 class="font-serif text-2xl sm:text-3xl font-bold text-pastel-blue-dark tracking-tight">Formulir Pengajuan Jadwal Mandiri</h2>
                <p class="text-xs sm:text-sm text-pastel-muted">Isi data lengkap di bawah ini untuk dikirim ke sistem verifikasi rumah sakit.</p>
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
                    <button type="submit" class="w-full py-4 bg-pastel-blue-accent hover:bg-pastel-blue-dark text-white font-bold text-xs uppercase tracking-wider rounded-2xl transition-all shadow-md cursor-pointer">
                        Kirim Pengajuan Pendaftaran Jadwal
                    </button>
                </div>
            </form>
        </div>

    </div>

    <!-- Script Alpine.js untuk Form -->
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
                    }
                }
            });
        });
    </script>
    @endif

</body>
</html>
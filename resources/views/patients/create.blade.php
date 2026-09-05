<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-slate-800">
                    {{ __('Pendaftaran Pasien Baru') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Lengkapi formulir registrasi rekam medis dan penunjang pasien.</p>
            </div>
            <a href="{{ route('patients.index') }}" class="inline-flex items-center justify-center px-4 py-2.5 bg-white border border-slate-200/80 rounded-2xl font-medium text-xs text-slate-600 hover:bg-slate-50 hover:text-slate-900 shadow-xs transition-all">
                &larr; Kembali ke Daftar Pasien
            </a>
        </div>
    </x-slot>

    <div class="py-8" x-data="patientForm()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('patients.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- SECTION 1: ADMINISTRASI -->
                <div class="relative z-40 bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-6 sm:p-8">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-inner">
                            1
                        </div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Jalur Masuk & Administrasi</h3>
                            <p class="text-xs text-slate-400">Tentukan asal sumber rujukan dan nomor rekam medis.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Custom Select: Sumber Pasien -->
                        <div class="relative" x-data="{ open: false }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Sumber Pasien <span class="text-rose-500">*</span></label>
                            <input type="hidden" name="source" x-model="source" required>
                            
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-left text-slate-800 flex items-center justify-between focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                <span x-text="sourceLabel" :class="source ? 'text-slate-800 font-medium' : 'text-slate-400'"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-1">
                                <div @click="selectSource('poli', 'Poliklinik (Admin Poli)'); open = false" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors">Poliklinik (Admin Poli)</div>
                                <div @click="selectSource('rs_lain', 'Rumah Sakit Lain (Admin Cathlab)'); open = false" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors">Rumah Sakit Lain (Admin Cathlab)</div>
                                <div @click="selectSource('mandiri', 'Mandiri (Admin Cathlab)'); open = false" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors">Mandiri (Admin Cathlab)</div>
                            </div>
                            <x-input-error :messages="$errors->get('source')" class="mt-1.5" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nomor Rekam Medis (RM)</label>
                            <input type="text" name="medical_record_number" value="{{ old('medical_record_number') }}" placeholder="Contoh: RM-001234 (Opsional)" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" />
                            <x-input-error :messages="$errors->get('medical_record_number')" class="mt-1.5" />
                        </div>

                        <!-- DROPDOWN & INPUT TAMBAHAN RUMAH SAKIT RUJUKAN -->
                        <div class="md:col-span-2 space-y-4" x-show="source === 'rs_lain'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pilih Rumah Sakit Perujuk (Banyuwangi) <span class="text-rose-500">*</span></label>
                                <input type="hidden" name="origin_hospital" x-model="originHospital" :required="source === 'rs_lain'">
                                
                                <div class="relative">
                                    <button type="button" @click="hospitalOpen = !hospitalOpen" @click.away="hospitalOpen = false" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-left text-slate-800 flex items-center justify-between focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                        <span x-text="originHospitalLabel" :class="originHospital ? 'text-slate-800 font-medium' : 'text-slate-400'"></span>
                                        <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="hospitalOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    <div x-show="hospitalOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 max-h-60 overflow-y-auto py-1">
                                        <template x-for="hos in hospitalsBanyuwangi" :key="hos">
                                            <div @click="selectHospital(hos)" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors" x-text="hos"></div>
                                        </template>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('origin_hospital')" class="mt-1.5" />
                            </div>

                            <!-- INPUT MANUAL KETIKA PILIH "Klinik / RS Lainnya" -->
                            <div x-show="isCustomHospital" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="space-y-2 pt-1">
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nama Rumah Sakit / Klinik Lainnya <span class="text-rose-500">*</span></label>
                                <input type="text" name="origin_hospital_custom" x-model="originHospitalCustom" :required="isCustomHospital" placeholder="Ketik nama rumah sakit atau klinik rujukan..." class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" />
                                <x-input-error :messages="$errors->get('origin_hospital_custom')" class="mt-1.5" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: IDENTITAS -->
                <div class="relative z-30 bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-6 sm:p-8">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-inner">2</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Identitas & Kontak Pasien</h3>
                            <p class="text-xs text-slate-400">Informasi personal dan pembiayaan jaminan kesehatan.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nama Lengkap Sesuai KTP <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name') }}" placeholder="Masukkan nama lengkap pasien" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tanggal Lahir <span class="text-rose-500">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth') }}" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" required />
                            <x-input-error :messages="$errors->get('date_of_birth')" class="mt-1.5" />
                        </div>

                        <!-- Custom Select: Jenis Kelamin -->
                        <div class="relative" x-data="{ open: false, selected: '{{ old('gender', '') }}', label: '{{ old('gender') == 'L' ? 'Laki-laki' : (old('gender') == 'P' ? 'Perempuan' : '-- Pilih --') }}' }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <input type="hidden" name="gender" x-model="selected" required>
                            
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-left text-slate-800 flex items-center justify-between focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                <span x-text="label" :class="selected ? 'text-slate-800 font-medium' : 'text-slate-400'"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-1">
                                <div @click="selected = 'L'; label = 'Laki-laki'; open = false" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors">Laki-laki</div>
                                <div @click="selected = 'P'; label = 'Perempuan'; open = false" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors">Perempuan</div>
                            </div>
                            <x-input-error :messages="$errors->get('gender')" class="mt-1.5" />
                        </div>

                        <!-- Custom Select: Jaminan / Pembiayaan -->
                        <div class="relative" x-data="{ open: false, selected: '{{ old('insurance_id', '') }}', label: '{{ old('insurance_id') ? optional($insurances->firstWhere('id', old('insurance_id')))->name : '-- Pilih Jaminan --' }}' }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jaminan / Pembiayaan <span class="text-rose-500">*</span></label>
                            <input type="hidden" name="insurance_id" x-model="selected" required>
                            
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-left text-slate-800 flex items-center justify-between focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                <span x-text="label" :class="selected ? 'text-slate-800 font-medium' : 'text-slate-400'"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden max-h-60 overflow-y-auto py-1">
                                @foreach($insurances as $ins)
                                    <div @click="selected = '{{ $ins->id }}'; label = '{{ $ins->name }}'; open = false" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors">{{ $ins->name }}</div>
                                @endforeach
                            </div>
                            <x-input-error :messages="$errors->get('insurance_id')" class="mt-1.5" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">No. Telepon Pasien</label>
                            <input type="text" name="patient_phone" value="{{ old('patient_phone') }}" placeholder="08xxxxxxxxxx" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" />
                            <x-input-error :messages="$errors->get('patient_phone')" class="mt-1.5" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">No. Telepon Keluarga</label>
                            <input type="text" name="family_phone" value="{{ old('family_phone') }}" placeholder="08xxxxxxxxxx" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" />
                            <x-input-error :messages="$errors->get('family_phone')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: WILAYAH -->
                <div class="relative z-20 bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-6 sm:p-8">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-inner">3</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Wilayah & Alamat Domisili</h3>
                            <p class="text-xs text-slate-400">Pilih kabupaten, kecamatan, serta detail alamat tinggal.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="relative" x-data="{ open: false }">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kabupaten <span class="text-rose-500">*</span></label>
                            <input type="hidden" name="regency" x-model="regency" required>
                            
                            <button type="button" @click="open = !open" @click.away="open = false" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-left text-slate-800 flex items-center justify-between focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                <span x-text="regencyLabel" :class="regency ? 'text-slate-800 font-medium' : 'text-slate-400'"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="open" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-1">
                                <template x-for="reg in ['Banyuwangi', 'Jember', 'Bondowoso', 'Situbondo', 'Lainnya']">
                                    <div @click="selectRegency(reg); open = false" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors" x-text="reg"></div>
                                </template>
                            </div>
                            <x-input-error :messages="$errors->get('regency')" class="mt-1.5" />
                        </div>

                        <div class="relative" x-data>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kecamatan <span class="text-rose-500">*</span></label>
                            <input type="hidden" name="district" x-model="district" required>
                            
                            <button type="button" @click="if(regency) districtOpen = !districtOpen" @click.away="districtOpen = false" class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-left text-slate-800 flex items-center justify-between focus:outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">
                                <span x-text="districtLabel" :class="district ? 'text-slate-800 font-medium' : 'text-slate-400'"></span>
                                <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="districtOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>

                            <div x-show="districtOpen" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-1">
                                <div class="p-2.5 border-b border-slate-100 bg-slate-50/50" @click.stop>
                                    <div class="relative">
                                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400 text-xs">🔍</span>
                                        <input type="text" x-model="districtSearch" placeholder="Cari kecamatan..." class="w-full pl-8 pr-3 py-2 bg-white border border-slate-200 rounded-xl text-xs text-slate-800 focus:outline-none focus:border-indigo-500">
                                    </div>
                                </div>
                                <div class="max-h-52 overflow-y-auto">
                                    <template x-for="dist in filteredDistricts" :key="dist">
                                        <div @click="selectDistrict(dist)" class="px-4 py-3 text-sm text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer transition-colors" x-text="dist"></div>
                                    </template>
                                    <div x-show="filteredDistricts.length === 0" class="px-4 py-3 text-xs text-slate-400 text-center">
                                        Kecamatan tidak ditemukan
                                    </div>
                                </div>
                            </div>
                            <x-input-error :messages="$errors->get('district')" class="mt-1.5" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Alamat Lengkap <span class="text-rose-500">*</span></label>
                            <textarea name="address" rows="3" placeholder="Nama Jalan, Dusun, RT/RW, Desa/Kelurahan..." class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all" required>{{ old('address') }}</textarea>
                            <x-input-error :messages="$errors->get('address')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: PENUNJANG -->
                <div class="relative z-10 bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-6 sm:p-8">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-inner">4</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Pemeriksaan Penunjang Medis</h3>
                            <p class="text-xs text-slate-400">Pilih opsi pemeriksaan penunjang yang diperlukan.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @foreach($supportingOptions as $opt)
                            <label class="relative flex items-center p-4 bg-slate-50/60 border border-slate-200/80 rounded-2xl cursor-pointer hover:border-indigo-500 hover:bg-indigo-50/20 transition-all group">
                                <input type="checkbox" name="supporting_options[]" value="{{ $opt->id }}" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
                                    {{ (is_array(old('supporting_options')) && in_array($opt->id, old('supporting_options'))) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-slate-700 group-hover:text-slate-900">{{ $opt->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- SECTION 5: PRIORITAS & KETERANGAN -->
                <div class="relative z-0 bg-white/90 backdrop-blur-xl rounded-3xl border border-slate-100 shadow-xl shadow-slate-200/40 p-6 sm:p-8">
                    <div class="flex items-center space-x-3 mb-6 pb-4 border-b border-slate-100">
                        <div class="w-9 h-9 rounded-2xl bg-indigo-50 flex items-center justify-center text-indigo-600 font-bold text-xs shadow-inner">5</div>
                        <div>
                            <h3 class="text-base font-bold text-slate-800">Catatan & Status Prioritas</h3>
                            <p class="text-xs text-slate-400">Atur jika pasien memerlukan penanganan darurat/segera.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="flex items-center justify-between p-5 bg-slate-50/60 border border-slate-200/80 rounded-2xl">
                            <div>
                                <span class="block text-sm font-bold text-slate-800">Pasien Prioritas / Diutamakan</span>
                                <span class="block text-xs text-slate-500 mt-0.5">Aktifkan tombol ini jika pasien memerlukan antrean atau penanganan khusus.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_priority" value="1" class="sr-only peer" {{ old('is_priority') ? 'checked' : '' }}>
                                <div class="w-12 h-7 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all shadow-inner peer-checked:bg-rose-500"></div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Keterangan / Catatan Tambahan (Opsional)</label>
                            <textarea name="notes" rows="3" placeholder="Masukkan catatan khusus terkait kondisi atau riwayat pasien..." class="w-full bg-slate-50/60 border border-slate-200/80 rounded-2xl px-4 py-3.5 text-sm text-slate-800 focus:bg-white focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all">{{ old('notes') }}</textarea>
                            <x-input-error :messages="$errors->get('notes')" class="mt-1.5" />
                        </div>
                    </div>
                </div>

                <!-- TOMBOL AKSI -->
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a href="{{ route('patients.index') }}" class="px-6 py-3.5 rounded-2xl border border-slate-200/80 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition-all">
                        Batal
                    </a>
                    <button type="submit" class="px-7 py-3.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-sm font-semibold shadow-lg shadow-indigo-600/25 transition-all">
                        Simpan Pendaftaran Pasien
                    </button>
                </div>

            </form>

        </div>
    </div>

    <!-- Alpine.js State Management -->
    <script>
        function patientForm() {
            return {
                // Sumber Pasien
                source: '{{ old('source', '') }}',
                sourceLabel: '{{ old('source') == 'poli' ? 'Poliklinik (Admin Poli)' : (old('source') == 'rs_lain' ? 'Rumah Sakit Lain (Admin Cathlab)' : (old('source') == 'mandiri' ? 'Mandiri (Admin Cathlab)' : '-- Pilih Sumber Pasien --')) }}',
                
                // Rumah Sakit Rujukan Banyuwangi
                originHospital: '{{ old('origin_hospital', '') }}',
                originHospitalLabel: '{{ old('origin_hospital', '-- Pilih Rumah Sakit Perujuk --') }}',
                originHospitalCustom: '{{ old('origin_hospital_custom', '') }}',
                isCustomHospital: {{ old('origin_hospital') == 'Klinik / RS Lainnya' ? 'true' : 'false' }},
                hospitalOpen: false,

                hospitalsBanyuwangi: [
                    "RSUD Blambangan",
                    "RSUD Genteng",
                    "RSUD Tamberejo",
                    "RS Al Huda Gambiran",
                    "RS Islam Fatimah Banyuwangi",
                    "RS Yasmin Banyuwangi",
                    "RS PKU Muhammadiyah Rogojampi",
                    "RS Bhayangkara Banyuwangi",
                    "RS Graha Medika Banyuwangi",
                    "Klinik / RS Lainnya"
                ],

                selectSource(val, label) {
                    this.source = val;
                    this.sourceLabel = label;
                    if (val !== 'rs_lain') {
                        this.originHospital = '';
                        this.originHospitalLabel = '-- Pilih Rumah Sakit Perujuk --';
                        this.originHospitalCustom = '';
                        this.isCustomHospital = false;
                    }
                },

                selectHospital(hos) {
                    this.originHospital = hos;
                    this.originHospitalLabel = hos;
                    this.hospitalOpen = false;
                    if (hos === 'Klinik / RS Lainnya') {
                        this.isCustomHospital = true;
                    } else {
                        this.isCustomHospital = false;
                        this.originHospitalCustom = '';
                    }
                },

                // Wilayah
                regency: '{{ old('regency', '') }}',
                regencyLabel: '{{ old('regency', '-- Pilih Kabupaten --') }}',
                
                district: '{{ old('district', '') }}',
                districtLabel: '{{ old('district', old('regency') ? '-- Pilih Kecamatan --' : '-- Pilih Kabupaten Terlebih Dahulu --') }}',
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
                    this.regency = reg;
                    this.regencyLabel = reg;
                    this.district = '';
                    this.districtLabel = '-- Pilih Kecamatan --';
                    this.districtSearch = '';
                },

                selectDistrict(dist) {
                    this.district = dist;
                    this.districtLabel = dist;
                    this.districtOpen = false;
                }
            }
        }
    </script>
</x-app-layout>
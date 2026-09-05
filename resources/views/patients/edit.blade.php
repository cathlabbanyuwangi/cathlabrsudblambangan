<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold tracking-tight text-slate-800">
                    {{ __('Edit Data Pasien') }}
                </h2>
                <p class="text-sm text-slate-500 mt-0.5">Perbarui informasi rekam medis dan penunjang pasien.</p>
            </div>
            <a href="{{ route('patients.index') }}" class="inline-flex items-center justify-center px-4 py-2 bg-white border border-slate-200 rounded-xl font-medium text-xs text-slate-600 hover:bg-slate-50 shadow-sm transition-all">
                &larr; Kembali
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <form action="{{ route('patients.update', $patient->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- KARTU 1: ADMINISTRASI -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-base font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100">1. Jalur Masuk & Administrasi</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Sumber Pasien <span class="text-rose-500">*</span></label>
                            <select name="source" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" required>
                                <option value="poli" {{ old('source', $patient->source) == 'poli' ? 'selected' : '' }}>Poliklinik (Admin Poli)</option>
                                <option value="rs_lain" {{ old('source', $patient->source) == 'rs_lain' ? 'selected' : '' }}>Rumah Sakit Lain (Admin Cathlab)</option>
                                <option value="mandiri" {{ old('source', $patient->source) == 'mandiri' ? 'selected' : '' }}>Mandiri (Admin Cathlab)</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nomor Rekam Medis (RM)</label>
                            <input type="text" name="medical_record_number" value="{{ old('medical_record_number', $patient->medical_record_number) }}" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" />
                        </div>
                    </div>
                </div>

                <!-- KARTU 2: IDENTITAS -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-base font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100">2. Identitas & Kontak Pasien</h3>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="md:col-span-3">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Nama Lengkap Sesuai KTP <span class="text-rose-500">*</span></label>
                            <input type="text" name="name" value="{{ old('name', $patient->name) }}" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" required />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Tanggal Lahir <span class="text-rose-500">*</span></label>
                            <input type="date" name="date_of_birth" value="{{ old('date_of_birth', $patient->date_of_birth) }}" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" required />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jenis Kelamin <span class="text-rose-500">*</span></label>
                            <select name="gender" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" required>
                                <option value="L" {{ old('gender', $patient->gender) == 'L' ? 'selected' : '' }}>Laki-laki</option>
                                <option value="P" {{ old('gender', $patient->gender) == 'P' ? 'selected' : '' }}>Perempuan</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Jaminan / Pembiayaan <span class="text-rose-500">*</span></label>
                            <select name="insurance_id" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" required>
                                @foreach($insurances as $ins)
                                    <option value="{{ $ins->id }}" {{ old('insurance_id', $patient->insurance_id) == $ins->id ? 'selected' : '' }}>{{ $ins->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">No. Telepon Pasien</label>
                            <input type="text" name="patient_phone" value="{{ old('patient_phone', $patient->patient_phone) }}" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" />
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">No. Telepon Keluarga</label>
                            <input type="text" name="family_phone" value="{{ old('family_phone', $patient->family_phone) }}" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" />
                        </div>
                    </div>
                </div>

                <!-- KARTU 3: WILAYAH -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-base font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100">3. Wilayah & Alamat Domisili</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kabupaten <span class="text-rose-500">*</span></label>
                            <select id="regency" name="regency" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" required onchange="updateDistricts()">
                                <option value="Banyuwangi" {{ old('regency', $patient->regency) == 'Banyuwangi' ? 'selected' : '' }}>Banyuwangi</option>
                                <option value="Jember" {{ old('regency', $patient->regency) == 'Jember' ? 'selected' : '' }}>Jember</option>
                                <option value="Bondowoso" {{ old('regency', $patient->regency) == 'Bondowoso' ? 'selected' : '' }}>Bondowoso</option>
                                <option value="Situbondo" {{ old('regency', $patient->regency) == 'Situbondo' ? 'selected' : '' }}>Situbondo</option>
                                <option value="Lainnya" {{ old('regency', $patient->regency) == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Kecamatan <span class="text-rose-500">*</span></label>
                            <select id="district" name="district" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" required>
                                <option value="{{ $patient->district }}">{{ $patient->district }}</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Alamat Lengkap <span class="text-rose-500">*</span></label>
                            <textarea name="address" rows="3" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500" required>{{ old('address', $patient->address) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- KARTU 4: PENUNJANG -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-base font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100">4. Pemeriksaan Penunjang Medis</h3>

                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-4">
                        @php $selectedOptions = $patient->supportingOptions->pluck('id')->toArray(); @endphp
                        @foreach($supportingOptions as $opt)
                            <label class="relative flex items-center p-4 bg-slate-50/50 border border-slate-200 rounded-xl cursor-pointer hover:border-indigo-500">
                                <input type="checkbox" name="supporting_options[]" value="{{ $opt->id }}" class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500"
                                    {{ in_array($opt->id, old('supporting_options', $selectedOptions)) ? 'checked' : '' }}>
                                <span class="ml-3 text-sm font-medium text-slate-700">{{ $opt->name }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <!-- KARTU 5: PRIORITAS & KETERANGAN -->
                <div class="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 sm:p-8">
                    <h3 class="text-base font-bold text-slate-800 mb-6 pb-4 border-b border-slate-100">5. Catatan & Status Prioritas</h3>

                    <div class="grid grid-cols-1 gap-6">
                        <div class="flex items-center justify-between p-4 bg-slate-50/50 border border-slate-200 rounded-xl">
                            <div>
                                <span class="block text-sm font-bold text-slate-800">Pasien Prioritas / Diutamakan</span>
                                <span class="block text-xs text-slate-500">Aktifkan jika pasien memerlukan penanganan segera.</span>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_priority" value="1" class="sr-only peer" {{ old('is_priority', $patient->is_priority) ? 'checked' : '' }}>
                                <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-rose-600"></div>
                            </label>
                        </div>

                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Keterangan / Catatan Tambahan</label>
                            <textarea name="notes" rows="3" class="w-full bg-slate-50/50 border border-slate-200 rounded-xl px-4 py-3 text-sm text-slate-800 focus:bg-white focus:border-indigo-500">{{ old('notes', $patient->notes) }}</textarea>
                        </div>
                    </div>
                </div>

                <!-- TOMBOL AKSI -->
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <a href="{{ route('patients.index') }}" class="px-6 py-3 rounded-xl border border-slate-200 text-sm font-semibold text-slate-600 hover:bg-slate-50">Batal</a>
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold shadow-lg shadow-indigo-600/20">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Script Wilayah -->
    <script>
        const districtsData = {
            "Banyuwangi": ["Banyuwangi", "Kalipuro", "Giri", "Glagah", "Kabat", "Rogojampi", "Blimbingsari", "Srono", "Muncar", "Cluring", "Gambiran", "Tegalsari", "Mempuro", "Genteng", "Glenmore", "Kalibaru", "Sempu", "Songgon", "Singojuruh", "Licin", "Wongsorejo", "Pesanggaran", "Siliragung", "Bangorejo"],
            "Jember": ["Jember Kota", "Patrang", "Kaliwates", "Sumbersari", "Batu Puteh", "Ajung", "Ambulu", "Jenggawah"],
            "Bondowoso": ["Bondowoso", "Tamanan", "Wringin", "Prajekan", "Tenggarang"],
            "Situbondo": ["Situbondo", "Panji", "Bungatan", "Kendit", "Asembagus"],
            "Lainnya": ["Lainnya"]
        };

        function updateDistricts() {
            const regency = document.getElementById('regency').value;
            const districtSelect = document.getElementById('district');
            districtSelect.innerHTML = '';
            if (districtsData[regency]) {
                districtsData[regency].forEach(d => {
                    const opt = document.createElement('option');
                    opt.value = d;
                    opt.textContent = d;
                    if(d === "{{ $patient->district }}") opt.selected = true;
                    districtSelect.appendChild(opt);
                });
            }
        }
    </script>
</x-app-layout>
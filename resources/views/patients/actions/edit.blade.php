<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 py-1">
            <div>
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <span class="px-3.5 py-1 bg-amber-50 text-amber-700 font-black text-[10px] rounded-full uppercase tracking-widest border border-amber-100 shadow-sm">Modul Cathlab</span>
                    <span class="text-slate-300">•</span>
                    <span class="text-xs font-bold text-slate-400 tracking-wide uppercase">Pembaruan Prosedur Medis</span>
                </div>
                <h2 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900">
                    Edit Tindakan: <span class="text-amber-600 font-extrabold">{{ $patient->name }}</span>
                </h2>
            </div>
            <div>
                <a href="{{ route('patients.actions-history', $patient->id) }}" class="inline-flex items-center px-5 py-3 bg-white border border-slate-200/80 text-slate-700 hover:bg-slate-50 hover:border-slate-300 font-extrabold text-xs uppercase tracking-wider rounded-2xl shadow-sm transition-all duration-200">
                    <svg class="w-4 h-4 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Riwayat
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('patients.actions.update', [$patient->id, $actionRecord->id]) }}" method="POST" class="space-y-8" x-data="editProcedureForm()">
                @csrf @method('PUT')
                
                <!-- SECTION 1: STATUS PROSEDUR & CITO -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 p-6 sm:p-8 transition-all duration-300 hover:shadow-2xl hover:shadow-slate-200/50">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md">Urgensi</span>
                            <h3 class="text-base font-black text-slate-900 tracking-tight pt-1">Status Prosedur & Kondisi Darurat</h3>
                            <p class="text-xs font-medium text-slate-500">Tandai jika tindakan ini merupakan kasus darurat / CITO.</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_cito" value="1" x-model="isCito" @change="toggleCitoColor($event.target)" class="sr-only peer">
                            <div class="w-16 h-8 bg-slate-200 rounded-full peer peer-checked:bg-rose-600 peer-checked:after:translate-x-8 after:content-[''] after:absolute after:top-[3px] after:left-[3px] after:bg-white after:rounded-full after:h-[26px] after:w-[26px] after:transition-all after:duration-300 after:shadow-md"></div>
                        </label>
                    </div>

                    <div id="citoBadge" class="hidden mt-5 p-4 bg-rose-50 border border-rose-100 rounded-2xl text-xs font-bold text-rose-700 flex items-center space-x-3 animate-pulse shadow-sm">
                        <div class="p-2 bg-rose-100 rounded-xl">
                            <svg class="w-5 h-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/></svg>
                        </div>
                        <div>
                            <span class="font-black uppercase tracking-wide block text-rose-800">PERHATIAN: STATUS CITO AKTIF</span>
                            <span class="text-rose-600 font-medium">Prosedur ditandai sebagai darurat medis. Prioritas sistem akan disesuaikan.</span>
                        </div>
                    </div>

                    <!-- DOOR-TO-BALLOON TIME CONTAINER -->
                    <div x-show="isCito" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="mt-6 pt-6 border-t border-slate-100 grid grid-cols-1 md:grid-cols-2 gap-6 bg-gradient-to-br from-rose-50/40 to-orange-50/20 p-6 rounded-2xl border border-rose-100/80 shadow-inner">
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-rose-900 uppercase tracking-wider">Waktu Tiba di RS (Door Time) <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" name="arrived_hospital_at" value="{{ old('arrived_hospital_at', isset($actionRecord->arrived_hospital_at) ? date('Y-m-d\TH:i', strtotime($actionRecord->arrived_hospital_at)) : '') }}" :required="isCito" class="w-full rounded-2xl border-rose-200 text-sm font-bold text-slate-800 bg-white py-3.5 px-4 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all shadow-sm">
                            <p class="text-[11px] text-slate-500 font-medium">Waktu pertama kali pasien masuk IGD / pendaftaran.</p>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-rose-900 uppercase tracking-wider">Waktu Inflasi Balon (Balloon Time) <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" name="balloon_inflation_at" value="{{ old('balloon_inflation_at', isset($actionRecord->balloon_inflation_at) ? date('Y-m-d\TH:i', strtotime($actionRecord->balloon_inflation_at)) : '') }}" :required="isCito" class="w-full rounded-2xl border-rose-200 text-sm font-bold text-slate-800 bg-white py-3.5 px-4 focus:border-rose-500 focus:ring-4 focus:ring-rose-500/10 transition-all shadow-sm">
                            <p class="text-[11px] text-slate-500 font-medium">Waktu kardiolog berhasil meniupkan balon di Cathlab.</p>
                        </div>
                    </div>
                </div>

                <!-- SECTION 2: KLASIFIKASI & TIM MEDIS -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 p-6 sm:p-8 space-y-6 transition-all duration-300">
                    <div class="border-b border-slate-100 pb-5 space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md">Alur Penugasan</span>
                        <h3 class="text-base font-black text-slate-900 tracking-tight pt-1">Klasifikasi Divisi & Tim Medis</h3>
                        <p class="text-xs font-medium text-slate-500">Pilih alur penugasan dari divisi utama hingga dokter penanggung jawab.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Kategori Divisi -->
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">1. Kategori Divisi Utama <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" name="action_category_id" x-model="categoryId" required>
                                <button type="button" @click="catOpen = !catOpen" @click.away="catOpen = false" 
                                    class="w-full rounded-2xl border border-slate-200 text-sm font-bold text-slate-800 bg-slate-50/50 py-3.5 px-4 text-left flex items-center justify-between focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all shadow-sm">
                                    <span x-text="categoryLabel" :class="{'text-slate-400': !categoryId, 'text-slate-800': categoryId}"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': catOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="catOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-2 max-h-60 overflow-y-auto">
                                    @foreach($categories as $c) 
                                        <div @click="selectCategory('{{ $c->id }}', '{{ $c->name }}')" 
                                            class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                            :class="{'bg-amber-50/60 text-amber-600 font-black': categoryId == '{{ $c->id }}'}">
                                            <span>{{ $c->name }}</span>
                                            <svg x-show="categoryId == '{{ $c->id }}'" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Sub-Divisi -->
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">2. Sub-Divisi Spesifik <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" name="sub_division_id" x-model="subId" required>
                                <button type="button" @click="subOptions.length > 0 && (subOpen = !subOpen)" @click.away="subOpen = false" 
                                    :class="{'opacity-60 cursor-not-allowed': subOptions.length === 0}"
                                    class="w-full rounded-2xl border border-slate-200 text-sm font-bold text-slate-800 bg-slate-50/50 py-3.5 px-4 text-left flex items-center justify-between focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all shadow-sm">
                                    <span x-text="subLabel" :class="{'text-slate-400': !subId, 'text-slate-800': subId}"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': subOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="subOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-2 max-h-60 overflow-y-auto">
                                    <template x-for="sub in subOptions" :key="sub.id">
                                        <div @click="selectSub(sub.id, sub.name)" 
                                            class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                            :class="{'bg-amber-50/60 text-amber-600 font-black': subId == sub.id}">
                                            <span x-text="sub.name"></span>
                                            <svg x-show="subId == sub.id" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </template>
                                    <div x-show="subOptions.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center font-medium" x-text="subLoading ? 'Memuat Sub-Divisi...' : 'Pilih Kategori Terlebih Dahulu'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-1">
                        <!-- Dokter Penanggung Jawab -->
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">3. Dokter Penanggung Jawab (DPJP) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" name="doctor_id" x-model="doctorId" required>
                                <button type="button" @click="doctorOptions.length > 0 && (doctorOpen = !doctorOpen)" @click.away="doctorOpen = false" 
                                    :class="{'opacity-60 cursor-not-allowed': doctorOptions.length === 0}"
                                    class="w-full rounded-2xl border border-slate-200 text-sm font-bold text-slate-800 bg-slate-50/50 py-3.5 px-4 text-left flex items-center justify-between focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all shadow-sm">
                                    <span x-text="doctorLabel" :class="{'text-slate-400': !doctorId, 'text-slate-800': doctorId}"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': doctorOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="doctorOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-2 max-h-60 overflow-y-auto">
                                    <template x-for="doc in doctorOptions" :key="doc.id">
                                        <div @click="selectDoctor(doc.id, doc.name)" 
                                            class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                            :class="{'bg-amber-50/60 text-amber-600 font-black': doctorId == doc.id}">
                                            <span x-text="doc.name"></span>
                                            <svg x-show="doctorId == doc.id" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </template>
                                    <div x-show="doctorOptions.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center font-medium" x-text="doctorLoading ? 'Memuat Dokter...' : 'Pilih Sub-Divisi Terlebih Dahulu'"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Tindakan Medis -->
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">4. Tindakan Medis (Hak Akses) <span class="text-rose-500">*</span></label>
                            <div class="relative">
                                <input type="hidden" name="action_id" x-model="actionId" required>
                                <button type="button" @click="actionOptions.length > 0 && (actionOpen = !actionOpen)" @click.away="actionOpen = false" 
                                    :class="{'opacity-60 cursor-not-allowed': actionOptions.length === 0}"
                                    class="w-full rounded-2xl border border-slate-200 text-sm font-bold text-slate-800 bg-slate-50/50 py-3.5 px-4 text-left flex items-center justify-between focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all shadow-sm">
                                    <span x-text="actionLabel" :class="{'text-slate-400': !actionId, 'text-slate-800': actionId}"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': actionOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="actionOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-2 max-h-60 overflow-y-auto">
                                    <template x-for="act in actionOptions" :key="act.id">
                                        <div @click="selectAction(act.id, act.name)" 
                                            class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                            :class="{'bg-amber-50/60 text-amber-600 font-black': actionId == act.id}">
                                            <span x-text="act.name"></span>
                                            <svg x-show="actionId == act.id" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    </template>
                                    <div x-show="actionOptions.length === 0" class="px-4 py-3 text-sm text-slate-400 text-center font-medium" x-text="actionLoading ? 'Memuat Tindakan...' : 'Pilih Dokter Terlebih Dahulu'"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Jumlah Ring (Kondisional) -->
                    <div id="ringContainer" class="{{ $actionRecord->ring_count ? '' : 'hidden' }} bg-gradient-to-br from-amber-50/80 to-orange-50/40 p-6 rounded-2xl border border-amber-100/80 transition-all duration-300 shadow-sm">
                        <label class="block text-xs font-black text-amber-900 uppercase tracking-wider mb-2">Jumlah Ring Terpasang <span class="text-rose-500">*</span></label>
                        <input type="number" name="ring_count" value="{{ $actionRecord->ring_count }}" min="1" max="10" placeholder="Masukkan jumlah ring terpasang (Contoh: 2)" class="w-full rounded-xl border-amber-200 text-sm font-bold text-slate-800 bg-white py-3 px-4 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10">
                    </div>

                    <!-- ROW TAMBAHAN: Ruangan Asal | Tanggal Tindakan | Dokter Anestesi -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 pt-5 border-t border-slate-100 mt-4">
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Ruangan / Asal Rujukan <span class="text-rose-500">*</span></label>
                            <input type="text" name="origin_ward" value="{{ $actionRecord->origin_ward }}" placeholder="Contoh: ICU / IGD" class="w-full rounded-2xl border-slate-200 text-sm font-semibold text-slate-800 bg-slate-50/50 py-3.5 px-4 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm" required>
                        </div>
                        
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Tanggal & Jam Tindakan <span class="text-rose-500">*</span></label>
                            <input type="datetime-local" name="action_date" value="{{ old('action_date', isset($actionRecord->action_date) ? date('Y-m-d\TH:i', strtotime($actionRecord->action_date)) : '') }}" class="w-full rounded-2xl border-slate-200 text-sm font-semibold text-slate-800 bg-slate-50/50 py-3.5 px-4 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm" required>
                        </div>

                        <!-- Dokter Anestesi Custom Select -->
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Dokter Anestesi</label>
                            <div class="relative">
                                <input type="hidden" name="anesthesia_doctor_id" x-model="anesId">
                                <button type="button" @click="anesOpen = !anesOpen" @click.away="anesOpen = false" 
                                    class="w-full rounded-2xl border border-slate-200 text-sm font-bold text-slate-800 bg-slate-50/50 py-3.5 px-4 text-left flex items-center justify-between focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all shadow-sm">
                                    <span x-text="anesLabel" :class="{'text-slate-400': !anesId, 'text-slate-800': anesId}"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180':anesOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="anesOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-2 max-h-60 overflow-y-auto">
                                    <div @click="selectAnes('', '-- Tanpa Anestesi --')" 
                                        class="px-4 py-2.5 text-sm font-bold text-slate-500 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                        :class="{'bg-amber-50/60 text-amber-600 font-black': !anesId}">
                                        <span>-- Tanpa Anestesi --</span>
                                        <svg x-show="!anesId" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    @foreach($anesthesiaDoctors as $anes)
                                        <div @click="selectAnes('{{ $anes->id }}', '{{ $anes->name }}')" 
                                            class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                            :class="{'bg-amber-50/60 text-amber-600 font-black': anesId == '{{ $anes->id }}'}">
                                            <span>{{ $anes->name }}</span>
                                            <svg x-show="anesId == '{{ $anes->id }}'" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3: KLINIS & DIAGNOSA -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 p-6 sm:p-8 space-y-6 transition-all duration-300">
                    <div class="border-b border-slate-100 pb-5 space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-amber-600 bg-amber-50 px-2.5 py-1 rounded-md">Medis & Klinis</span>
                        <h3 class="text-base font-black text-slate-900 tracking-tight pt-1">Asesmen Klinis & Diagnosa</h3>
                        <p class="text-xs font-medium text-slate-500">Rincian diagnosa utama serta diagnosa sekunder pendukung.</p>
                    </div>

                    <div class="space-y-5">
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Diagnosa Utama (Primary Diagnosis) <span class="text-rose-500">*</span></label>
                            <input type="text" name="diagnosis_1" value="{{ $actionRecord->diagnosis_1 }}" placeholder="Masukkan diagnosa utama..." class="w-full rounded-2xl border-slate-200 text-sm font-semibold text-slate-800 bg-slate-50/50 py-3.5 px-4 shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all" required>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Diagnosa Sekunder 2 (Opsional)</label>
                                <input type="text" name="diagnosis_2" value="{{ $actionRecord->diagnosis_2 }}" placeholder="Diagnosa sekunder tambahan..." class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 bg-slate-50/40 py-3 px-4 shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider">Diagnosa Sekunder 3 (Opsional)</label>
                                <input type="text" name="diagnosis_3" value="{{ $actionRecord->diagnosis_3 }}" placeholder="Diagnosa sekunder tambahan..." class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 bg-slate-50/40 py-3 px-4 shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- SECTION 3.5: PARAMETER KLINIS PROFESIONAL (KARS) -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 p-6 sm:p-8 space-y-6 transition-all duration-300">
                    <div class="border-b border-slate-100 pb-5 space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-indigo-600 bg-indigo-50 px-3 py-1 rounded-lg border border-indigo-100">Standar Mutu KARS</span>
                        <h3 class="text-base font-black text-slate-900 tracking-tight pt-1">Parameter Teknis & Klinis Prosedur</h3>
                        <p class="text-xs font-medium text-slate-500">Pencatatan indikator keberhasilan angiografi, radiasi, dan kontras.</p>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- TIMI Flow Post dengan Alpine Custom Select -->
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">TIMI Flow Post</label>
                            <div class="relative">
                                <input type="hidden" name="timi_flow_post" x-model="timiId">
                                <button type="button" @click="timiOpen = !timiOpen" @click.away="timiOpen = false" 
                                    class="w-full rounded-2xl border border-slate-200 text-sm font-bold text-slate-800 bg-slate-50/50 py-3.5 px-4 text-left flex items-center justify-between focus:outline-none focus:ring-4 focus:ring-amber-500/10 focus:border-amber-500 transition-all shadow-sm">
                                    <span x-text="timiLabel"></span>
                                    <svg class="w-4 h-4 text-slate-400 transition-transform duration-200" :class="{'rotate-180': timiOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                </button>
                                <div x-show="timiOpen" x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="absolute z-50 w-full mt-2 bg-white rounded-2xl shadow-2xl border border-slate-100 overflow-hidden py-2 max-h-60 overflow-y-auto">
                                    <div @click="selectTimi('3', 'TIMI 3 (Normal Flow)')" 
                                        class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                        :class="{'bg-amber-50/60 text-amber-600 font-black': timiId == '3'}">
                                        <span>TIMI 3 (Normal Flow)</span>
                                        <svg x-show="timiId == '3'" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div @click="selectTimi('2', 'TIMI 2 (Partial Flow)')" 
                                        class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                        :class="{'bg-amber-50/60 text-amber-600 font-black': timiId == '2'}">
                                        <span>TIMI 2 (Partial Flow)</span>
                                        <svg x-show="timiId == '2'" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div @click="selectTimi('1', 'TIMI 1 (Penetration without Perfusion)')" 
                                        class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                        :class="{'bg-amber-50/60 text-amber-600 font-black': timiId == '1'}">
                                        <span>TIMI 1 (Penetration without Perfusion)</span>
                                        <svg x-show="timiId == '1'" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div @click="selectTimi('0', 'TIMI 0 (No Perfusion)')" 
                                        class="px-4 py-2.5 text-sm font-bold text-slate-700 hover:bg-amber-50 hover:text-amber-600 cursor-pointer transition-colors flex items-center justify-between"
                                        :class="{'bg-amber-50/60 text-amber-600 font-black': timiId == '0'}">
                                        <span>TIMI 0 (No Perfusion)</span>
                                        <svg x-show="timiId == '0'" class="w-4 h-4 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Volume Kontras (ml)</label>
                            <input type="number" name="contrast_volume" value="{{ $actionRecord->contrast_volume }}" placeholder="Contoh: 150" class="w-full rounded-2xl border-slate-200 text-sm font-bold text-slate-800 bg-slate-50/50 py-3.5 px-4 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Fluoroscopy Time (Menit)</label>
                            <input type="number" step="0.1" name="fluro_time" value="{{ $actionRecord->fluro_time }}" placeholder="Contoh: 12.5" class="w-full rounded-2xl border-slate-200 text-sm font-bold text-slate-800 bg-slate-50/50 py-3.5 px-4 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all shadow-sm">
                        </div>
                    </div>
                </div>

                <!-- SECTION 4: KESIMPULAN, SARAN & CATATAN -->
                <div class="bg-white rounded-3xl border border-slate-100 shadow-xl shadow-slate-100/80 p-6 sm:p-8 space-y-6 transition-all duration-300">
                    <div class="border-b border-slate-100 pb-5 space-y-1">
                        <span class="text-[10px] font-black uppercase tracking-widest text-purple-600 bg-purple-50 px-2.5 py-1 rounded-md">Hasil & Tindak Lanjut</span>
                        <h3 class="text-base font-black text-slate-900 tracking-tight pt-1">Kesimpulan & Rekomendasi Medis</h3>
                        <p class="text-xs font-medium text-slate-500">Hasil akhir prosedur dan instruksi tindak lanjut pasien.</p>
                    </div>

                    <div class="bg-gradient-to-br from-indigo-50/80 to-blue-50/30 p-6 rounded-2xl border border-indigo-100/80 space-y-4 shadow-sm">
                        <div class="flex items-center justify-between">
                            <div>
                                <h4 class="text-xs font-black text-indigo-900 uppercase tracking-wider">Audit Outcome Klinis (Standar Akreditasi)</h4>
                                <p class="text-xs text-slate-600 font-medium">Tentukan status keberhasilan klinis / anatomis tindakan kateterisasi.</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_successful" value="1" {{ $actionRecord->is_successful ? 'checked' : '' }} class="sr-only peer">
                                <div class="w-14 h-7 bg-slate-300 rounded-full peer peer-checked:bg-emerald-600 peer-checked:after:translate-x-7 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-[24px] after:w-[24px] after:transition-all after:shadow-md"></div>
                            </label>
                        </div>
                        <div class="space-y-2 pt-3 border-t border-indigo-100/80">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Catatan Komplikasi / Kejadian Tidak Diharapkan (Opsional)</label>
                            <input type="text" name="complication_notes" value="{{ $actionRecord->complication_notes }}" placeholder="Contoh: Tidak ada komplikasi / Terjadi hematoma minor di area puncture" class="w-full rounded-xl border-slate-200 text-sm font-medium text-slate-800 bg-white py-3 px-4 focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 shadow-sm">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Kesimpulan Prosedur <span class="text-rose-500">*</span></label>
                            <textarea name="conclusion" rows="4" placeholder="Tuliskan hasil laporan atau kesimpulan prosedur kateterisasi..." class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 bg-slate-50/50 p-4 shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all" required>{{ $actionRecord->conclusion }}</textarea>
                        </div>
                        <div class="space-y-2">
                            <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Saran & Tindak Lanjut <span class="text-rose-500">*</span></label>
                            <textarea name="suggestion" rows="4" placeholder="Tuliskan rekomendasi terapi, obat lanjutan, atau perawatan..." class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 bg-slate-50/50 p-4 shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all" required>{{ $actionRecord->suggestion }}</textarea>
                        </div>
                    </div>

                    <div class="space-y-2 pt-1">
                        <label class="block text-xs font-black text-slate-700 uppercase tracking-wider">Catatan Tambahan (Opsional)</label>
                        <textarea name="notes" rows="2.5" placeholder="Catatan khusus instrumen, komplikasi, atau hal penting lainnya..." class="w-full rounded-2xl border-slate-200 text-sm font-medium text-slate-800 bg-slate-50/50 p-4 shadow-sm focus:border-amber-500 focus:ring-4 focus:ring-amber-500/10 transition-all">{{ $actionRecord->notes }}</textarea>
                    </div>
                </div>

                <!-- ACTION FOOTER BAR -->
                <div class="flex items-center justify-end space-x-4 pt-2">
                    <a href="{{ route('patients.actions-history', $patient->id) }}" class="px-6 py-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-extrabold text-xs uppercase tracking-widest rounded-2xl transition-all duration-200">
                        Batal
                    </a>
                    <button type="submit" class="px-8 py-4 bg-amber-600 hover:bg-amber-700 text-white font-black text-xs uppercase tracking-widest rounded-2xl shadow-xl shadow-amber-600/30 hover:shadow-amber-600/50 transition-all duration-200 transform hover:-translate-y-0.5">
                        Perbarui Catatan Tindakan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT ALPINE.JS & DYNAMIC DROPDOWN FETCHING -->
    <script>
        function toggleCitoColor(el) {
            const badge = document.getElementById('citoBadge');
            if(el.checked) { 
                badge.classList.remove('hidden');
            } else { 
                badge.classList.add('hidden');
            }
        }

        function editProcedureForm() {
            const allSubDivisions = @json($subDivisions ?? []);
            const allDoctors = @json($doctors ?? []);
            const allActions = @json($allActions ?? []);

            const currentCatId = '{{ $actionRecord->action_category_id }}';
            const currentSubId = '{{ $actionRecord->sub_division_id }}';
            const currentDocId = '{{ $actionRecord->doctor_id }}';
            const currentActId = '{{ $actionRecord->action_id }}';
            const currentTimi = '{{ $actionRecord->timi_flow_post }}';

            let initialCatName = 'Pilih Kategori Divisi';
            @foreach($categories as $c)
                if (String(currentCatId) === '{{ $c->id }}') {
                    initialCatName = '{{ $c->name }}';
                }
            @endforeach

            const foundSub = allSubDivisions.find(s => String(s.id) === String(currentSubId));
            const foundDoc = allDoctors.find(d => String(d.id) === String(currentDocId));
            const foundAct = allActions.find(a => String(a.id) === String(currentActId));

            let initialAnesName = '-- Tanpa Anestesi --';
            @foreach($anesthesiaDoctors as $anes)
                if (String('{{ $actionRecord->anesthesia_doctor_id }}') === '{{ $anes->id }}') {
                    initialAnesName = '{{ $anes->name }}';
                }
            @endforeach

            const timiMap = {
                '3': 'TIMI 3 (Normal Flow)',
                '2': 'TIMI 2 (Partial Flow)',
                '1': 'TIMI 1 (Penetration without Perfusion)',
                '0': 'TIMI 0 (No Perfusion)'
            };

            return {
                isCito: {{ $actionRecord->is_cito ? 'true' : 'false' }},
                
                catOpen: false,
                categoryId: currentCatId,
                categoryLabel: initialCatName,

                subOpen: false,
                subId: currentSubId,
                subLabel: foundSub ? foundSub.name : 'Pilih Sub-Divisi',
                subOptions: allSubDivisions.filter(s => String(s.action_category_id) === String(currentCatId)),
                subLoading: false,

                doctorOpen: false,
                doctorId: currentDocId,
                doctorLabel: foundDoc ? foundDoc.name : 'Pilih Dokter',
                doctorOptions: allDoctors.filter(d => String(d.sub_division_id) === String(currentSubId)),
                doctorLoading: false,

                actionOpen: false,
                actionId: currentActId,
                actionLabel: foundAct ? foundAct.name : 'Pilih Tindakan',
                actionOptions: allActions.filter(a => String(a.doctor_id) === String(currentDocId)),
                actionLoading: false,

                anesOpen: false,
                anesId: '{{ $actionRecord->anesthesia_doctor_id }}',
                anesLabel: initialAnesName,

                timiOpen: false,
                timiId: currentTimi,
                timiLabel: timiMap[currentTimi] || 'Pilih TIMI Flow',

                init() {
                    if (this.isCito) {
                        document.getElementById('citoBadge').classList.remove('hidden');
                    }
                },

                selectAnes(id, name) {
                    this.anesId = id;
                    this.anesLabel = name;
                    this.anesOpen = false;
                },

                selectTimi(id, name) {
                    this.timiId = id;
                    this.timiLabel = name;
                    this.timiOpen = false;
                },

                async selectCategory(id, name) {
                    this.categoryId = id;
                    this.categoryLabel = name;
                    this.catOpen = false;

                    this.subId = '';
                    this.subLabel = 'Pilih Sub-Divisi';
                    this.subOptions = [];
                    this.doctorId = '';
                    this.doctorLabel = 'Pilih Dokter Terlebih Dahulu';
                    this.doctorOptions = [];
                    this.actionId = '';
                    this.actionLabel = 'Pilih Tindakan Terlebih Dahulu';
                    this.actionOptions = [];
                    document.getElementById('ringContainer').classList.add('hidden');

                    this.subLoading = true;
                    try {
                        const res = await fetch(`/sub-divisions/by-category/${id}`);
                        this.subOptions = await res.json();
                    } finally {
                        this.subLoading = false;
                    }
                },

                async selectSub(id, name) {
                    this.subId = id;
                    this.subLabel = name;
                    this.subOpen = false;

                    this.doctorId = '';
                    this.doctorLabel = 'Pilih Dokter';
                    this.doctorOptions = [];
                    this.actionId = '';
                    this.actionLabel = 'Pilih Tindakan Terlebih Dahulu';
                    this.actionOptions = [];
                    document.getElementById('ringContainer').classList.add('hidden');

                    this.doctorLoading = true;
                    try {
                        const res = await fetch(`/doctors/by-sub-division/${id}`);
                        this.doctorOptions = await res.json();
                    } finally {
                        this.doctorLoading = false;
                    }
                },

                async selectDoctor(id, name) {
                    this.doctorId = id;
                    this.doctorLabel = name;
                    this.doctorOpen = false;

                    this.actionId = '';
                    this.actionLabel = 'Pilih Tindakan';
                    this.actionOptions = [];
                    document.getElementById('ringContainer').classList.add('hidden');

                    this.actionLoading = true;
                    try {
                        const res = await fetch(`/actions/by-doctor/${id}`);
                        this.actionOptions = await res.json();
                    } finally {
                        this.actionLoading = false;
                    }
                },

                selectAction(id, name) {
                    this.actionId = id;
                    this.actionLabel = name;
                    this.actionOpen = false;

                    const ringContainer = document.getElementById('ringContainer');
                    if (name.toUpperCase().includes('PCI') || name.toUpperCase().includes('PPCI')) {
                        ringContainer.classList.remove('hidden');
                    } else {
                        ringContainer.classList.add('hidden');
                        const ringInput = document.querySelector('input[name="ring_count"]');
                        if (ringInput) ringInput.value = '';
                    }
                }
            }
        }
    </script>
</x-app-layout>
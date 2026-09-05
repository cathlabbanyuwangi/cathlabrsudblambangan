<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tight leading-tight">
                    {{ __('Edit Pengguna & Hak Akses') }}
                </h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Perbarui informasi profil akun, ubah jabatan, atau perbarui kata sandi.</p>
            </div>
            
            <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-xs transition-all gap-2">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                Kembali ke Daftar User
            </a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-2xl border border-slate-200/80">
                <div class="p-8">
                    <form action="{{ route('users.update', $user->id) }}" method="POST">
                        @csrf 
                        @method('PUT')
                        
                        <!-- Layout Grid 2 Kolom yang Proporsional -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <!-- KOLOM KIRI: Informasi Identitas Akun -->
                            <div class="space-y-4 bg-slate-50/70 p-6 rounded-2xl border border-slate-200/60">
                                <div class="flex items-center space-x-2 pb-2 border-b border-slate-200/60">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Informasi Identitas</h3>
                                </div>

                                <!-- Nama Lengkap -->
                                <div>
                                    <x-input-label for="name" :value="__('Nama Lengkap')" class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1" />
                                    <x-text-input id="name" class="block w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs py-2.5" type="text" name="name" :value="old('name', $user->name)" required autofocus autocomplete="name" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-1" />
                                </div>

                                <!-- Username -->
                                <div>
                                    <x-input-label for="username" :value="__('Username (Login)')" class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1" />
                                    <x-text-input id="username" class="block w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs py-2.5" type="text" name="username" :value="old('username', $user->username)" required autocomplete="username" />
                                    <x-input-error :messages="$errors->get('username')" class="mt-1" />
                                </div>

                                <!-- Email Address -->
                                <div>
                                    <x-input-label for="email" :value="__('Email Sistem')" class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1" />
                                    <x-text-input id="email" class="block w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs py-2.5" type="email" name="email" :value="old('email', $user->email)" required autocomplete="email" />
                                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                                </div>
                            </div>

                            <!-- KOLOM KANAN: Jabatan Role & Keamanan Password -->
                            <div class="space-y-4 bg-slate-50/70 p-6 rounded-2xl border border-slate-200/60">
                                <div class="flex items-center space-x-2 pb-2 border-b border-slate-200/60">
                                    <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    <h3 class="text-xs font-black text-slate-800 uppercase tracking-wider">Jabatan & Keamanan</h3>
                                </div>

                                @php
                                    $currentRole = old('role', $user->getRoleNames()->first() ?? '');
                                    $currentRoleLabel = $currentRole ? ucwords(str_replace('-', ' ', $currentRole)) : '-- Pilih Role / Jabatan --';
                                @endphp

                                <!-- Role / Jabatan (Custom Alpine.js Dropdown yang Estetik & Rounded) -->
                                <div x-data="{ open: false, selected: '{{ $currentRole }}', selectedLabel: '{{ $currentRoleLabel }}' }" class="relative">
                                    <x-input-label for="role" :value="__('Role / Jabatan Akses')" class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1" />
                                    
                                    <!-- Hidden Input untuk mengirim value ke server -->
                                    <input type="hidden" name="role" x-model="selected" required>

                                    <!-- Tombol Trigger Dropdown -->
                                    <button type="button" 
                                            @click="open = !open" 
                                            class="w-full flex items-center justify-between text-xs bg-white rounded-xl border border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs py-2.5 px-3 text-left transition-all cursor-pointer">
                                        <span x-text="selectedLabel" :class="selected ? 'text-slate-900 font-bold' : 'text-slate-400'"></span>
                                        <svg class="w-4 h-4 text-slate-500 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>

                                    <!-- Daftar Pilihan Dropdown Custom -->
                                    <div x-show="open" 
                                         @click.away="open = false"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute z-50 mt-1.5 w-full bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 max-h-60 overflow-y-auto"
                                         style="display: none;">
                                        
                                        @foreach($roles as $role)
                                            <div @click="selected = '{{ $role->name }}'; selectedLabel = '{{ ucwords(str_replace('-', ' ', $role->name)) }}'; open = false"
                                                 class="px-3 py-2 text-xs text-slate-700 hover:bg-indigo-50 hover:text-indigo-600 cursor-pointer font-bold transition-colors flex items-center justify-between"
                                                 :class="selected === '{{ $role->name }}' ? 'bg-indigo-50/80 text-indigo-600 font-black' : ''">
                                                <span>{{ ucwords(str_replace('-', ' ', $role->name)) }}</span>
                                                <span x-show="selected === '{{ $role->name }}'">
                                                    <svg class="w-3.5 h-3.5 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>

                                    <x-input-error :messages="$errors->get('role')" class="mt-1" />
                                </div>

                                <!-- Password Baru (Opsional) -->
                                <div>
                                    <x-input-label for="password" :value="__('Password Baru (Opsional)')" class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1" />
                                    <x-text-input id="password" class="block w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs py-2.5" type="password" name="password" autocomplete="new-password" placeholder="Kosongkan jika tidak ingin mengubah password" />
                                    <x-input-error :messages="$errors->get('password')" class="mt-1" />
                                </div>

                                <!-- Confirm Password Baru -->
                                <div>
                                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password Baru')" class="text-[11px] font-bold uppercase tracking-wider text-slate-600 mb-1" />
                                    <x-text-input id="password_confirmation" class="block w-full text-xs rounded-xl border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-2xs py-2.5" type="password" name="password_confirmation" autocomplete="new-password" placeholder="Ulangi password baru di atas" />
                                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1" />
                                </div>
                            </div>

                        </div>

                        <!-- Tombol Aksi -->
                        <div class="flex items-center justify-end space-x-3 mt-6 pt-5 border-t border-slate-100">
                            <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold rounded-xl transition-all">
                                Batal
                            </a>

                            <x-primary-button class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                                Perbarui User
                            </x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
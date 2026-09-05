<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="font-black text-2xl text-slate-900 tracking-tight leading-tight">
                    {{ __('Manajemen Pengguna (User)') }}
                </h2>
                <p class="text-xs text-slate-500 font-semibold mt-0.5">Kelola daftar akun sistem dan penugasan jabatan (role).</p>
            </div>
            
            <div class="flex items-center gap-3">
                <!-- Tombol Kembali / Ke Manajemen Role -->
                <a href="{{ route('roles.index') }}" class="inline-flex items-center px-4 py-2 bg-white hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl border border-slate-200 shadow-xs transition-all gap-2">
                    <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"/></svg>
                    Kelola Role
                </a>

                <!-- Tombol Tambah User (Menggunakan route create yang baru) -->
                <a href="{{ route('users.create') }}">
                    <x-primary-button class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold rounded-xl shadow-md shadow-indigo-600/20 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Tambah User Baru
                    </x-primary-button>
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <!-- Flash Message Sukses -->
            @if(session('success'))
                <div class="p-4 bg-emerald-50 text-emerald-800 rounded-2xl border border-emerald-200 text-xs font-bold flex items-center shadow-xs">
                    <svg class="w-5 h-5 mr-2.5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Card Tabel -->
            <div class="bg-white overflow-hidden shadow-xs sm:rounded-2xl border border-slate-200/80">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm font-black text-slate-900 uppercase tracking-wider">Daftar Akun Terdaftar</h3>
                        <span class="text-xs font-bold text-slate-400 bg-slate-50 px-2.5 py-1 rounded-lg border border-slate-100">Total: {{ count($users) }} User</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-100">
                            <thead>
                                <tr class="bg-slate-50/70">
                                    <th class="px-6 py-3.5 text-left text-[11px] font-black text-slate-500 uppercase tracking-wider rounded-l-xl">No</th>
                                    <th class="px-6 py-3.5 text-left text-[11px] font-black text-slate-500 uppercase tracking-wider">Nama Lengkap</th>
                                    <th class="px-6 py-3.5 text-left text-[11px] font-black text-slate-500 uppercase tracking-wider">Username</th>
                                    <th class="px-6 py-3.5 text-left text-[11px] font-black text-slate-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3.5 text-left text-[11px] font-black text-slate-500 uppercase tracking-wider">Role / Jabatan</th>
                                    <th class="px-6 py-3.5 text-right text-[11px] font-black text-slate-500 uppercase tracking-wider rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 bg-white">
                                @forelse($users as $index => $user)
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-bold text-slate-500">{{ $index + 1 }}</td>
                                    
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-xs font-black text-slate-900">{{ $user->name }}</div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-600">
                                        <span class="bg-slate-100 text-slate-700 px-2 py-0.5 rounded font-mono">{{ $user->username ?? '-' }}</span>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-xs font-semibold text-slate-600">
                                        {{ $user->email }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            @forelse($user->getRoleNames() as $role)
                                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-indigo-50 text-indigo-700 border border-indigo-100">
                                                    {{ $role }}
                                                </span>
                                            @empty
                                                <span class="text-xs text-rose-500 font-semibold italic">Tanpa Role</span>
                                            @endforelse
                                        </div>
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-semibold">
                                        <a href="{{ route('users.edit', $user->id) }}" class="inline-flex items-center px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-all font-bold">
                                            Edit Role
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-8 text-center text-xs text-slate-400 font-semibold">
                                        Belum ada data user yang terdaftar.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
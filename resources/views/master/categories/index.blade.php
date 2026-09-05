<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-slate-800">Master Data Kategori Divisi</h2>
    </x-slot>

    <div class="py-8 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 space-y-6">
            
            <!-- Form Tambah Kategori -->
            <form action="{{ route('categories.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                @csrf
                <div class="sm:col-span-2">
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Kategori Divisi</label>
                    <input type="text" name="name" placeholder="Contoh: Cardio, Neuro, Radiologi" class="w-full rounded-xl border-slate-200 text-sm font-semibold" required>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                        + Tambah Kategori
                    </button>
                </div>
            </form>

            <!-- Tabel Daftar Kategori -->
            <div class="overflow-hidden border border-slate-100 rounded-2xl">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Nama Kategori Divisi</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('categories.destroy', $item->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus kategori ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-rose-500 font-bold text-xs hover:underline">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="2" class="px-6 py-8 text-center text-slate-400 italic">Belum ada data kategori divisi.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
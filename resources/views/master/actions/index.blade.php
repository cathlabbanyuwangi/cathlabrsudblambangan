<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-slate-800">Master Data Tindakan Medis</h2>
    </x-slot>

    <div class="py-8 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100 space-y-6">
            
            <!-- Form Tambah Tindakan -->
            <form action="{{ route('actions.store') }}" method="POST" class="grid grid-cols-1 sm:grid-cols-3 gap-4 bg-slate-50 p-6 rounded-2xl border border-slate-100">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kategori Divisi</label>
                    <select name="action_category_id" class="w-full rounded-xl border-slate-200 text-sm font-semibold" required>
                        <option value="">Pilih Kategori</option>
                        @foreach($categories as $cat) 
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option> 
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Tindakan</label>
                    <input type="text" name="name" placeholder="Contoh: PCI / Kateterisasi" class="w-full rounded-xl border-slate-200 text-sm font-semibold" required>
                </div>
                <div class="flex items-end">
                    <button type="submit" class="w-full py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-wider rounded-xl shadow-md transition-all">
                        + Tambah Tindakan
                    </button>
                </div>
            </form>

            <!-- Tabel Daftar Tindakan -->
            <div class="overflow-hidden border border-slate-100 rounded-2xl">
                <table class="w-full text-sm">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-3.5 text-left">Kategori Divisi</th>
                            <th class="px-6 py-3.5 text-left">Nama Tindakan</th>
                            <th class="px-6 py-3.5 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-700">{{ $item->category->name }}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">{{ $item->name }}</td>
                            <td class="px-6 py-4 text-right">
                                <!-- Tombol Hapus dengan SweetAlert2 -->
                                <form action="{{ route('actions.destroy', $item->id) }}" method="POST" class="inline-block delete-action-form">
                                    @csrf 
                                    @method('DELETE')
                                    <button type="button" onclick="confirmDeleteAction(this)" class="text-rose-500 font-bold text-xs hover:underline uppercase">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="3" class="px-6 py-8 text-center text-slate-400 italic">Belum ada data tindakan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>

    <!-- Script SweetAlert2 untuk Konfirmasi Hapus Master Tindakan -->
    <script>
        function confirmDeleteAction(button) {
            const form = button.closest('form');
            Swal.fire({
                title: 'Apakah Anda Yakin?',
                text: "Data master tindakan ini akan dihapus permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#e11d48',
                cancelButtonColor: '#64748b',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: 'rounded-[28px]'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
        }
    </script>
</x-app-layout>
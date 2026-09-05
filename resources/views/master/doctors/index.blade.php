<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-black text-slate-800">Master Data Dokter Spesialis & Tindakan Lintas Divisi</h2>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
        
        <!-- Form Tambah Dokter -->
        <div class="bg-white p-8 rounded-3xl shadow-sm border border-slate-100">
            <h3 class="text-base font-bold text-slate-800 mb-6 pb-3 border-b border-slate-100">Tambah Dokter Baru</h3>
            <form action="{{ route('doctors.store') }}" method="POST" class="space-y-6">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kategori Divisi</label>
                        <select name="action_category_id" id="doctor_category" class="w-full rounded-2xl border-slate-200 text-sm font-semibold" onchange="fetchSubDivisions(this.value, 'doctor_sub_division')" required>
                            <option value="">Pilih Kategori</option>
                            @foreach($categories as $cat) 
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Sub-Divisi</label>
                        <select name="sub_division_id" id="doctor_sub_division" class="w-full rounded-2xl border-slate-200 text-sm font-semibold" required>
                            <option value="">Pilih Kategori Terlebih Dahulu</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Dokter & Gelar</label>
                        <input type="text" name="name" placeholder="Contoh: Dr. Budi, Sp.JP" class="w-full rounded-2xl border-slate-200 text-sm font-semibold" required>
                    </div>
                </div>

                <!-- Checkbox Tindakan Lintas Divisi -->
                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Tindakan Tambahan / Lintas Kategori (Opsional)</label>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 max-h-48 overflow-y-auto">
                        @foreach($allActions as $act)
                            <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 bg-white p-2.5 rounded-xl border border-slate-100 cursor-pointer hover:bg-indigo-50/50">
                                <input type="checkbox" name="actions[]" value="{{ $act->id }}" class="rounded text-indigo-600 focus:ring-indigo-500">
                                <span class="truncate">{{ $act->name }} <span class="text-[10px] text-slate-400">({{ $act->category->name }})</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end">
                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest rounded-2xl shadow-md transition-all">
                        + Simpan Dokter Baru
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabel Daftar Dokter -->
        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-base font-bold text-slate-800">Daftar Dokter Terdaftar</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-sm text-left">
                    <thead class="bg-slate-50 text-slate-400 uppercase text-[10px] font-bold">
                        <tr>
                            <th class="px-6 py-4">Nama Dokter</th>
                            <th class="px-6 py-4">Kategori & Sub-Divisi</th>
                            <th class="px-6 py-4">Tindakan Lintas Divisi / Khusus</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($items as $item)
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="px-6 py-4 font-bold text-slate-800">{{ $item->name }}</td>
                            <td class="px-6 py-4">
                                <span class="font-semibold text-slate-700 block">{{ $item->category->name }}</span>
                                @if($item->subDivision)
                                    <span class="text-xs text-indigo-600 font-bold block mt-0.5">Sub: {{ $item->subDivision->name }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-wrap gap-1">
                                    @forelse($item->actions as $docAct)
                                        <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-bold rounded-md border border-indigo-100">{{ $docAct->name }}</span>
                                    @empty
                                        <span class="text-slate-400 text-xs italic">Hanya divisi utama</span>
                                    @endforelse
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                <button onclick="openEditModal({{ json_encode($item) }})" class="px-3 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-600 font-bold text-xs rounded-xl transition-all">
                                    Edit
                                </button>
                                <form action="{{ route('doctors.destroy', $item->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus dokter ini?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs rounded-xl transition-all">
                                        Hapus
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 italic">Belum ada data dokter.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT DOKTER -->
    <div id="editModal" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm hidden items-center justify-center z-50 p-4">
        <div class="bg-white rounded-3xl max-w-2xl w-full p-8 shadow-2xl space-y-6 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center border-b border-slate-100 pb-4">
                <h3 class="text-lg font-black text-slate-800">Edit Data Dokter</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-slate-600 font-bold">&times;</button>
            </div>

            <form id="editForm" method="POST" class="space-y-6">
                @csrf @method('PUT')
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Kategori Divisi</label>
                        <select name="action_category_id" id="edit_category" class="w-full rounded-2xl border-slate-200 text-sm font-semibold" onchange="fetchSubDivisions(this.value, 'edit_sub_division')" required>
                            @foreach($categories as $cat) 
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option> 
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Sub-Divisi</label>
                        <select name="sub_division_id" id="edit_sub_division" class="w-full rounded-2xl border-slate-200 text-sm font-semibold" required>
                            <!-- Diisi via Javascript -->
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-2">Nama Dokter</label>
                        <input type="text" name="name" id="edit_name" class="w-full rounded-2xl border-slate-200 text-sm font-semibold" required>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Tindakan Lintas Divisi / Khusus</label>
                    <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-100 max-h-40 overflow-y-auto">
                        @foreach($allActions as $act)
                            <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 bg-white p-2.5 rounded-xl border border-slate-100 cursor-pointer hover:bg-indigo-50/50">
                                <input type="checkbox" name="actions[]" value="{{ $act->id }}" class="edit-action-checkbox rounded text-indigo-600 focus:ring-indigo-500">
                                <span>{{ $act->name }} <span class="text-[10px] text-slate-400">({{ $act->category->name }})</span></span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                    <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 bg-slate-100 text-slate-600 font-bold text-xs uppercase tracking-widest rounded-xl">Batal</button>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs uppercase tracking-widest rounded-xl shadow-md">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- SCRIPT JAVASCRIPT -->
    <script>
        async function fetchSubDivisions(catId, targetId, selectedSubId = null) {
            const subSelect = document.getElementById(targetId);
            subSelect.innerHTML = '<option value="">Memuat Sub-Divisi...</option>';

            if (!catId) {
                subSelect.innerHTML = '<option value="">Pilih Kategori Terlebih Dahulu</option>';
                return;
            }

            const res = await fetch(`/sub-divisions/by-category/${catId}`);
            const data = await res.json();

            subSelect.innerHTML = '<option value="">Pilih Sub-Divisi</option>';
            data.forEach(sub => {
                const isSelected = selectedSubId && sub.id == selectedSubId ? 'selected' : '';
                subSelect.innerHTML += `<option value="${sub.id}" ${isSelected}>${sub.name}</option>`;
            });
        }

        async function openEditModal(doctor) {
            document.getElementById('editForm').action = `/doctors/${doctor.id}`;
            document.getElementById('edit_name').value = doctor.name;
            document.getElementById('edit_category').value = doctor.action_category_id;

            // Load sub divisi dan set nilai yang dipilih
            await fetchSubDivisions(doctor.action_category_id, 'edit_sub_division', doctor.sub_division_id);

            // Centang checkbox tindakan yang sudah dimiliki dokter
            const assignedActionIds = doctor.actions.map(a => a.id);
            document.querySelectorAll('.edit-action-checkbox').forEach(cb => {
                cb.checked = assignedActionIds.includes(parseInt(cb.value));
            });

            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('flex');
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</x-app-layout>
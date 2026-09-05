<x-app-layout>
    <div class="py-8 max-w-4xl mx-auto px-4">
        <div class="bg-white p-6 rounded-2xl shadow-sm border">
            <h2 class="text-lg font-bold mb-4">Master Data Sub-Divisi (SPJP, SPN, dll)</h2>
            <form action="{{ route('sub-divisions.store') }}" method="POST" class="flex gap-2 mb-6">
                @csrf
                <select name="action_category_id" class="rounded-xl border-slate-200" required>
                    @foreach($categories as $c) <option value="{{ $c->id }}">{{ $c->name }}</option> @endforeach
                </select>
                <input type="text" name="name" placeholder="Nama Sub-Divisi" class="rounded-xl border-slate-200 flex-1" required>
                <button class="bg-indigo-600 text-white px-4 py-2 rounded-xl">Simpan</button>
            </form>
            <table class="w-full text-sm">
                @foreach($items as $i)
                <tr class="border-b">
                    <td class="py-3">{{ $i->category->name }}</td>
                    <td class="py-3 font-bold">{{ $i->name }}</td>
                    <td class="py-3 text-right">
                        <form action="{{ route('sub-divisions.destroy', $i->id) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="text-rose-500">Hapus</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
</x-app-layout>
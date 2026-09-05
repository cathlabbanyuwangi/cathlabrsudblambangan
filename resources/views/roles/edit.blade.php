<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Role') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('roles.update', $role->id) }}" method="POST">
                    @csrf 
                    @method('PUT')
                    
                    <!-- Nama Role -->
                    <div class="mb-4">
                        <x-input-label for="name" :value="__('Nama Role')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $role->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <!-- Pilihan Hak Akses (Permissions) -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold text-slate-700 uppercase mb-2">Pilih Hak Akses (Permissions)</label>
                        <div class="grid grid-cols-2 gap-3 bg-slate-50 p-4 rounded-2xl border border-slate-200">
                            @foreach($permissions as $permission)
                                <label class="flex items-center space-x-2 text-xs font-semibold text-slate-700 cursor-pointer">
                                    <input type="checkbox" 
                                           name="permissions[]" 
                                           value="{{ $permission->name }}"
                                           {{ (is_array(old('permissions')) && in_array($permission->name, old('permissions'))) || (empty(old('permissions')) && in_array($permission->name, $rolePermissions ?? [])) ? 'checked' : '' }}
                                           class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span>{{ ucwords(str_replace('-', ' ', $permission->name)) }}</span>
                                </label>
                            @endforeach
                        </div>
                        <x-input-error :messages="$errors->get('permissions')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('roles.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
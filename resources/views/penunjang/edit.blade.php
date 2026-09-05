<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Opsi Penunjang') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-md mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('supporting-options.update', $supportingOption->id) }}" method="POST">
                    @csrf @method('PUT')
                    <div>
                        <x-input-label for="name" :value="__('Nama Opsi')" />
                        <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name', $supportingOption->name)" required autofocus />
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div class="flex items-center justify-end mt-4">
                        <a href="{{ route('supporting-options.index') }}" class="mr-4 text-sm text-gray-600 hover:text-gray-900">Batal</a>
                        <x-primary-button>Update</x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
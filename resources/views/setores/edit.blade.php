<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-lg tracking-tight text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Editar setor') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('setores.update', $setor) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('setores._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

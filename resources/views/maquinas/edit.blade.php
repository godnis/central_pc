<x-app-layout>
    <x-slot name="header">
        <h2 class="font-display font-semibold text-lg tracking-tight text-gray-800 dark:text-gray-100 leading-tight">
            {{ __('Editar máquina') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('maquinas.update', $maquina) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    @include('maquinas._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

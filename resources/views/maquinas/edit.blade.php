<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Editar máquina') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-lg mx-auto sm:px-6 lg:px-8">
            <div class="bg-white p-6 shadow-sm sm:rounded-lg">
                <form action="{{ route('maquinas.update', $maquina) }}" method="POST">
                    @csrf
                    @method('PUT')
                    @include('maquinas._form')
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

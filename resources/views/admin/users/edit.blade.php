@extends('layout')

@section('title', 'Adicionar Usuário')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-subpage-title>Editar Usuário</x-subpage-title>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-6">
                <div class="grid grid-cols-1 gap-4">
                    <div class="col-span-1">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome *</label>
                        <input type="text" name="name" id="name" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('name', $user->name) }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-4">
                    <div class="col-span-1">
                        <label for="email" class="block text-sm font-medium text-gray-700">E-mail *</label>
                        <input type="email" name="email" id="email" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('email', $user->email) }}">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.users.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Atualizar Usuário
                </button>
            </div>
        </form>
    </div>

@endsection
@extends('layout')

@section('title', 'Adicionar Tag')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-subpage-title>Adicionar Nova Tag</x-subpage-title>
        
        <form action="{{ route('tags.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf
            
            <div class="grid grid-cols-1 gap-6">
                <div class="grid grid-cols-12 gap-4">
                    <div class="col-span-8">
                        <label for="name" class="block text-sm font-medium text-gray-700">Nome *</label>
                        <input type="text" name="name" id="name" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                               value="{{ old('name') }}">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="col-span-4">
                        <label for="color" class="block text-sm font-medium text-gray-700">Cor *</label>
                        <div class="flex items-center space-x-2">
                            <input type="color" name="color" id="color" required
                                   class="mt-1 h-10 w-20 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                   value="{{ old('color', '#d5dae3') }}">
                            <span id="color_text" class="mt-1 font-bold text-sm text-gray-600">{{ old('color', '#d5dae3') }}</span>
                        </div>
                        @error('color')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
            
            <div class="mt-6 flex justify-end">
                <a href="{{ route('tags.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Salvar Tag
                </button>
            </div>
        </form>
    </div>

    <script>
        document.getElementById('color').addEventListener('input', function() {
            document.getElementById('color_text').textContent = this.value;
        });
    </script>
@endsection
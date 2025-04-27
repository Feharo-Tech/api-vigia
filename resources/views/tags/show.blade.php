@extends('layout')

@section('title', 'Detalhes da Tag')

@section('content')
    <div class="max-w-2xl mx-auto">
        <h1 class="text-2xl font-bold mb-6">Detalhes da Tag</h1>
        
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center mb-4">
                <span class="inline-block w-4 h-4 rounded-full mr-2" style="background-color: {{ $tag->color }}"></span>
                <h2 class="text-xl font-semibold">{{ $tag->name }}</h2>
            </div>
            
            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <p class="text-sm text-gray-500">Cor</p>
                    <p>{{ $tag->color }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-500">Criada em</p>
                    <p>{{ $tag->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            
            <div class="flex justify-end">
                <a href="{{ route('tags.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                    Voltar
                </a>
                <a href="{{ route('tags.edit', $tag) }}" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Editar
                </a>
            </div>
        </div>
    </div>
@endsection
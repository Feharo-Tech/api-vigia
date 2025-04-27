@extends('layout')

@section('title', 'APIs')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <x-page-title>APIs</x-page-title>
        <x-button-link href="{{ route('apis.create') }}">
            Adicionar API
        </x-button-link>
    </div>

    <livewire:api-list-card />
@endsection
@extends('layout')

@section('title', 'Dashboard')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <x-page-title>Dashboard</x-page-title>

        <div class="flex space-x-4">
            <x-button-link href="{{ route('apis.create') }}">
                Adicionar API
            </x-button-link>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
        <livewire:uptime-card />
        {{-- <livewire:response-time-card /> --}}
        {{-- <livewire:error-rate-card /> --}}
        {{-- <livewire:status-code-card /> --}}

        {{-- <livewire:status-history-card /> --}}
    </div>

    <div class="bg-white rounded-xl shadow-md overflow-hidden">
        <div class="p-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01" />
                </svg>
                APIs
            </h2>

            <livewire:api-list-card />
        </div>
    </div>
@endsection
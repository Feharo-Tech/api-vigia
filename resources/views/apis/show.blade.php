@extends('layout')

@section('title', 'Detalhes da API')

@section('content')
    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <div>
            <x-page-title>{{ $api->name }}</x-subpage-title>
                <div class="flex items-center space-x-4 mt-2">
                    <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $api->is_active ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $api->is_active ? 'Ativo' : 'Inativo' }}
                    </span>
                    <span class="text-gray-600 text-sm">Verificação a cada {{ $api->check_interval }} minutos</span>
                </div>
            </div>
            
            <div class="flex space-x-2">
                <form action="{{ route('apis.reset', $api) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded" onclick="return confirm('Tem certeza que deseja resetar as estatísticas esta API?')">
                        Resetar Estatísticas
                    </button>
                </form>
                <a href="{{ route('apis.edit', $api) }}" class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded">
                    Editar
                </a>
                <form action="{{ route('apis.check-now', $api) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Verificar Agora
                    </button>
                </form>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                            Informações da API
                        </h2>
                        <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $api->should_notify ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                        {{ $api->should_notify ? 'Notificação Ativa' : 'Notificação Inativa' }}
                    </span>
                    </div>

                    <div class="space-y-4 max-h-84 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                        <div class="grid grid-cols-1 gap-4">
                            <div class="border-b pb-3">
                                <p class="text-sm font-medium text-gray-500 mb-1">URL</p>
                                <div class="bg-gray-50 p-2 rounded">
                                    <p class="font-mono text-sm break-all">{{ $api->url }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="space-y-3">
                                    <div class="border-b pb-3">
                                        <p class="text-sm font-medium text-gray-500 mb-1">Método</p>
                                        <div class="flex items-center">
                                            <span class="px-2 py-1 text-xs font-semibold rounded 
                                                @if($api->method === 'GET') bg-blue-100 text-blue-800
                                                @elseif($api->method === 'POST') bg-green-100 text-green-800
                                                @elseif($api->method === 'PUT' || $api->method === 'PATCH') bg-yellow-100 text-yellow-800
                                                @elseif($api->method === 'DELETE') bg-red-100 text-red-800
                                                @else bg-gray-100 text-gray-800 @endif">
                                                {{ $api->method }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="border-b pb-3">
                                        <p class="text-sm font-medium text-gray-500 mb-1">Timeout</p>
                                        <p class="text-gray-800">{{ $api->timeout_threshold }} segundos</p>
                                    </div>
                                </div>

                                <div class="space-y-3">
                                    <div class="border-b pb-3">
                                        <p class="text-sm font-medium text-gray-500 mb-1">Status Esperado</p>
                                        <span class="px-2 py-1 text-xs font-semibold rounded 
                                            @if($api->expected_status_code >= 200 && $api->expected_status_code < 300) bg-green-100 text-green-800
                                            @elseif($api->expected_status_code >= 300 && $api->expected_status_code < 400) bg-blue-100 text-blue-800
                                            @elseif($api->expected_status_code >= 400 && $api->expected_status_code < 500) bg-yellow-100 text-yellow-800
                                            @else bg-red-100 text-red-800 @endif">
                                            {{ $api->expected_status_code }}
                                        </span>
                                    </div>

                                    <div class="border-b pb-3">
                                        <p class="text-sm font-medium text-gray-500 mb-1">Sensibilidade</p>
                                        <p class="text-gray-800">Notifica após {{ $api->error_threshold }} falhas</p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <p class="text-sm font-medium text-gray-500 mb-2">Tags</p>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($api->tags as $tag)
                                        <span class="px-2 py-1 text-xs rounded-full text-sm font-medium text-white" style="background-color: {{ $tag->color }}">
                                            {{ $tag->name }}
                                        </span>
                                    @empty
                                        <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Nenhuma Tag</span>
                                    @endforelse
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            Configurações da API
                        </h2>
                    </div>

                        <div class="space-y-4 max-h-80 overflow-y-auto pr-2" style="scrollbar-width: thin;">
                        
                            <div class="border rounded-lg overflow-hidden">
                                <div class="flex justify-between items-center bg-gray-50 px-4 py-3 border-b">
                                    <h3 class="text-sm font-medium text-gray-700">Resposta Esperada</h3>
                                    <span class="px-2 py-1 text-xs rounded-full 
                                            @if($api->expected_response) bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                        @if($api->expected_response) Configurado @else Não configurado @endif
                                    </span>
                                </div>
                                
                                @if($api->expected_response)
                                    <div class=" text-sm text-gray-500 bg-gray-50 bg-gray-50 p-2 rounded max-h-20 overflow-y-auto scrollbar-thin">
                                        {{ $api->expected_response }}
                                    </div>
                                @else
                                    <div class="flex text-sm items-center justify-center p-2 text-gray-500 bg-gray-50">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                        </svg>
                                        Nenhuma resposta esperada configurado para esta API
                                    </div>
                                @endif
                            </div>

                        <div class="border rounded-lg overflow-hidden">
                            <div class="flex justify-between items-center bg-gray-50 px-4 py-3 border-b">
                                <h3 class="text-sm font-medium text-gray-700">Headers</h3>
                                <span class="px-2 py-1 text-xs rounded-full 
                                        @if($api->headers && $api->headers !== 'null') bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                    @if($api->headers && $api->headers !== 'null') Configurado @else Não configurado @endif
                                </span>
                            </div>
                            
                            @if($api->headers && $api->headers !== 'null')
                                <div class="max-h-64 overflow-y-auto scrollbar-thin bg-gray-50">
                                    <pre class="font-mono text-xs pl-4 whitespace-pre-wrap break-words text-gray-800">
                                        @if(is_array($api->headers))
                                        {{ json_encode($api->headers, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                                        @else
                                        {{ json_encode(json_decode($api->headers, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                                        @endif
                                    </pre>
                                </div>
                            @else
                                <div class="flex text-sm  items-center justify-center p-2 text-gray-500 bg-gray-50">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Nenhum header configurado para esta API
                                </div>
                            @endif
                        </div>

                        <div class="border rounded-lg overflow-hidden">
                            <div class="flex justify-between items-center bg-gray-50 px-4 py-3 border-b">
                                <h3 class="text-sm font-medium text-gray-700">Body</h3>
                                <span class="px-2 py-1 text-xs rounded-full 
                                        @if($api->body && $api->body !== 'null') bg-green-100 text-green-800 @else bg-gray-100 text-gray-800 @endif">
                                    @if($api->body && $api->body !== 'null') Configurado @else Não configurado @endif
                                </span>
                            </div>
                            
                            @if($api->body && $api->body !== 'null')
                                <div class="max-h-64 overflow-y-auto scrollbar-thin bg-gray-50">
                                    <pre class="font-mono text-xs pl-4 whitespace-pre-wrap break-words text-gray-800">
                                        @if(is_array($api->body))
                                        {{ json_encode($api->body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                                        @else
                                        {{ json_encode(json_decode($api->body, true), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}
                                        @endif
                                    </pre>
                                </div>
                            @else
                                <div class="flex text-sm  items-center justify-center p-2 text-gray-500 bg-gray-50">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                    </svg>
                                    Nenhum body configurado para esta API
                                </div>
                            @endif
                        </div>
                        
                    </div>
                </div>
            </div>

            
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            Estatísticas de Monitoramento
                        </h2>
                    </div>

                    <div class="space-y-4">
                        <div class="border-b pb-4">
                            <div class="flex justify-between items-center mb-1">
                                <p class="text-sm font-medium text-gray-700">Taxa de Uptime</p>
                                <span class="text-sm font-semibold 
                                    @if($uptimeStats['uptime'] >= 99) text-green-600
                                    @elseif($uptimeStats['uptime'] >= 95) text-yellow-600
                                    @else text-red-600 @endif">
                                    {{ $uptimeStats['uptime'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full 
                                    @if($uptimeStats['uptime'] >= 99) bg-green-500
                                    @elseif($uptimeStats['uptime'] >= 95) bg-yellow-500
                                    @else bg-red-500 @endif" 
                                    style="width: {{ $uptimeStats['uptime'] }}%">
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-1">Disponibilidade nos últimos 30 dias</p>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="border rounded-lg p-3 bg-gradient-to-br from-green-50 to-green-100">
                                <div class="flex flex-col items-center">
                                    <p class="text-xs font-medium text-gray-600">Verificações</p>
                                    <p class="text-2xl font-bold text-green-800">{{ $uptimeStats['success_checks'] }}</p>
                                </div>
                            </div>

                            <div class="border rounded-lg p-3 bg-gradient-to-br from-red-50 to-red-100">
                                <div class="flex flex-col items-center">
                                    <p class="text-xs font-medium text-gray-600">Falhas</p>
                                    <p class="text-2xl font-bold text-red-800">{{ $uptimeStats['failure_checks'] }}</p>
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="border rounded-lg p-3 bg-gray-50">
                                <div class="flex flex-col items-center">
                                    <svg class="w-6 h-6 text-blue-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="text-xs font-medium text-gray-600 mb-1">Tempo Médio</p>
                                    @if($uptimeStats['average_response_time'])
                                        <p class="text-2xl font-bold 
                                        @if($uptimeStats['performance_status'] === 'fast') text-green-600
                                        @elseif($uptimeStats['performance_status'] === 'moderate') text-yellow-600
                                        @else text-red-600 @endif">
                                        {{ $uptimeStats['average_response_time'] }}ms
                                        </p>
                                        <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                                            <div class="h-2 rounded-full 
                                                @if($uptimeStats['performance_status'] === 'fast') bg-green-500
                                                @elseif($uptimeStats['performance_status'] === 'moderate') bg-yellow-500
                                                @else bg-red-500 @endif" 
                                                style="width: {{ min(100, $uptimeStats['average_response_time'] / 10) }}%">
                                            </div>
                                        </div>
                                    @else
                                        <p class="text-gray-400">Dados não disponíveis</p>
                                    @endif
                                </div>
                            </div>

                            <div class="border rounded-lg p-3 bg-gray-50">
                                <div class="flex flex-col items-center">
                                    <svg class="w-6 h-6 text-purple-500 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    <p class="text-xs font-medium text-gray-600 mb-1">Última Reposta</p>
                                    @if($uptimeStats['last_response_time'])
                                        <p class="text-2xl font-bold">{{ $uptimeStats['last_response_time'] }}ms</p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            {{ $api->statusChecks()->latest()->first()->created_at->diffForHumans() }}
                                        </p>
                                    @else
                                        <p class="text-gray-400">Dados não disponíveis</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        

                        
                    </div>
                </div>
            </div>
        </div>
        
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        Histórico de Verificações
                    </h2>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Data/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tempo (s)</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalhes</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($statusChecks as $check)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $check->created_at->format('d/m/Y H:i:s') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 rounded-full text-xs font-semibold {{ $check->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $check->success ? 'Sucesso' : 'Falha' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ number_format($check->response_time, 3) }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if(!$check->success)
                                        <p class="text-red-600">{{ $check->error_message }}</p>
                                        @if($check->response_body)
                                            <button onclick="toggleResponseBody({{ $check->id }})" class="text-blue-500 text-xs mt-1">
                                                Mostrar resposta
                                            </button>
                                            <div id="response-body-{{ $check->id }}" class="hidden mt-2 bg-gray-50 p-2 rounded font-mono text-xs">
                                                {{ Str::limit($check->response_body, 200) }}
                                            </div>
                                        @endif
                                    @else
                                        <p class="text-gray-500">Operação bem-sucedida</p>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500">
                                    Nenhuma verificação registrada ainda.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($statusChecks->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $statusChecks->onEachSide(1)->links('vendor.pagination.tailwind') }}
                </div>
            @endif
        </div>
    </div>
    
    @push('scripts')
        <script>
            function toggleResponseBody(id) {
                const element = document.getElementById(`response-body-${id}`);
                element.classList.toggle('hidden');
            }
        </script>
    @endpush
@endsection
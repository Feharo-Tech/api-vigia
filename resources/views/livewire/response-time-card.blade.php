
<div class="bg-white rounded-xl shadow-md overflow-hidden" wire:poll.30s="loadData">
    <div class="p-6">
        <div class="flex justify-between items-start">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tempo de Resposta <small class="ml-2">(ms)</small>
            </h2>
        </div>

        <div class="space-y-4 max-h-64 overflow-y-auto pr-2 scrollbar-thin">
            @forelse ($responseTimeData as $api)
                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-medium text-gray-700">
                            <a href="{{ route('apis.show', $api['id']) }}" class="font-medium text-gray-700 hover:text-gray-800 hover:none">
                                {{ $api['name'] }}
                            </a>
                        </span>
                        <span class="text-sm font-semibold {{ $this->getResponseTimeColor($api['avg_response_time']) === '#10B981' ? 'text-green-600' : 
                                                             ($this->getResponseTimeColor($api['avg_response_time']) === '#F59E0B' ? 'text-yellow-600' : 
                                                             'text-red-600') }}">
                            {{ $api['avg_response_time'] }}ms
                        </span>
                    </div>
                    
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div 
                            class="h-2 rounded-full" 
                            style="width: {{ min(100, $api['avg_response_time'] / 10) }}%; background-color: {{ $this->getResponseTimeColor($api['avg_response_time']) }}"
                        ></div>
                    </div>
                    
                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $api['checks_count'] }} verificações</span>
                        <span class="flex items-center">
                            Última resposta: 
                            @if($api['last_status'] === true)
                                <span class="ml-1 flex items-center text-green-600">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    {{ $api['last_response_time'] }}ms
                                </span>
                            @elseif($api['last_status'] === false)
                                <span class="ml-1 flex items-center text-red-600">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Timeout
                                </span>
                            @else
                                <span class="ml-1 flex items-center text-gray-400">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                        <circle cx="4" cy="4" r="3" />
                                    </svg>
                                    Não verificado
                                </span>
                            @endif
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-gray-500">
                    Nenhum dado de tempo de resposta disponível
                </div>
            @endforelse
        </div>
    </div>
</div>
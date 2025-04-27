<div class="bg-white rounded-xl shadow-md overflow-hidden" wire:poll.30s="loadData">
    <div class="p-6">
        <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            Taxa de Erros e Análise
        </h2>

        <div class="space-y-4 max-h-64 overflow-y-auto pr-2 scrollbar-thin">
            <!-- Cards de resumo -->
            <div class="grid grid-cols-3 gap-4 mb-2">
                @foreach(['last_24h' => '24 horas', 'last_week' => '7 dias', 'last_month' => '30 dias'] as $period => $label)
                <div class="text-center  bg-red-50 rounded-lg border border-red-100">
                    <div class="text-1xl font-bold {{ $errorData[$period]['rate'] > 10 ? 'text-red-600' : ($errorData[$period]['rate'] > 5 ? 'text-yellow-600' : 'text-green-600') }}">
                        {{ $errorData[$period]['rate'] }}%
                    </div>
                    <div class="text-sm text-gray-600 mb-1">{{ $label }}</div>
                    <div class="text-xs text-gray-500">
                        @php
                            $totalErrors = array_sum(array_column($errorData[$period]['apis'], 'error_count'));
                        @endphp
                        {{ $totalErrors }} erro{{ $totalErrors != 1 ? 's' : '' }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Tabs -->
            <div>
                <div class="flex space-x-1 bg-gray-100 p-1 rounded-lg">
                    @foreach(['last_24h' => '24 horas', 'last_week' => '7 dias', 'last_month' => '30 dias'] as $period => $label)
                    <button
                        wire:click="changeTab('{{ $period }}')"
                        class="px-4 py-2 text-sm font-medium rounded-md transition-all duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 {{ $activeTab === $period ? 'bg-white shadow-sm text-blue-600' : 'text-gray-500 hover:text-gray-700 hover:bg-gray-50' }}"
                    >
                        {{ $label }}
                        @if($activeTab === $period)
                        <span class="block mx-auto bg-blue-500 rounded-full mt-1"></span>
                        @endif
                    </button>
                    @endforeach
                </div>

                <!-- Conteúdo das tabs -->
                <div class="py-4">
                    <div class="mb-5 text-xs text-gray-600">
                        <span class="font-medium">Média:</span> {{ $errorData[$activeTab]['rate'] }}%
                        <span class="mx-2">|</span>
                        <span class="font-medium">Verificações:</span> {{ array_sum(array_column($errorData[$activeTab]['apis'], 'total_checks')) }}
                        <span class="mx-2">|</span>
                        <span class="font-medium">Erros:</span> {{ array_sum(array_column($errorData[$activeTab]['apis'], 'error_count')) }}
                    </div>

                    @if(!empty($errorData[$activeTab]['apis']))
                    <div class="space-y-3">
                        @foreach($errorData[$activeTab]['apis'] as $api)
                        <div class="border-b border-gray-100 pb-3 last:border-0">
                            <div class="flex justify-between items-center mb-1">
                                <span class="font-medium text-gray-700">
                                    <a href="{{ route('apis.show', $api['id']) }}" class="font-medium text-gray-700 hover:text-gray-800 hover:none">
                                        {{ $api['name'] }}
                                    </a>
                                </span>
                                <span class="text-sm font-semibold {{ $api['error_rate'] > 10 ? 'text-red-600' : ($api['error_rate'] > 5 ? 'text-yellow-600' : 'text-green-600') }}">
                                    {{ $api['error_rate'] }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div 
                                    class="h-2 rounded-full bg-red-500" 
                                    style="width: {{ min($api['error_rate'], 100) }}%"
                                ></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500 mt-1">
                                <span>{{ $api['total_checks'] }} verificações</span>
                                <span>{{ $api['error_count'] }} erro{{ $api['error_count'] != 1 ? 's' : '' }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-4 text-gray-500">
                        Nenhum dado disponível para este período
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
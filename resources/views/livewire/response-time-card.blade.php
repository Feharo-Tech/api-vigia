<div class="bg-white rounded-xl shadow-md overflow-hidden" wire:poll.30s="loadData">
    <div class="p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
            <h2 class="text-lg sm:text-xl font-semibold text-gray-800 flex items-center shrink-0">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Tempo de Resposta
            </h2>

            <div class="relative w-32">
                <select wire:model.live="selectedPeriod"
                    class="appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-1 px-2 pr-6 rounded-md text-xs focus:outline-none focus:ring-blue-500 focus:border-blue-500 w-full">
                    @foreach($availablePeriods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="space-y-4 max-h-64 overflow-y-auto pr-2 scrollbar-thin">
            @forelse ($responseTimeData as $api)
                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-medium text-gray-700">
                            <a href="{{ route('apis.show', $api['id']) }}"
                                class="font-medium text-gray-700 hover:text-gray-800 hover:none">
                                {{ $api['name'] }}
                            </a>
                        </span>
                        <span
                            class="text-sm font-semibold {{ $api['color'] === '#22C55E' ? 'text-green-600' : ($api['color'] === '#F59E0B' ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $api['avg_response_time'] }}ms
                        </span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full"
                            style="width: {{ min(100, $api['avg_response_time'] / 10) }}%; background-color: {{ $api['color'] }}">
                        </div>
                    </div>

                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $api['checks_count'] }} verificações</span>
                        <span class="flex items-center">
                            @if($api['last_check'])
                                {{ $api['last_check']->diffForHumans() }}
                                <span class="ml-2 flex items-center">
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
                            @else
                                Nunca verificado
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
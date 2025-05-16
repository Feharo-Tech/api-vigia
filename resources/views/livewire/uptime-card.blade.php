<div class="bg-white rounded-xl shadow-md overflow-hidden" wire:poll.30s="loadData">
    <div class="p-6">
        <div class="flex justify-between items-start">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Uptime das APIs
            </h2>

            <div class="relative ml-4 w-32">
                <select wire:model.live="selectedPeriod"
                    class="text-xs appearance-none bg-gray-50 border border-gray-300 text-gray-700 py-1 px-3 pr-8 rounded-md focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                    @foreach($availablePeriods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="space-y-4 max-h-64 overflow-y-auto pr-2 scrollbar-thin">
            @forelse ($uptimeData as $api)
                <div class="border-b border-gray-100 pb-4 last:border-0 last:pb-0">
                    <div class="flex justify-between items-center mb-1">
                        <span class="font-medium text-gray-700">
                            <a href="{{ route('apis.show', $api['id']) }}"
                                class="font-medium text-gray-700 hover:text-gray-800 hover:none">
                                {{ $api['name'] }}
                            </a>
                        </span>
                        <span
                            class="text-sm font-semibold {{ $api['uptime'] >= 99 ? 'text-green-600' : ($api['uptime'] >= 95 ? 'text-yellow-600' : 'text-red-600') }}">
                            {{ $api['uptime'] }}%
                        </span>
                    </div>

                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $api['uptime'] >= 99 ? 'bg-green-500' : ($api['uptime'] >= 95 ? 'bg-yellow-500' : 'bg-red-500') }}"
                            style="width: {{ $api['uptime'] }}%"></div>
                    </div>

                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                        <span>{{ $api['checks_count'] }} verificações</span>
                        <span class="flex items-center">
                            @if($api['last_check'])
                                {{ $api['last_check']->diffForHumans() }}
                            @else
                                Não verificado
                            @endif
                            <span class="ml-2 flex items-center">
                                @if($api['last_status'] === true)
                                    <span class="ml-1 flex items-center text-green-600">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Online
                                    </span>
                                @elseif($api['last_status'] === false)
                                    <span class="ml-1 flex items-center text-red-600">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3" />
                                        </svg>
                                        Offline
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
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-4 text-gray-500">
                    Nenhuma API cadastrada ou dados de uptime disponíveis
                </div>
            @endforelse
        </div>
    </div>
</div>
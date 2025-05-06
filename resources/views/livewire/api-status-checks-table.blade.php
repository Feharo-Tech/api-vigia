<div class="bg-white rounded-lg shadow overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex justify-between items-center">
            <h2 class="text-xl font-semibold text-gray-800 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                Histórico de Verificações
            </h2>

            <div class="flex space-x-2">
                <select wire:model.live="periodFilter"
                    class="w-24 text-xs rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    @foreach($availablePeriods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>

                <select wire:model.live="statusFilter"
                    class="w-32 text-xs rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                    <option value="all">Todos status</option>
                    <option value="success">Apenas sucessos</option>
                    <option value="failure">Apenas falhas</option>
                </select>
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th wire:click="sortBy('created_at')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        <div class="flex items-center">
                            Data/Hora
                            @if($sortField === 'created_at')
                                <x-sort-icon :direction="$sortDirection" />
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                    </th>
                    <th wire:click="sortBy('response_time')"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer hover:bg-gray-100">
                        <div class="flex items-center">
                            Tempo (s)
                            @if($sortField === 'response_time')
                                <x-sort-icon :direction="$sortDirection" />
                            @endif
                        </div>
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Detalhes
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse ($statusChecks as $check)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $check->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span
                                class="px-2 py-1 rounded-full text-xs font-semibold {{ $check->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
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
                                    <div id="response-body-{{ $check->id }}"
                                        class="hidden mt-2 bg-gray-50 p-2 rounded font-mono text-xs">
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
                            Nenhuma verificação registrada no período selecionado.
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
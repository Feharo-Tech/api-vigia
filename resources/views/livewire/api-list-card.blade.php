<div wire:poll.30s="loadData">
    <div class="flex justify-end items-center gap-4 mb-4">
        <input type="text" wire:model.lazy="search" placeholder="Buscar por nome..."
            class="px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500" />

        <select wire:model.lazy="monitoringStatus" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            <option value="">Monitoramento</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>

        </select>

        <select wire:model.lazy="notifyFilter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            <option value="">Notificação</option>
            <option value="ativo">Ativo</option>
            <option value="inativo">Inativo</option>
        </select>

        <select wire:model.lazy="statusFilter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            <option value="">Todos os status</option>
            <option value="online">Online</option>
            <option value="offline">Offline</option>
            <option value="nunca">Nunca verificado</option>
        </select>

        <select wire:model.lazy="tagFilter" class="px-3 py-2 border border-gray-300 rounded-md shadow-sm">
            <option value="">Todas as tags</option>
            @foreach ($allTags as $tag)
                <option value="{{ $tag->id }}">{{ $tag->name }}</option>
            @endforeach
        </select>
    </div>

    @if($apis->isEmpty() && !$isAnyFilterActive)
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
            Você ainda não tem APIs cadastradas.
            <a href="{{ route('apis.create') }}" class="font-semibold hover:underline text-blue-600 hover:text-blue-800">
                Clique aqui
            </a>
            para adicionar sua primeira API.
        </div>
    @elseif($apis->isEmpty() && $isAnyFilterActive)
        <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg">
            Nenhuma API encontrada usando o filtro.
        </div>
    @else
        <div class="overflow-x-auto rounded-lg border border-gray-200 shadow-sm">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Monitoramento</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Notificação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tags</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Última
                            Verificação</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach ($apis as $api)
                        <tr class="hover:bg-gray-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap font-medium text-gray-900">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-2 text-blue-500" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M5 12h14M5 12a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v4a2 2 0 01-2 2M5 12a2 2 0 00-2 2v4a2 2 0 002 2h14a2 2 0 002-2v-4a2 2 0 00-2-2m-2-4h.01M17 16h.01">
                                        </path>
                                    </svg>
                                    <a href="{{ route('apis.show', $api) }}"
                                        class="font-medium text-gray-700 hover:text-gray-800 hover:none">
                                        {{ $api->name }}
                                    </a>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($api->is_active)
                                    <span
                                        class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($api->should_notify)
                                    <span
                                        class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                        Ativo
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Inativo
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($api->latestStatusCheck)
                                    <span
                                        class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $api->latestStatusCheck->success ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        @if($api->latestStatusCheck->success)
                                            Online
                                        @else
                                            Offline
                                        @endif
                                    </span>
                                @else
                                    <span
                                        class="px-3 py-1 inline-flex text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Não verificado
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @forelse($api->tags as $tag)
                                    <span class="px-2 py-1 text-xs rounded-full text-sm font-medium text-white"
                                        style="background-color: {{ $tag->color }}">
                                        {{ $tag->name }}
                                    </span>
                                @empty
                                    <span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">Nenhuma Tag</span>
                                @endforelse
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                @if($api->latestStatusCheck)
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1 text-gray-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        {{ $api->latestStatusCheck->created_at->diffForHumans() }}
                                    </div>
                                @else
                                    N/A
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('apis.show', $api) }}"
                                        class="text-blue-500 hover:text-blue-600 p-1 rounded-full hover:bg-blue-50"
                                        title="Detalhes">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                    </a>
                                    <a href="{{ route('apis.edit', $api) }}"
                                        class="text-yellow-500 hover:text-yellow-700 p-1 rounded-full hover:bg-yellow-50"
                                        title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('apis.destroy', $api) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50"
                                            title="Remover"
                                            onclick="return confirm('Tem certeza que deseja remover esta API?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
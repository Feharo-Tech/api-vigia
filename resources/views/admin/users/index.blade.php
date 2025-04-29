@extends('layout')

@section('title', 'Usuários')

@section('content')
    <div class="flex justify-between items-center mb-8">
        <x-page-title>Usuários</x-page-title>
        <x-button-link href="{{ route('admin.users.create') }}">
            Adicionar Usuário
        </x-button-link>
    </div>


    @if($users->isEmpty())
        <div class="bg-blue-50 border border-blue-200 text-blue-800 px-4 py-3 rounded-lg">
            Você ainda não tem usuários cadastrados.
            <a href="{{ route('admin.users.create') }}" class="font-semibold hover:underline text-blue-600 hover:text-blue-800">
                Clique aqui
            </a>
            para adicionar seu primeiro usuário.
        </div>
    @else
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nome</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">E-mail</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ações</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($users as $user)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                {{ $user->name }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <span
                                    class="px-2 py-1 inline-flex text-xs font-semibold rounded-full {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    @if($user->is_active)
                                        Ativo
                                    @else
                                        Inativo
                                    @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex space-x-2">
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                        class="text-yellow-500 hover:text-yellow-700 p-1 rounded-full hover:bg-yellow-50"
                                        title="Editar">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="text-red-500 hover:text-red-700 p-1 rounded-full hover:bg-red-50" title="Remover"
                                            onclick="return confirm('Tem certeza que deseja excluir esta tag?')">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </form>
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST" class="inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit"
                                            class="p-1 rounded-full hover:bg-gray-100 {{ $user->is_active ? 'text-green-500 hover:text-green-700' : 'text-gray-500 hover:text-gray-700' }}"
                                            title="{{ $user->is_active ? 'Desativar usuário' : 'Ativar usuário' }}"
                                            onclick="return confirm('Tem certeza que deseja {{ $user->is_active ? 'desativar' : 'ativar' }} este usuário?')">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24"
                                                stroke="currentColor" stroke-width="2">
                                                @if ($user->is_active)
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M9 10V7a3 3 0 016 0v3m-7 0h8a2 2 0 012 2v5a2 2 0 01-2 2H7a2 2 0 01-2-2v-5a2 2 0 012-2h1z" />
                                                @else
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        d="M12 15v2m-6-6v5a2 2 0 002 2h8a2 2 0 002-2v-5a2 2 0 00-2-2h-1m-6 0V7a3 3 0 016 0v3" />
                                                @endif
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

@endsection
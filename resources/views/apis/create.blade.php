@extends('layout')

@section('title', 'Adicionar API')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-subpage-title>Adicionar API</x-subpage-title>

        <form action="{{ route('apis.store') }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome da API *</label>
                    <input type="text" name="name" id="name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        value="{{ old('name') }}">
                    @error('name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700">URL *</label>
                    <input type="url" name="url" id="url" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="https://api.example.com/endpoint" value="{{ old('url') }}">
                    @error('url')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="method" class="block text-sm font-medium text-gray-700">Método HTTP *</label>
                        <select name="method" id="method" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($methods as $method)
                                <option value="{{ $method }}" {{ old('method') == $method ? 'selected' : '' }}>{{ $method }}
                                </option>
                            @endforeach
                        </select>
                        @error('method')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="expected_status_code" class="block text-sm font-medium text-gray-700">Status Esperado
                            *</label>
                        <input type="number" name="expected_status_code" id="expected_status_code" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="200" value="{{ old('expected_status_code', 200) }}">
                        @error('expected_status_code')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="check_interval" class="block text-sm font-medium text-gray-700">Intervalo de Verificação
                            *</label>
                        <select name="check_interval" id="check_interval" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($intervals as $value => $label)
                                <option value="{{ $value }}" {{ old('check_interval', 5) == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('check_interval')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-end">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                {{ old('is_active', true) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Monitoramento
                                ativo</label>
                        </div>
                    </div>

                    <div class="flex items-end">
                        <div class="flex items-center">
                            <input type="checkbox" name="should_notify" id="should_notify"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                {{ old('should_notify', true) ? 'checked' : '' }}>
                            <label for="should_notify" class="ml-2 block text-sm font-medium text-gray-700">Habilitar
                                Notificação</label>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="error_threshold" class="block text-sm font-medium text-gray-700">Sensibilidade (erros
                            consecutivos) *</label>
                        <input type="number" name="error_threshold" id="error_threshold" min="1" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('error_threshold', 5) }}">
                        @error('error_threshold')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="timeout_threshold" class="block text-sm font-medium text-gray-700">Timeout máximo
                            (segundos) *</label>
                        <input type="number" name="timeout_threshold" id="timeout_threshold" min="1" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('timeout_threshold', 30) }}">
                        @error('timeout_threshold')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                        <select name="tags[]" id="tags" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-[42px]">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', [])) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('tags')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="content_type" class="block text-sm font-medium text-gray-700">Content-Type</label>
                        <select name="content_type" id="content_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="" {{ old('content_type') == '' ? 'selected' : '' }}>Nenhum</option>
                            @foreach($contentTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('content_type') == $value ? 'selected' : '' }}>{{ $label }} -
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                        @error('content_type')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="expected_response" class="block text-sm font-medium text-gray-700">Conteúdo Esperado na
                        Resposta (opcional)</label>
                    <textarea name="expected_response" id="expected_response" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('expected_response') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Se preenchido, o sistema verificará se a resposta contém este
                        texto.</p>
                    @error('expected_response')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="headers" class="block text-sm font-medium text-gray-700">Headers (opcional - JSON)</label>
                    <textarea name="headers" id="headers" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                        placeholder='{"Authorization": "Bearer token", "Content-Type": "application/json"}'>{{ old('headers') }}</textarea>
                    @error('headers')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700">Body (opcional - JSON)</label>
                    <textarea name="body" id="body" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                        placeholder='{"param1": "value1", "param2": "value2"}'>{{ old('body') }}</textarea>
                    @error('body')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('apis.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Salvar API
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new TomSelect('#tags', {
                plugins: ['remove_button'],
                create: false,
                maxItems: null
            });
        });
    </script>
@endsection
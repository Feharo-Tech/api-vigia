@extends('layout')

@section('title', 'Editar API')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-subpage-title>Editar API</x-subpage-title>

        <form action="{{ route('apis.update', $api) }}" method="POST" class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome da API *</label>
                    <input type="text" name="name" id="name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        value="{{ old('name', $api->name) }}">
                </div>

                <div>
                    <label for="url" class="block text-sm font-medium text-gray-700">URL *</label>
                    <input type="url" name="url" id="url" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        placeholder="https://api.example.com/endpoint" value="{{ old('url', $api->url) }}">
                </div>

                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label for="method" class="block text-sm font-medium text-gray-700">Método HTTP *</label>
                        <select name="method" id="method" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($methods as $method)
                                <option value="{{ $method }}" {{ old('method', $api->method) == $method ? 'selected' : '' }}>
                                    {{ $method }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="expected_status_code" class="block text-sm font-medium text-gray-700">Status Esperado
                            *</label>
                        <input type="number" name="expected_status_code" id="expected_status_code" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            placeholder="200" value="{{ old('expected_status_code', $api->expected_status_code) }}">
                    </div>

                    <div>
                        <label for="check_interval" class="block text-sm font-medium text-gray-700">Intervalo de Verificação
                            *</label>
                        <select name="check_interval" id="check_interval" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($intervals as $value => $label)
                                <option value="{{ $value }}" {{ old('check_interval', $api->check_interval) == $value ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex items-end">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                {{ old('is_active', $api->is_active) ? 'checked' : '' }}>
                            <label for="is_active" class="ml-2 block text-sm font-medium text-gray-700">Monitoramento
                                ativo</label>
                        </div>
                    </div>

                    <div class="flex items-end">
                        <div class="flex items-center">
                            <input type="checkbox" name="should_notify" id="should_notify"
                                class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                {{ old('should_notify', $api->should_notify) ? 'checked' : '' }}>
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
                            value="{{ old('error_threshold', $api->error_threshold) }}">
                    </div>

                    <div>
                        <label for="timeout_threshold" class="block text-sm font-medium text-gray-700">Timeout máximo
                            (segundos) *</label>
                        <input type="number" name="timeout_threshold" id="timeout_threshold" min="1" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('timeout_threshold', $api->timeout_threshold) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-4">
                    <div>
                        <label for="tags" class="block text-sm font-medium text-gray-700">Tags</label>
                        <select name="tags[]" id="tags" multiple
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 h-[42px]">
                            @foreach($tags as $tag)
                                <option value="{{ $tag->id }}" {{ in_array($tag->id, old('tags', $selectedTags ?? [])) ? 'selected' : '' }}>
                                    {{ $tag->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="content_type" class="block text-sm font-medium text-gray-700">Content-Type</label>
                        <select name="content_type" id="content_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="" {{ old('content_type') == '' ? 'selected' : '' }}>Nenhum</option>
                            @foreach($contentTypes as $value => $label)
                                <option value="{{ $value }}" {{ old('content_type', $api->content_type) == $value ? 'selected' : '' }}>{{ $label }} -
                                    {{ $value }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="certificate_id" class="block text-sm font-medium text-gray-700">Certificado
                            Digital</label>
                        <select name="certificate_id" id="certificate_id"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            <option value="" {{ old('certificate_id') == '' ? 'selected' : '' }}>Nenhum</option>
                            @foreach($certificates as $certificate)
                                <option value="{{ $certificate->id }}" {{ old('certificate_id', $api->certificate_id) == $certificate->id ? 'selected' : '' }}>
                                    {{ $certificate->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div>
                    <label for="expected_response" class="block text-sm font-medium text-gray-700">Conteúdo Esperado na
                        Resposta (opcional)</label>
                    <textarea name="expected_response" id="expected_response" rows="2"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">{{ old('expected_response', $api->expected_response) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Se preenchido, o sistema verificará se a resposta contém este
                        texto.</p>
                </div>

                <div>
                    <label for="headers" class="block text-sm font-medium text-gray-700">Headers (opcional - JSON)</label>
                    <textarea name="headers" id="headers" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                        placeholder='{"Authorization": "Bearer token", "Content-Type": "application/json"}'>{{ old('headers', is_array($api->headers) ? json_encode($api->headers, JSON_PRETTY_PRINT) : $api->headers) }}</textarea>
                </div>

                <div>
                    <label for="body" class="block text-sm font-medium text-gray-700">Body (opcional - JSON)</label>
                    <textarea name="body" id="body" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 font-mono text-sm"
                        placeholder='{"param1": "value1", "param2": "value2"}'>{{ old('body', is_array($api->body) ? json_encode($api->body, JSON_PRETTY_PRINT) : $api->body) }}</textarea>
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('apis.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Atualizar API
                </button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            new TomSelect('#tags', {
                plugins: ['remove_button'],
                create: false,
                maxItems: null,
                render: {
                    option: function (data, escape) {
                        return '<div class="flex items-center">' +
                            '<span class="inline-block w-3 h-3 rounded-full mr-2" style="background-color: ' + escape(data.$order.color) + '"></span>' +
                            escape(data.text) +
                            '</div>';
                    },
                    item: function (data, escape) {
                        return '<div class="flex items-center">' +
                            '<span class="inline-block w-3 h-3 rounded-full mr-2" style="background-color: ' + escape(data.$order.color) + '"></span>' +
                            escape(data.text) +
                            '</div>';
                    }
                },
                onInitialize: function () {
                    this.sync();
                }
            });
        });
    </script>
@endsection
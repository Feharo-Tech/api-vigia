@extends('layout')

@section('title', 'Editar Certificado')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-subpage-title>Editar Certificado</x-subpage-title>

        <form action="{{ route('admin.certificates.update', $certificate) }}" method="POST" enctype="multipart/form-data"
            class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700">Nome *</label>
                    <input type="text" name="name" id="name" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                        value="{{ old('name', $certificate->name) }}">
                </div>

                <div>
                    <label for="type" class="block text-sm font-medium text-gray-700">Tipo *</label>
                    <select name="type" id="type" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        @foreach($types as $type)
                            <option value="{{ $type }}" {{ old('type', strtoupper($certificate->type)) == $type ? 'selected' : '' }}>
                                {{ strtoupper($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div id="password-fields" class="grid grid-cols-2 gap-6 mt-6 hidden">
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Senha *</label>
                    <input type="password" name="password" id="password"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Senha
                        *</label>
                    <input type="password" name="password_confirmation" id="password_confirmation"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                </div>
            </div>

            <div x-data="{ fileName: '{{ $certificate->original_name }}' }" class="grid grid-cols-1 mt-6">
                <label for="file" class="block text-sm font-medium text-gray-700">Arquivo do Certificado</label>

                <input type="file" name="file" id="file" class="hidden" accept=".pfx,.pem"
                    @change="fileName = $event.target.files[0]?.name">

                <label for="file"
                    class="mt-1 flex items-center justify-between px-4 py-2 bg-white border border-gray-300 rounded-md shadow-sm cursor-pointer hover:bg-gray-50">
                    <span x-text="fileName || 'Selecione um arquivo...'"></span>
                    <span class="text-blue-600 text-sm">Procurar</span>
                </label>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('admin.certificates.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Atualizar Certificado
                </button>
            </div>
        </form>
    </div>

    <script>
        function togglePasswordFields() {
            const type = document.getElementById('type').value.toLowerCase();
            const passwordFields = document.getElementById('password-fields');

            if (type === 'pfx') {
                passwordFields.classList.remove('hidden');
                document.getElementById('password').setAttribute('required', true);
                document.getElementById('password_confirmation').setAttribute('required', true);
            } else {
                passwordFields.classList.add('hidden');
                document.getElementById('password').removeAttribute('required');
                document.getElementById('password_confirmation').removeAttribute('required');
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            togglePasswordFields();
            document.getElementById('type').addEventListener('change', togglePasswordFields);
        });
    </script>
@endsection
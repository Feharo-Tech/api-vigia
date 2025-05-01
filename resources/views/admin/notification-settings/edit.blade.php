@extends('layout')

@section('title', 'Configurações de Notificações')

@section('content')
    <div class="max-w-2xl mx-auto">
        <x-subpage-title>Configurações de Notificação</x-subpage-title>

        <form action="{{ route('admin.notification-settings.update') }}" method="POST"
            class="bg-white rounded-lg shadow p-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 gap-6">

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="notification_email" class="block text-sm font-medium text-gray-700">E-mail para
                            Notificação *</label>
                        <input type="email" name="notification_email" id="notification_email"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            value="{{ old('notification_email', $notificationSetting->notification_email) }}">
                        @error('notification_email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="notification_frequency" class="block text-sm font-medium text-gray-700">Frequência de
                            Notificação *</label>
                        <select name="notification_frequency" id="notification_frequency" required
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                            @foreach($frequencies as $value => $label)
                                <option value="{{ $value }}" {{ old('notification_frequency', $notificationSetting->notification_frequency) == $value ? 'selected' : '' }}>{{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('notification_frequency')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <div class="flex items-center">
                        <input type="checkbox" name="email_notifications" id="email_notifications"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                            {{ old('email_notifications', $notificationSetting->email_notifications ?? true) ? 'checked' : '' }}>
                        <label for="email_notifications" class="ml-2 font-medium block text-sm text-gray-700">Ativar
                            Notificações por E-mail</label>
                    </div>
                    @error('email_notifications')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-6 flex justify-end">
                <a href="{{ route('dashboard') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-800 px-4 py-2 rounded mr-2">
                    Cancelar
                </a>
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                    Salvar
                </button>
            </div>
        </form>
    </div>
@endsection
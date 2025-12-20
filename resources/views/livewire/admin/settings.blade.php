<div class="max-w-3xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Ajustes del sistema</h1>

    @if (session()->has('status'))
        <div class="mb-4 text-green-700 bg-green-100 border border-green-200 rounded px-4 py-2">
            {{ session('status') }}
        </div>
    @endif

    <div class="mb-6">
        <label class="block text-gray-700 mb-2">Seleccionar Configuración</label>
        <select wire:model="selectedUserId" class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm">
            <option value="">Configuración Global</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
            @endforeach
        </select>
    </div>

    <div class="space-y-6">
        @if(!$selectedUserId)
            <div class="flex items-center justify-between">
                <label class="text-gray-700">Permitir registro de nuevos usuarios</label>
                <input type="checkbox" wire:model="allow_registration" class="rounded">
            </div>
        @else
            <div class="flex items-center justify-between">
                <label class="text-gray-700">Usuario activo (Acceso al sistema)</label>
                <input type="checkbox" wire:model="is_active" class="rounded">
            </div>
        @endif

        <div class="flex items-center justify-between">
            <label class="text-gray-700">Permitir enviar mensajes de texto</label>
            <input type="checkbox" wire:model="allow_send_messages" class="rounded">
        </div>

        <div class="flex items-center justify-between">
            <label class="text-gray-700">Permitir enviar imágenes (incluye tomar foto)</label>
            <input type="checkbox" wire:model="allow_send_images" class="rounded">
        </div>

        <div class="flex items-center justify-between">
            <label class="text-gray-700">Permitir enviar audio</label>
            <input type="checkbox" wire:model="allow_send_audio" class="rounded">
        </div>

        <div>
            <x-jet-button wire:click="save">
                {{ $selectedUserId ? 'Guardar Permisos de Usuario' : 'Guardar Ajustes Globales' }}
            </x-jet-button>
        </div>
    </div>
</div>


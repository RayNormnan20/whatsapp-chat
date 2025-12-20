<div class="max-w-5xl mx-auto py-10">
    <h1 class="text-2xl font-semibold text-gray-800 mb-6">Administrar usuarios</h1>

    @if (session()->has('status'))
        <div class="mb-4 text-green-700 bg-green-100 border border-green-200 rounded px-4 py-2">
            {{ session('status') }}
        </div>
    @endif

    <div class="bg-white shadow rounded divide-y">
        <div class="px-4 py-2 font-medium text-gray-600 grid grid-cols-3 gap-4">
            <div>Nombre</div>
            <div>Email</div>
            <div>Rol</div>
        </div>
        @foreach ($list as $user)
            <div class="px-4 py-3 grid grid-cols-3 gap-4 items-center">
                <div class="text-gray-800">{{ $user->name }}</div>
                <div class="text-gray-600">{{ $user->email }}</div>
                <div>
                    <select wire:change="setRole({{ $user->id }}, $event.target.value)" class="border rounded px-2 py-1">
                        <option value="user" {{ $user->role === 'user' ? 'selected' : '' }}>Usuario</option>
                        <option value="admin" {{ $user->role === 'admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                </div>
            </div>
        @endforeach
    </div>
</div>


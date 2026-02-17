@extends('layouts.app')

@section('title', 'Pengaturan')

@section('content')
<div x-data="{ tab: 'api' }">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white">Pengaturan</h1>
        <p class="text-gray-400 mt-2">Kelola API key dan user management</p>
    </div>

    <!-- Tabs -->
    <div class="border-b border-gray-700 mb-6">
        <nav class="-mb-px flex space-x-8">
            <button
                @click="tab = 'api'"
                :class="tab === 'api' ? 'border-blue-500 text-blue-500' : 'border-transparent text-gray-400 hover:text-gray-300'"
                class="py-4 px-1 border-b-2 font-medium text-sm transition">
                Pengaturan API
            </button>
            <button
                @click="tab = 'users'"
                :class="tab === 'users' ? 'border-blue-500 text-blue-500' : 'border-transparent text-gray-400 hover:text-gray-300'"
                class="py-4 px-1 border-b-2 font-medium text-sm transition">
                Manajemen User
            </button>
        </nav>
    </div>

    <!-- Tab Content: API Settings -->
    <div x-show="tab === 'api'" class="bg-gray-800 rounded-lg p-6">
        <h2 class="text-xl font-semibold mb-4">Konfigurasi Groq API</h2>

        <form method="POST" action="{{ route('pengaturan.update-api') }}">
            @csrf

            <div class="space-y-4">
                <!-- API Key -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Groq API Key
                    </label>
                    <input
                        type="password"
                        name="groq_api_key"
                        value="{{ $pengaturan['groq_api_key']->nilai ?? '' }}"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="gsk_..."
                        required>
                    <p class="text-xs text-gray-400 mt-1">Dapatkan API key dari <a href="https://console.groq.com" target="_blank" class="text-blue-400 hover:underline">console.groq.com</a></p>
                </div>

                <!-- Model -->
                <div>
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        Model
                    </label>
                    <select
                        name="groq_model"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                        <option value="llama-3.3-70b-versatile" {{ ($pengaturan['groq_model']->nilai ?? '') === 'llama-3.3-70b-versatile' ? 'selected' : '' }}>Llama 3.3 70B Versatile</option>
                        <option value="llama-3.1-70b-versatile" {{ ($pengaturan['groq_model']->nilai ?? '') === 'llama-3.1-70b-versatile' ? 'selected' : '' }}>Llama 3.1 70B Versatile</option>
                        <option value="mixtral-8x7b-32768" {{ ($pengaturan['groq_model']->nilai ?? '') === 'mixtral-8x7b-32768' ? 'selected' : '' }}>Mixtral 8x7B</option>
                    </select>
                </div>

                <!-- Submit Button -->
                <div class="pt-4">
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition font-medium">
                        Simpan Pengaturan API
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- Tab Content: User Management -->
    <div x-show="tab === 'users'" x-data="{ showModal: false, editUser: null }">
        <!-- Add User Button -->
        <div class="mb-4">
            <button
                @click="showModal = true; editUser = null"
                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition font-medium">
                + Tambah User
            </button>
        </div>

        <!-- Users Table -->
        <div class="bg-gray-800 rounded-lg overflow-hidden">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Nama</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Email</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-700/50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center mr-3">
                                    <span class="text-white font-semibold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <div class="text-sm font-medium text-white">{{ $user->name }}</div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ $user->email }}</td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex gap-1">
                                @foreach($user->roles as $role)
                                <span class="px-2 py-1 text-xs rounded-full
                                    {{ $role->nama === 'admin' ? 'bg-red-900/50 text-red-300' : '' }}
                                    {{ $role->nama === 'supervisor' ? 'bg-yellow-900/50 text-yellow-300' : '' }}
                                    {{ $role->nama === 'cs' ? 'bg-blue-900/50 text-blue-300' : '' }}">
                                    {{ $role->label }}
                                </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                            <button
                                @click="editUser = {{ $user->toJson() }}; editUser.role_ids = {{ $user->roles->pluck('id')->toJson() }}; showModal = true"
                                class="text-blue-400 hover:text-blue-300 mr-3">
                                Edit
                            </button>
                            @if($user->id !== auth()->id())
                            <form method="POST" action="{{ route('pengaturan.hapus-user', $user) }}" class="inline" onsubmit="return confirm('Yakin hapus user ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300">Hapus</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-gray-400">Belum ada user</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modal Tambah/Edit User -->
        <div
            x-show="showModal"
            x-cloak
            class="fixed inset-0 z-50 overflow-y-auto"
            style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <!-- Backdrop -->
                <div
                    @click="showModal = false"
                    class="fixed inset-0 bg-black bg-opacity-75 transition-opacity"></div>

                <!-- Modal Content -->
                <div class="relative bg-gray-800 rounded-lg max-w-md w-full p-6 z-10">
                    <h3 class="text-xl font-semibold mb-4" x-text="editUser ? 'Edit User' : 'Tambah User'"></h3>

                    <form
                        :action="editUser ? '{{ url('pengaturan/user') }}/' + editUser.id : '{{ route('pengaturan.tambah-user') }}'"
                        method="POST">
                        @csrf
                        <template x-if="editUser">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <div class="space-y-4">
                            <!-- Nama -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Nama</label>
                                <input
                                    type="text"
                                    name="name"
                                    :value="editUser ? editUser.name : ''"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Email</label>
                                <input
                                    type="email"
                                    name="email"
                                    :value="editUser ? editUser.email : ''"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    required>
                            </div>

                            <!-- Password -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    Password <span x-show="editUser" class="text-xs text-gray-400">(kosongkan jika tidak diubah)</span>
                                </label>
                                <input
                                    type="password"
                                    name="password"
                                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
                                    :required="!editUser">
                            </div>

                            <!-- Roles -->
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">Role</label>
                                <div class="space-y-2">
                                    @foreach($roles as $role)
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            name="roles[]"
                                            value="{{ $role->id }}"
                                            :checked="editUser && editUser.role_ids.includes({{ $role->id }})"
                                            class="rounded bg-gray-700 border-gray-600 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-300">{{ $role->label }}</span>
                                    </label>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Buttons -->
                            <div class="flex justify-end space-x-3 pt-4">
                                <button
                                    type="button"
                                    @click="showModal = false"
                                    class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg transition">
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition">
                                    Simpan
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection


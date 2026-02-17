@extends('layouts.app')

@section('title', 'Pengaturan AI Provider')

@section('content')
<div x-data="aiProviderApp()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white flex items-center">
            <svg class="h-8 w-8 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
            </svg>
            Pengaturan AI Provider
        </h1>
        <p class="text-gray-400 mt-2">Kelola multiple AI providers dengan auto-rotation dan quota management</p>
    </div>

    <!-- Toast Notification -->
    <div
        x-show="showToast"
        x-transition
        class="fixed top-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2 z-50"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span x-text="toastMessage"></span>
    </div>

    <!-- Global Providers -->
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="h-6 w-6 mr-2 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Global AI Providers
        </h2>
        <p class="text-gray-400 text-sm mb-4">Provider yang tersedia untuk semua user</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($globalProviders as $provider)
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-5">
                <!-- Provider Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            {{ $provider->nama }}
                            @if($provider->aktif)
                            <span class="ml-2 px-2 py-1 bg-green-600 text-white text-xs rounded-full">Aktif</span>
                            @else
                            <span class="ml-2 px-2 py-1 bg-gray-600 text-gray-300 text-xs rounded-full">Nonaktif</span>
                            @endif
                        </h3>
                        <p class="text-sm text-gray-400 mt-1">{{ $provider->model }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500">Prioritas</span>
                        <p class="text-lg font-bold text-blue-400">{{ $provider->prioritas }}</p>
                    </div>
                </div>

                <!-- API Key Input -->
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-300 mb-2">API Key</label>
                    <div class="flex space-x-2">
                        <div class="flex-1 relative">
                            <input
                                :type="showApiKey[{{ $provider->id }}] ? 'text' : 'password'"
                                x-model="apiKeys[{{ $provider->id }}]"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="{{ $provider->api_key ? '••••••••••••••••' : 'Masukkan API key' }}"
                            >
                            <button
                                @click="showApiKey[{{ $provider->id }}] = !showApiKey[{{ $provider->id }}]"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                            >
                                <svg x-show="!showApiKey[{{ $provider->id }}]" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showApiKey[{{ $provider->id }}]" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <button
                            @click="updateApiKey({{ $provider->id }})"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200"
                        >
                            Simpan
                        </button>
                    </div>
                </div>

                <!-- Quota Info -->
                @if($provider->quota_limit)
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-400">Quota Usage</span>
                        <span class="text-white font-semibold">{{ $provider->quota_used }} / {{ $provider->quota_limit }}</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div
                            class="h-2 rounded-full {{ $provider->quota_used / $provider->quota_limit > 0.8 ? 'bg-red-500' : ($provider->quota_used / $provider->quota_limit > 0.5 ? 'bg-yellow-500' : 'bg-green-500') }}"
                            style="width: {{ min(100, ($provider->quota_used / $provider->quota_limit) * 100) }}%"
                        ></div>
                    </div>
                    @if($provider->quota_reset_date)
                    <p class="text-xs text-gray-500 mt-1">Reset: {{ \Carbon\Carbon::parse($provider->quota_reset_date)->format('d M Y') }}</p>
                    @endif
                </div>
                @endif

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-xs text-gray-400">Last Used</p>
                        <p class="text-sm text-white font-semibold">
                            {{ $provider->last_used_at ? \Carbon\Carbon::parse($provider->last_used_at)->diffForHumans() : 'Never' }}
                        </p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-xs text-gray-400">Error Count</p>
                        <p class="text-sm {{ $provider->error_count > 0 ? 'text-red-400' : 'text-green-400' }} font-semibold">
                            {{ $provider->error_count }}
                        </p>
                    </div>
                </div>

                <!-- Error Message -->
                @if($provider->last_error_message)
                <div class="mb-3 bg-red-900/30 border border-red-700 rounded-lg p-3">
                    <p class="text-xs text-red-400 font-semibold mb-1">Last Error:</p>
                    <p class="text-xs text-red-300">{{ Str::limit($provider->last_error_message, 100) }}</p>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button
                        @click="toggleAktif({{ $provider->id }})"
                        class="flex-1 {{ $provider->aktif ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg transition duration-200"
                    >
                        {{ $provider->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    @if($provider->quota_limit)
                    <button
                        @click="resetQuota({{ $provider->id }})"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200"
                        title="Reset Quota"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- User Providers (jika ada) -->
    @if(count($userProviders) > 0)
    <div class="mb-8">
        <h2 class="text-xl font-semibold text-white mb-4 flex items-center">
            <svg class="h-6 w-6 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            My Personal Providers
        </h2>
        <p class="text-gray-400 text-sm mb-4">Provider pribadi hanya untuk akun Anda</p>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            @foreach($userProviders as $provider)
            <!-- Same card structure as global providers -->
            <div class="bg-gray-800 rounded-lg border border-green-700 p-5">
                <!-- Provider Header -->
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            {{ $provider->nama }}
                            @if($provider->aktif)
                            <span class="ml-2 px-2 py-1 bg-green-600 text-white text-xs rounded-full">Aktif</span>
                            @else
                            <span class="ml-2 px-2 py-1 bg-gray-600 text-gray-300 text-xs rounded-full">Nonaktif</span>
                            @endif
                            <span class="ml-2 px-2 py-1 bg-green-600 text-white text-xs rounded-full">Personal</span>
                        </h3>
                        <p class="text-sm text-gray-400 mt-1">{{ $provider->model }}</p>
                    </div>
                    <div class="text-right">
                        <span class="text-xs text-gray-500">Prioritas</span>
                        <p class="text-lg font-bold text-blue-400">{{ $provider->prioritas }}</p>
                    </div>
                </div>

                <!-- API Key Input -->
                <div class="mb-3">
                    <label class="block text-sm font-medium text-gray-300 mb-2">API Key</label>
                    <div class="flex space-x-2">
                        <div class="flex-1 relative">
                            <input
                                :type="showApiKey[{{ $provider->id }}] ? 'text' : 'password'"
                                x-model="apiKeys[{{ $provider->id }}]"
                                class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                placeholder="{{ $provider->api_key ? '••••••••••••••••' : 'Masukkan API key' }}"
                            >
                            <button
                                @click="showApiKey[{{ $provider->id }}] = !showApiKey[{{ $provider->id }}]"
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-white"
                            >
                                <svg x-show="!showApiKey[{{ $provider->id }}]" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                                <svg x-show="showApiKey[{{ $provider->id }}]" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path>
                                </svg>
                            </button>
                        </div>
                        <button
                            @click="updateApiKey({{ $provider->id }})"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition duration-200"
                        >
                            Simpan
                        </button>
                    </div>
                </div>

                <!-- Quota Info -->
                @if($provider->quota_limit)
                <div class="mb-3">
                    <div class="flex justify-between text-sm mb-1">
                        <span class="text-gray-400">Quota Usage</span>
                        <span class="text-white font-semibold">{{ $provider->quota_used }} / {{ $provider->quota_limit }}</span>
                    </div>
                    <div class="w-full bg-gray-700 rounded-full h-2">
                        <div
                            class="h-2 rounded-full {{ $provider->quota_used / $provider->quota_limit > 0.8 ? 'bg-red-500' : ($provider->quota_used / $provider->quota_limit > 0.5 ? 'bg-yellow-500' : 'bg-green-500') }}"
                            style="width: {{ min(100, ($provider->quota_used / $provider->quota_limit) * 100) }}%"
                        ></div>
                    </div>
                    @if($provider->quota_reset_date)
                    <p class="text-xs text-gray-500 mt-1">Reset: {{ \Carbon\Carbon::parse($provider->quota_reset_date)->format('d M Y') }}</p>
                    @endif
                </div>
                @endif

                <!-- Stats -->
                <div class="grid grid-cols-2 gap-3 mb-3">
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-xs text-gray-400">Last Used</p>
                        <p class="text-sm text-white font-semibold">
                            {{ $provider->last_used_at ? \Carbon\Carbon::parse($provider->last_used_at)->diffForHumans() : 'Never' }}
                        </p>
                    </div>
                    <div class="bg-gray-700 rounded-lg p-3">
                        <p class="text-xs text-gray-400">Error Count</p>
                        <p class="text-sm {{ $provider->error_count > 0 ? 'text-red-400' : 'text-green-400' }} font-semibold">
                            {{ $provider->error_count }}
                        </p>
                    </div>
                </div>

                <!-- Error Message -->
                @if($provider->last_error_message)
                <div class="mb-3 bg-red-900/30 border border-red-700 rounded-lg p-3">
                    <p class="text-xs text-red-400 font-semibold mb-1">Last Error:</p>
                    <p class="text-xs text-red-300">{{ Str::limit($provider->last_error_message, 100) }}</p>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button
                        @click="toggleAktif({{ $provider->id }})"
                        class="flex-1 {{ $provider->aktif ? 'bg-yellow-600 hover:bg-yellow-700' : 'bg-green-600 hover:bg-green-700' }} text-white px-4 py-2 rounded-lg transition duration-200"
                    >
                        {{ $provider->aktif ? 'Nonaktifkan' : 'Aktifkan' }}
                    </button>
                    @if($provider->quota_limit)
                    <button
                        @click="resetQuota({{ $provider->id }})"
                        class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition duration-200"
                        title="Reset Quota"
                    >
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Info Box -->
    <div class="bg-blue-900/30 border border-blue-700 rounded-lg p-5">
        <h3 class="text-lg font-semibold text-blue-300 mb-3 flex items-center">
            <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            Cara Kerja Auto-Rotation
        </h3>
        <ul class="text-sm text-blue-200 space-y-2">
            <li class="flex items-start">
                <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Provider dipilih berdasarkan <strong>prioritas</strong> (angka terkecil = prioritas tertinggi)</span>
            </li>
            <li class="flex items-start">
                <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Jika quota habis atau error, sistem otomatis switch ke provider berikutnya</span>
            </li>
            <li class="flex items-start">
                <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Provider dengan <strong>5 error berturut-turut</strong> akan dinonaktifkan otomatis</span>
            </li>
            <li class="flex items-start">
                <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Quota akan <strong>reset otomatis</strong> sesuai tanggal reset yang ditentukan</span>
            </li>
        </ul>
    </div>
</div>
@endsection

@push('scripts')
<script>
function aiProviderApp() {
    return {
        apiKeys: {},
        showApiKey: {},
        showToast: false,
        toastMessage: '',

        async updateApiKey(providerId) {
            const apiKey = this.apiKeys[providerId];

            if (!apiKey || apiKey.trim() === '') {
                this.showToastMessage('API key tidak boleh kosong');
                return;
            }

            try {
                const response = await fetch(`/ai-provider/${providerId}/api-key`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({ api_key: apiKey })
                });

                const data = await response.json();

                if (data.sukses) {
                    this.showToastMessage('✅ API key berhasil disimpan');
                    this.apiKeys[providerId] = '';

                    // Reload page setelah 1 detik
                    setTimeout(() => {
                        window.location.reload();
                    }, 1000);
                } else {
                    this.showToastMessage('❌ ' + data.pesan);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showToastMessage('❌ Gagal menyimpan API key');
            }
        },

        async toggleAktif(providerId) {
            try {
                const response = await fetch(`/ai-provider/${providerId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.sukses) {
                    this.showToastMessage('✅ ' + data.pesan);

                    // Reload page setelah 500ms
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    this.showToastMessage('❌ ' + data.pesan);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showToastMessage('❌ Gagal mengubah status provider');
            }
        },

        async resetQuota(providerId) {
            if (!confirm('Reset quota untuk provider ini?')) {
                return;
            }

            try {
                const response = await fetch(`/ai-provider/${providerId}/reset-quota`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    }
                });

                const data = await response.json();

                if (data.sukses) {
                    this.showToastMessage('✅ Quota berhasil direset');

                    // Reload page setelah 500ms
                    setTimeout(() => {
                        window.location.reload();
                    }, 500);
                } else {
                    this.showToastMessage('❌ ' + data.pesan);
                }
            } catch (error) {
                console.error('Error:', error);
                this.showToastMessage('❌ Gagal reset quota');
            }
        },

        showToastMessage(message) {
            this.toastMessage = message;
            this.showToast = true;

            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    }
}
</script>
@endpush


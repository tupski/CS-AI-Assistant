@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div x-data="dashboardApp()" x-init="init()">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-white">Dashboard CS AI Assistant</h1>
        <p class="text-gray-400 mt-2">Generate jawaban otomatis untuk chat member dengan AI</p>
    </div>

    <!-- Layout 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- KOLOM KIRI: Input -->
        <div class="space-y-4">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-6">
                <h2 class="text-xl font-semibold mb-4 flex items-center">
                    <svg class="h-6 w-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Chat dari Member
                </h2>

                <!-- Textarea Input -->
                <textarea
                    x-model="pesanMember"
                    rows="10"
                    class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"
                    placeholder="Paste chat dari member di sini..."
                    :disabled="loading"
                ></textarea>

                <!-- Error Message -->
                <div x-show="errorMessage" class="mt-3 bg-red-900/50 border border-red-700 text-red-300 px-4 py-2 rounded-lg text-sm">
                    <span x-text="errorMessage"></span>
                </div>

                <!-- Buttons -->
                <div class="mt-4 flex space-x-3">
                    <button
                        @click="generateJawaban()"
                        :disabled="loading || !pesanMember.trim()"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-lg transition duration-200 flex items-center justify-center space-x-2"
                    >
                        <svg x-show="!loading" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <svg x-show="loading" class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Generating...' : 'Generate Jawaban'"></span>
                    </button>

                    <button
                        @click="bersihkan()"
                        :disabled="loading"
                        class="bg-gray-600 hover:bg-gray-700 disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-semibold py-3 px-6 rounded-lg transition duration-200"
                    >
                        Bersihkan
                    </button>
                </div>

                <!-- Kategori Terdeteksi -->
                <div x-show="kategori" class="mt-4 bg-purple-900/30 border border-purple-700/50 rounded-lg px-4 py-3">
                    <p class="text-sm text-purple-300">
                        <span class="font-semibold">Kategori Terdeteksi:</span>
                        <span x-text="kategori" class="text-purple-100"></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- KOLOM KANAN: Output -->
        <div class="space-y-4">
            <!-- Card Formal -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-blue-900/30 border-b border-blue-700/50 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="font-semibold text-blue-300">Versi Formal</h3>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="regenerate('formal')"
                            :disabled="!jawabanFormal || loadingRegenerate === 'formal'"
                            class="bg-blue-500 hover:bg-blue-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg x-show="loadingRegenerate !== 'formal'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg x-show="loadingRegenerate === 'formal'" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loadingRegenerate === 'formal' ? 'Loading...' : 'Regenerate'"></span>
                        </button>
                        <button
                            @click="salin('formal')"
                            :disabled="!jawabanFormal"
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Salin</span>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <textarea
                        x-model="jawabanFormal"
                        rows="6"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"
                        placeholder="Jawaban formal akan muncul di sini..."
                    ></textarea>
                </div>
            </div>

            <!-- Card Santai -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-green-900/30 border-b border-green-700/50 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="font-semibold text-green-300">Versi Santai</h3>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="regenerate('santai')"
                            :disabled="!jawabanSantai || loadingRegenerate === 'santai'"
                            class="bg-green-500 hover:bg-green-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg x-show="loadingRegenerate !== 'santai'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg x-show="loadingRegenerate === 'santai'" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loadingRegenerate === 'santai' ? 'Loading...' : 'Regenerate'"></span>
                        </button>
                        <button
                            @click="salin('santai')"
                            :disabled="!jawabanSantai"
                            class="bg-green-600 hover:bg-green-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Salin</span>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <textarea
                        x-model="jawabanSantai"
                        rows="6"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 resize-none"
                        placeholder="Jawaban santai akan muncul di sini..."
                    ></textarea>
                </div>
            </div>

            <!-- Card Singkat -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-orange-900/30 border-b border-orange-700/50 px-4 py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-2">
                        <svg class="h-5 w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h3 class="font-semibold text-orange-300">Versi Singkat</h3>
                    </div>
                    <div class="flex gap-2">
                        <button
                            @click="regenerate('singkat')"
                            :disabled="!jawabanSingkat || loadingRegenerate === 'singkat'"
                            class="bg-orange-500 hover:bg-orange-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg x-show="loadingRegenerate !== 'singkat'" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg x-show="loadingRegenerate === 'singkat'" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-text="loadingRegenerate === 'singkat' ? 'Loading...' : 'Regenerate'"></span>
                        </button>
                        <button
                            @click="salin('singkat')"
                            :disabled="!jawabanSingkat"
                            class="bg-orange-600 hover:bg-orange-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span>Salin</span>
                        </button>
                    </div>
                </div>
                <div class="p-4">
                    <textarea
                        x-model="jawabanSingkat"
                        rows="6"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 resize-none"
                        placeholder="Jawaban singkat akan muncul di sini..."
                    ></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div
        x-show="showToast"
        x-transition
        class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-lg flex items-center space-x-2"
    >
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span x-text="toastMessage"></span>
    </div>
</div>
@endsection

@push('scripts')
<script>
function dashboardApp() {
    return {
        pesanMember: '',
        kategori: '',
        jawabanFormal: '',
        jawabanSantai: '',
        jawabanSingkat: '',
        loading: false,
        loadingRegenerate: null,
        errorMessage: '',
        showToast: false,
        toastMessage: '',

        init() {
            // Setup CSRF token untuk semua request AJAX
            const token = document.querySelector('meta[name="csrf-token"]').content;
            window.axios = window.axios || {};
            window.axios.defaults = window.axios.defaults || {};
            window.axios.defaults.headers = window.axios.defaults.headers || {};
            window.axios.defaults.headers.common = window.axios.defaults.headers.common || {};
            window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token;
        },

        async generateJawaban() {
            if (!this.pesanMember.trim()) {
                this.errorMessage = 'Pesan member tidak boleh kosong';
                return;
            }

            this.loading = true;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route("dashboard.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        pesan_member: this.pesanMember
                    })
                });

                const data = await response.json();

                if (data.sukses) {
                    this.kategori = data.data.kategori;
                    this.jawabanFormal = data.data.formal;
                    this.jawabanSantai = data.data.santai;
                    this.jawabanSingkat = data.data.singkat;

                    this.tampilkanToast('Jawaban berhasil di-generate! ✨');
                } else {
                    this.errorMessage = data.pesan || 'Terjadi kesalahan';
                }
            } catch (error) {
                console.error('Error:', error);
                this.errorMessage = 'Gagal menghubungi server. Coba lagi.';
            } finally {
                this.loading = false;
            }
        },

        async regenerate(tipe) {
            if (!this.pesanMember.trim()) {
                this.errorMessage = 'Pesan member tidak boleh kosong';
                return;
            }

            this.loadingRegenerate = tipe;
            this.errorMessage = '';

            try {
                const response = await fetch('{{ route("dashboard.generate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        pesan_member: this.pesanMember
                    })
                });

                const data = await response.json();

                if (data.sukses) {
                    // Update hanya tipe yang di-regenerate
                    switch(tipe) {
                        case 'formal':
                            this.jawabanFormal = data.data.formal;
                            break;
                        case 'santai':
                            this.jawabanSantai = data.data.santai;
                            break;
                        case 'singkat':
                            this.jawabanSingkat = data.data.singkat;
                            break;
                    }

                    this.tampilkanToast(`Jawaban ${tipe} berhasil di-regenerate! 🔄`);
                } else {
                    this.errorMessage = data.pesan || 'Terjadi kesalahan';
                }
            } catch (error) {
                console.error('Error:', error);
                this.errorMessage = 'Gagal menghubungi server. Coba lagi.';
            } finally {
                this.loadingRegenerate = null;
            }
        },

        bersihkan() {
            this.pesanMember = '';
            this.kategori = '';
            this.jawabanFormal = '';
            this.jawabanSantai = '';
            this.jawabanSingkat = '';
            this.errorMessage = '';
        },

        async salin(tipe) {
            let teks = '';

            switch(tipe) {
                case 'formal':
                    teks = this.jawabanFormal;
                    break;
                case 'santai':
                    teks = this.jawabanSantai;
                    break;
                case 'singkat':
                    teks = this.jawabanSingkat;
                    break;
            }

            if (!teks) return;

            try {
                await navigator.clipboard.writeText(teks);
                this.tampilkanToast(`Jawaban ${tipe} berhasil disalin! 📋`);
            } catch (error) {
                console.error('Error menyalin:', error);
                this.errorMessage = 'Gagal menyalin teks';
            }
        },

        tampilkanToast(pesan) {
            this.toastMessage = pesan;
            this.showToast = true;

            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        }
    }
}
</script>
@endpush


@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div x-data="dashboardApp()" x-init="init()">
    <!-- Header -->
    <div class="mb-4 md:mb-6">
        <h1 class="text-2xl md:text-3xl font-bold text-white">Dashboard CS AI Assistant</h1>
        <p class="text-sm md:text-base text-gray-400 mt-1 md:mt-2">Generate jawaban otomatis untuk chat member dengan AI</p>
    </div>

    <!-- AI Provider Stats Widget (Collapsible) -->
    @if(count($providers) > 0)
    <div class="mb-4 md:mb-6 bg-gradient-to-r from-blue-900/30 to-purple-900/30 border border-blue-700 rounded-lg p-3 md:p-5" x-data="{ showProviders: false }">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 mb-2">
            <button @click="showProviders = !showProviders" class="text-base md:text-lg font-semibold text-white flex items-center hover:text-blue-300 transition-colors">
                <svg class="h-4 w-4 md:h-5 md:w-5 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
                AI Provider Status
                <svg class="h-4 w-4 ml-2 transition-transform" :class="showProviders ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-400">{{ $providers->where('aktif', true)->count() }} Active</span>
                <a href="{{ route('ai-provider.index') }}" class="text-xs md:text-sm text-blue-400 hover:text-blue-300 flex items-center">
                    Kelola
                    <svg class="h-3 w-3 md:h-4 md:w-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </a>
            </div>
        </div>

        <div x-show="showProviders" x-collapse class="mt-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 md:gap-3">
                @foreach($providers->take(4) as $provider)
                <div class="bg-gray-800/50 rounded-lg p-2.5 md:p-3 border {{ $provider->aktif ? 'border-green-700' : 'border-gray-700' }}">
                    <div class="flex items-center justify-between mb-1.5 md:mb-2">
                        <span class="text-xs font-semibold text-gray-400 truncate mr-2">{{ $provider->nama }}</span>
                        @if($provider->aktif)
                        <span class="w-2 h-2 bg-green-500 rounded-full flex-shrink-0"></span>
                        @else
                        <span class="w-2 h-2 bg-gray-500 rounded-full flex-shrink-0"></span>
                        @endif
                    </div>
                    <p class="text-xs md:text-sm text-white font-semibold mb-1 truncate">{{ $provider->model }}</p>
                    @if($provider->quota_limit)
                    <div class="mt-2">
                        <div class="flex justify-between text-xs mb-1">
                            <span class="text-gray-400">Quota</span>
                            <span class="text-white">{{ $provider->quota_used }}/{{ $provider->quota_limit }}</span>
                        </div>
                        <div class="w-full bg-gray-700 rounded-full h-1.5">
                            <div
                                class="h-1.5 rounded-full {{ $provider->quota_used / $provider->quota_limit > 0.8 ? 'bg-red-500' : ($provider->quota_used / $provider->quota_limit > 0.5 ? 'bg-yellow-500' : 'bg-green-500') }}"
                                style="width: {{ min(100, ($provider->quota_used / $provider->quota_limit) * 100) }}%"
                            ></div>
                        </div>
                    </div>
                    @else
                    <p class="text-xs text-gray-500 mt-2">Unlimited quota</p>
                    @endif
                </div>
                @endforeach
            </div>

            @if(count($providers) > 4)
            <p class="text-xs text-gray-400 mt-3 text-center">
                Dan {{ count($providers) - 4 }} provider lainnya.
                <a href="{{ route('ai-provider.index') }}" class="text-blue-400 hover:text-blue-300">Lihat semua →</a>
            </p>
            @endif
        </div>
    </div>
    @endif

    <!-- Quick Search FAQ -->
    <div class="mb-4 md:mb-6 bg-gray-800 rounded-lg border border-gray-700 p-4" x-data="{ searchQuery: '', searchResults: [], searching: false }">
        <div class="flex items-center gap-3">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
            <input type="text"
                   x-model="searchQuery"
                   @input.debounce.300ms="searchFAQ()"
                   placeholder="Cari di FAQ... (ketik minimal 3 karakter)"
                   class="flex-1 bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <svg x-show="searching" class="animate-spin h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
        </div>

        <!-- Search Results -->
        <div x-show="searchResults.length > 0" class="mt-3 space-y-2 max-h-60 overflow-y-auto">
            <template x-for="result in searchResults" :key="result.id">
                <a :href="`/faq?highlight=${encodeURIComponent(searchQuery)}&id=${result.id}`"
                   class="block bg-gray-700/50 hover:bg-gray-700 rounded-lg p-3 transition-colors border border-gray-600 hover:border-blue-500">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <p class="text-white font-medium mb-1" x-html="highlightText(result.judul, searchQuery)"></p>
                            <p class="text-sm text-gray-400 line-clamp-2" x-html="highlightText(result.isi, searchQuery)"></p>
                        </div>
                        <span x-show="result.kategori"
                              class="px-2 py-1 rounded text-xs font-medium shadow-lg flex-shrink-0"
                              :style="result.kategori ? `background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.5)), ${result.kategori.warna}50; color: ${result.kategori.warna}; border: 2px solid ${result.kategori.warna}; text-shadow: 0 1px 2px rgba(0,0,0,0.8);` : ''"
                              x-text="result.kategori ? `${result.kategori.icon} ${result.kategori.nama}` : ''">
                        </span>
                    </div>
                </a>
            </template>
        </div>

        <p x-show="searchQuery.length >= 3 && searchResults.length === 0 && !searching" class="text-sm text-gray-400 mt-3 text-center">
            Tidak ada hasil untuk "<span x-text="searchQuery"></span>"
        </p>
    </div>

    <!-- Layout 2 Kolom -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- KOLOM KIRI: Input -->
        <div class="space-y-3 md:space-y-4">
            <div class="bg-gray-800 rounded-lg border border-gray-700 p-4 md:p-6">
                <h2 class="text-lg md:text-xl font-semibold mb-3 md:mb-4 flex items-center">
                    <svg class="h-5 w-5 md:h-6 md:w-6 mr-2 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"></path>
                    </svg>
                    Chat dari Member
                </h2>

                <!-- Pilih AI Provider -->
                @if(count($providers) > 1)
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-300 mb-2">
                        <svg class="inline h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        AI Model
                    </label>
                    <select
                        x-model="providerId"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-2 text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        :disabled="loading"
                    >
                        <option value="">Auto (Rotasi Otomatis)</option>
                        @foreach($providers as $provider)
                        <option value="{{ $provider->id }}">
                            {{ $provider->nama }} - {{ $provider->model }}
                            @if($provider->quota_limit)
                                ({{ $provider->quota_used }}/{{ $provider->quota_limit }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-gray-400 mt-1">
                        💡 Auto akan memilih provider terbaik dan rotasi otomatis jika quota habis
                    </p>
                </div>
                @endif

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
                <div class="mt-3 md:mt-4 flex flex-col sm:flex-row gap-2 sm:gap-3">
                    <button
                        @click="generateJawaban()"
                        :disabled="loading || !pesanMember.trim()"
                        class="flex-1 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-lg transition duration-200 flex items-center justify-center space-x-2 text-sm md:text-base"
                    >
                        <svg x-show="!loading" class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <svg x-show="loading" class="animate-spin h-4 w-4 md:h-5 md:w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span x-text="loading ? 'Generating...' : 'Generate Jawaban'"></span>
                    </button>

                    <button
                        @click="bersihkan()"
                        :disabled="loading"
                        class="bg-gray-600 hover:bg-gray-700 disabled:bg-gray-700 disabled:cursor-not-allowed text-white font-semibold py-2.5 md:py-3 px-4 md:px-6 rounded-lg transition duration-200 text-sm md:text-base"
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
        <div class="space-y-3 md:space-y-4">
            <!-- Card Formal -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-blue-900/30 border-b border-blue-700/50 px-3 md:px-4 py-2 md:py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 md:space-x-2">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="text-sm md:text-base font-semibold text-blue-300">Versi Formal</h3>
                    </div>
                    <div class="flex gap-1.5 md:gap-2">
                        <button
                            @click="regenerate('formal')"
                            :disabled="!jawabanFormal || loadingRegenerate === 'formal'"
                            class="bg-blue-500 hover:bg-blue-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-2 md:px-3 py-1 rounded text-xs md:text-sm transition duration-200 flex items-center space-x-1"
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
                            class="bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-2 md:px-3 py-1 rounded text-xs md:text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg class="h-3 w-3 md:h-4 md:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span class="hidden sm:inline">Salin</span>
                        </button>
                    </div>
                </div>
                <div class="p-3 md:p-4">
                    <textarea
                        x-model="jawabanFormal"
                        rows="6"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500 resize-none"
                        placeholder="Jawaban formal akan muncul di sini..."
                    ></textarea>
                </div>
            </div>

            <!-- Card Santai -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-green-900/30 border-b border-green-700/50 px-3 md:px-4 py-2 md:py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 md:space-x-2">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h3 class="text-sm md:text-base font-semibold text-green-300">Versi Santai</h3>
                    </div>
                    <div class="flex gap-1.5 md:gap-2">
                        <button
                            @click="regenerate('santai')"
                            :disabled="!jawabanSantai || loadingRegenerate === 'santai'"
                            class="bg-green-500 hover:bg-green-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-2 md:px-3 py-1 rounded text-xs md:text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg x-show="loadingRegenerate !== 'santai'" class="h-3 w-3 md:h-4 md:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg x-show="loadingRegenerate === 'santai'" class="animate-spin h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="hidden sm:inline" x-text="loadingRegenerate === 'santai' ? 'Loading...' : 'Regenerate'"></span>
                        </button>
                        <button
                            @click="salin('santai')"
                            :disabled="!jawabanSantai"
                            class="bg-green-600 hover:bg-green-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-2 md:px-3 py-1 rounded text-xs md:text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg class="h-3 w-3 md:h-4 md:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span class="hidden sm:inline">Salin</span>
                        </button>
                    </div>
                </div>
                <div class="p-3 md:p-4">
                    <textarea
                        x-model="jawabanSantai"
                        rows="6"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-white focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-500 resize-none"
                        placeholder="Jawaban santai akan muncul di sini..."
                    ></textarea>
                </div>
            </div>

            <!-- Card Singkat -->
            <div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
                <div class="bg-orange-900/30 border-b border-orange-700/50 px-3 md:px-4 py-2 md:py-3 flex items-center justify-between">
                    <div class="flex items-center space-x-1.5 md:space-x-2">
                        <svg class="h-4 w-4 md:h-5 md:w-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                        </svg>
                        <h3 class="text-sm md:text-base font-semibold text-orange-300">Versi Singkat</h3>
                    </div>
                    <div class="flex gap-1.5 md:gap-2">
                        <button
                            @click="regenerate('singkat')"
                            :disabled="!jawabanSingkat || loadingRegenerate === 'singkat'"
                            class="bg-orange-500 hover:bg-orange-600 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-2 md:px-3 py-1 rounded text-xs md:text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg x-show="loadingRegenerate !== 'singkat'" class="h-3 w-3 md:h-4 md:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                            </svg>
                            <svg x-show="loadingRegenerate === 'singkat'" class="animate-spin h-3 w-3 md:h-4 md:w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span class="hidden sm:inline" x-text="loadingRegenerate === 'singkat' ? 'Loading...' : 'Regenerate'"></span>
                        </button>
                        <button
                            @click="salin('singkat')"
                            :disabled="!jawabanSingkat"
                            class="bg-orange-600 hover:bg-orange-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-2 md:px-3 py-1 rounded text-xs md:text-sm transition duration-200 flex items-center space-x-1"
                        >
                            <svg class="h-3 w-3 md:h-4 md:w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                            </svg>
                            <span class="hidden sm:inline">Salin</span>
                        </button>
                    </div>
                </div>
                <div class="p-3 md:p-4">
                    <textarea
                        x-model="jawabanSingkat"
                        rows="6"
                        class="w-full bg-gray-700 border border-gray-600 rounded-lg px-3 md:px-4 py-2 md:py-3 text-sm md:text-base text-white focus:outline-none focus:border-orange-500 focus:ring-1 focus:ring-orange-500 resize-none"
                        placeholder="Jawaban singkat akan muncul di sini..."
                    ></textarea>
                </div>
            </div>

            <!-- FAQ Relevan Section -->
            <div x-show="faqRelevan.length > 0" x-transition class="mt-4 md:mt-6">
                <div class="bg-purple-900/30 border border-purple-700 rounded-lg p-3 md:p-5">
                    <h3 class="text-base md:text-lg font-semibold text-purple-300 mb-2 md:mb-3 flex items-center">
                        <svg class="h-4 w-4 md:h-5 md:w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        FAQ Relevan
                    </h3>
                    <p class="text-xs md:text-sm text-purple-200 mb-3 md:mb-4">Referensi FAQ yang mungkin sesuai dengan pertanyaan member:</p>

                    <div class="space-y-2 md:space-y-3">
                        <template x-for="(faq, index) in faqRelevan" :key="index">
                            <div class="bg-gray-800 rounded-lg p-3 md:p-4 border border-purple-700/50" x-data="{ expanded: false }">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5 md:mb-2">
                                            <span x-show="faq.kategori"
                                                  class="inline-block px-2 py-0.5 md:py-1 text-xs rounded-full shadow-lg"
                                                  :style="faq.kategori ? `background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.5)), ${faq.kategori.warna}50; color: ${faq.kategori.warna}; border: 2px solid ${faq.kategori.warna}; text-shadow: 0 1px 2px rgba(0,0,0,0.8);` : ''"
                                                  x-text="faq.kategori ? `${faq.kategori.icon} ${faq.kategori.nama}` : ''">
                                            </span>
                                        </div>
                                        <h4 class="text-sm md:text-base font-semibold text-white mb-1.5 md:mb-2 flex items-start">
                                            <svg class="h-3.5 w-3.5 md:h-4 md:w-4 mr-1 mt-0.5 text-purple-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <span class="break-words" x-text="faq.pertanyaan"></span>
                                        </h4>
                                    </div>
                                    <button
                                        @click="expanded = !expanded"
                                        class="ml-2 text-purple-400 hover:text-purple-300 transition flex-shrink-0"
                                    >
                                        <svg x-show="!expanded" class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        <svg x-show="expanded" class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="expanded" x-collapse>
                                    <div class="mt-2 md:mt-3 pt-2 md:pt-3 border-t border-purple-700/30">
                                        <p class="text-xs md:text-sm text-gray-300 whitespace-pre-wrap break-words" x-text="faq.jawaban"></p>
                                    </div>
                                </div>
                                <div x-show="!expanded">
                                    <p class="text-xs md:text-sm text-gray-400 line-clamp-2 break-words" x-text="faq.jawaban"></p>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Peraturan Relevan Section -->
            <div x-show="peraturanRelevan.length > 0" x-transition class="mt-4 md:mt-6">
                <div class="bg-indigo-900/30 border border-indigo-700 rounded-lg p-3 md:p-5">
                    <h3 class="text-base md:text-lg font-semibold text-indigo-300 mb-2 md:mb-3 flex items-center">
                        <svg class="h-4 w-4 md:h-5 md:w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        Peraturan Relevan
                    </h3>
                    <p class="text-xs md:text-sm text-indigo-200 mb-3 md:mb-4">Peraturan yang mungkin terkait dengan pertanyaan member:</p>

                    <div class="space-y-2 md:space-y-3">
                        <template x-for="(peraturan, index) in peraturanRelevan" :key="index">
                            <div class="bg-gray-800 rounded-lg p-3 md:p-4 border border-indigo-700/50" x-data="{ expanded: false }">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-1.5 md:mb-2">
                                            <span class="inline-block px-2 py-0.5 md:py-1 bg-indigo-600/30 text-indigo-300 text-xs rounded-full border border-indigo-600/50 truncate" x-text="peraturan.tipe"></span>
                                            <span class="inline-block px-2 py-0.5 md:py-1 bg-yellow-600/30 text-yellow-300 text-xs rounded-full border border-yellow-600/50">
                                                P<span x-text="peraturan.prioritas"></span>
                                            </span>
                                        </div>
                                        <h4 class="text-sm md:text-base font-semibold text-white mb-1.5 md:mb-2 flex items-start">
                                            <svg class="h-3.5 w-3.5 md:h-4 md:w-4 mr-1 mt-0.5 text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                            </svg>
                                            <span class="break-words" x-text="peraturan.judul"></span>
                                        </h4>
                                    </div>
                                    <button
                                        @click="expanded = !expanded"
                                        class="ml-2 text-indigo-400 hover:text-indigo-300 transition flex-shrink-0"
                                    >
                                        <svg x-show="!expanded" class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                        <svg x-show="expanded" class="h-4 w-4 md:h-5 md:w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                                <div x-show="expanded" x-collapse>
                                    <div class="mt-2 md:mt-3 pt-2 md:pt-3 border-t border-indigo-700/30">
                                        <p class="text-xs md:text-sm text-gray-300 whitespace-pre-wrap break-words" x-text="peraturan.isi"></p>
                                    </div>
                                </div>
                                <div x-show="!expanded">
                                    <p class="text-xs md:text-sm text-gray-400 line-clamp-2 break-words" x-text="peraturan.isi"></p>
                                </div>
                            </div>
                        </template>
                    </div>
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
        providerId: '', // Provider yang dipilih (kosong = auto)
        kategori: '',
        jawabanFormal: '',
        jawabanSantai: '',
        jawabanSingkat: '',
        faqRelevan: [], // FAQ yang relevan dengan pertanyaan
        peraturanRelevan: [], // Peraturan yang relevan dengan pertanyaan
        loading: false,
        loadingRegenerate: null,
        errorMessage: '',
        showToast: false,
        toastMessage: '',
        memoryId: null, // Simpan memory ID untuk tracking

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
                        pesan_member: this.pesanMember,
                        provider_id: this.providerId || null
                    })
                });

                const data = await response.json();

                if (data.sukses) {
                    this.kategori = data.data.kategori;
                    this.jawabanFormal = data.data.formal;
                    this.jawabanSantai = data.data.santai;
                    this.jawabanSingkat = data.data.singkat;
                    this.faqRelevan = data.data.faq_relevan || []; // FAQ relevan
                    this.peraturanRelevan = data.data.peraturan_relevan || []; // Peraturan relevan
                    this.memoryId = data.data.memory_id; // Simpan memory ID

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
                        pesan_member: this.pesanMember,
                        provider_id: this.providerId || null
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

                    // Update memory ID jika ada
                    if (data.data.memory_id) {
                        this.memoryId = data.data.memory_id;
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

                // Track copy ke database
                if (this.memoryId) {
                    this.trackCopy(tipe);
                }
            } catch (error) {
                console.error('Error menyalin:', error);
                this.errorMessage = 'Gagal menyalin teks';
            }
        },

        async trackCopy(tipe) {
            try {
                await fetch('{{ route("dashboard.track-copy") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        memory_id: this.memoryId,
                        tipe_jawaban: tipe
                    })
                });
                // Silent tracking, tidak perlu notifikasi
            } catch (error) {
                console.error('Error tracking copy:', error);
                // Silent fail, tidak ganggu user experience
            }
        },

        tampilkanToast(pesan) {
            this.toastMessage = pesan;
            this.showToast = true;

            setTimeout(() => {
                this.showToast = false;
            }, 3000);
        },

        async searchFAQ() {
            if (this.searchQuery.length < 3) {
                this.searchResults = [];
                return;
            }

            this.searching = true;
            try {
                const response = await fetch(`/faq?ajax=1&search=${encodeURIComponent(this.searchQuery)}`);
                const data = await response.json();
                if (data.sukses) {
                    this.searchResults = data.data;
                }
            } catch (error) {
                console.error('Error searching FAQ:', error);
            } finally {
                this.searching = false;
            }
        },

        highlightText(text, query) {
            if (!query || !text) return text;
            const regex = new RegExp(`(${query})`, 'gi');
            return text.replace(regex, '<mark class="bg-yellow-400 text-gray-900 px-1 rounded">$1</mark>');
        }
    }
}
</script>
@endpush


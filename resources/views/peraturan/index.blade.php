@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Peraturan & Guidelines CS</h1>
            <p class="text-gray-400 mt-1">Aturan dan panduan untuk Customer Service</p>
        </div>
        @if(auth()->user()->isAdmin())
        <button @click="showModal = true; editMode = false; resetForm()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Peraturan
        </button>
        @endif
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="bg-green-500/20 border border-green-500 text-green-400 px-4 py-3 rounded-lg mb-6">
        {{ session('success') }}
    </div>
    @endif

    @if(session('error'))
    <div class="bg-red-500/20 border border-red-500 text-red-400 px-4 py-3 rounded-lg mb-6">
        {{ session('error') }}
    </div>
    @endif

    <div x-data="peraturanManager()">
        <!-- Filter & Search -->
        <div class="bg-gray-800 rounded-lg p-4 mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" x-model="filters.search" @input.debounce.500ms="loadPeraturan()"
                           placeholder="Cari peraturan..."
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-48">
                    <select x-model="filters.tipe" @change="loadPeraturan()"
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="umum">📋 Umum</option>
                        <option value="wajib">✅ Wajib</option>
                        <option value="larangan">🚫 Larangan</option>
                        <option value="tips">💡 Tips</option>
                    </select>
                </div>
                <div class="w-48">
                    <select x-model="filters.prioritas" @change="loadPeraturan()"
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Prioritas</option>
                        <option value="tinggi">🔥 Tinggi</option>
                        <option value="normal">Normal</option>
                        <option value="rendah">Rendah</option>
                    </select>
                </div>
                <button @click="resetFilters()" type="button"
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Reset
                </button>
            </div>
        </div>

        <!-- Loading State -->
        <div x-show="loading" class="text-center py-8">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <p class="text-gray-400 mt-2">Memuat peraturan...</p>
        </div>

        <!-- Peraturan List Container -->
        <div id="peraturan-list" x-show="!loading">
            @include('peraturan.partials.list', ['peraturansGrouped' => $peraturansGrouped])
        </div>

        <!-- Modal Tambah/Edit Peraturan (Admin Only) -->
        @if(auth()->user()->isAdmin())
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-75" @click="showModal = false"></div>

                <div class="relative bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold text-white mb-4" x-text="editMode ? 'Edit Peraturan' : 'Tambah Peraturan Baru'"></h3>

                    <form :action="editMode ? `/peraturan/${editId}` : '{{ route('peraturan.store') }}'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="editMode ? 'PUT' : 'POST'">

                        <div class="space-y-4">
                            <!-- Judul -->
                            <div>
                                <label class="block text-gray-300 mb-2">Judul Peraturan <span class="text-red-400">*</span></label>
                                <input type="text" name="judul" x-model="formData.judul" required
                                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Contoh: Selalu gunakan bahasa yang sopan">
                                @error('judul')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Isi -->
                            <div>
                                <label class="block text-gray-300 mb-2">Isi Peraturan <span class="text-red-400">*</span></label>
                                <textarea name="isi" x-model="formData.isi" required rows="5"
                                          class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Tulis detail peraturan atau guideline..."></textarea>
                                @error('isi')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Tipe & Prioritas -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-300 mb-2">Tipe <span class="text-red-400">*</span></label>
                                    <select name="tipe" x-model="formData.tipe" required
                                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="umum">📋 Umum</option>
                                        <option value="wajib">✅ Wajib</option>
                                        <option value="larangan">🚫 Larangan</option>
                                        <option value="tips">💡 Tips</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-gray-300 mb-2">Prioritas <span class="text-red-400">*</span></label>
                                    <select name="prioritas" x-model="formData.prioritas" required
                                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        <option value="tinggi">🔥 Tinggi</option>
                                        <option value="normal">Normal</option>
                                        <option value="rendah">Rendah</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Urutan & Aktif -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-gray-300 mb-2">Urutan</label>
                                    <input type="number" name="urutan" x-model="formData.urutan" min="0"
                                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                           placeholder="0">
                                    <p class="text-gray-500 text-xs mt-1">Urutan tampilan (0 = paling atas)</p>
                                </div>
                                <div>
                                    <label class="block text-gray-300 mb-2">Status</label>
                                    <label class="flex items-center gap-3 bg-gray-700 rounded-lg px-4 py-2 cursor-pointer">
                                        <input type="checkbox" name="aktif" x-model="formData.aktif" value="1"
                                               class="w-5 h-5 text-blue-600 bg-gray-600 border-gray-500 rounded focus:ring-blue-500">
                                        <span class="text-white">Peraturan Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showModal = false"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                                Batal
                            </button>
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                <span x-text="editMode ? 'Update' : 'Simpan'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
function peraturanManager() {
    return {
        showModal: false,
        editMode: false,
        editId: null,
        loading: false,
        filters: {
            search: '{{ request('search') }}',
            tipe: '{{ request('tipe') }}',
            prioritas: '{{ request('prioritas') }}'
        },
        formData: {
            judul: '',
            isi: '',
            tipe: 'umum',
            prioritas: 'normal',
            aktif: true,
            urutan: 0
        },

        // Load peraturan dengan AJAX
        async loadPeraturan() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filters.search) params.append('search', this.filters.search);
                if (this.filters.tipe) params.append('tipe', this.filters.tipe);
                if (this.filters.prioritas) params.append('prioritas', this.filters.prioritas);

                const response = await fetch(`{{ route('peraturan.index') }}?${params.toString()}`, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    document.getElementById('peraturan-list').innerHTML = data.html;
                }
            } catch (error) {
                console.error('Error loading peraturan:', error);
            } finally {
                this.loading = false;
            }
        },

        // Reset filters
        resetFilters() {
            this.filters = {
                search: '',
                tipe: '',
                prioritas: ''
            };
            this.loadPeraturan();
        },

        resetForm() {
            this.formData = {
                judul: '',
                isi: '',
                tipe: 'umum',
                prioritas: 'normal',
                aktif: true,
                urutan: 0
            };
        },

        editPeraturan(id, judul, isi, tipe, prioritas, aktif, urutan) {
            this.editMode = true;
            this.editId = id;
            this.formData = {
                judul: judul,
                isi: isi,
                tipe: tipe,
                prioritas: prioritas,
                aktif: aktif,
                urutan: urutan
            };
            this.showModal = true;
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
</style>
@endsection


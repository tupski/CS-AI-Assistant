@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Kelola FAQ</h1>
            <p class="text-gray-400 mt-1">Manage frequently asked questions</p>
        </div>
        <div class="flex gap-2">
            <button @click="showUploadModal = true"
                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                Upload Excel
            </button>
            <button @click="showModal = true; editMode = false; resetForm()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah FAQ
            </button>
        </div>
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

    <div x-data="faqManager()">
        <!-- Filter & Search -->
        <div class="bg-gray-800 rounded-lg p-4 mb-6">
            <div class="flex gap-4">
                <div class="flex-1">
                    <input type="text" x-model="filters.search" @input.debounce.500ms="loadFaqs()"
                           placeholder="Cari FAQ..."
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-64">
                    <select x-model="filters.kategori_id" @change="loadFaqs()"
                            class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}">{{ $kat->nama }}</option>
                        @endforeach
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
            <p class="text-gray-400 mt-2">Memuat FAQ...</p>
        </div>

        <!-- FAQ Table -->
        <div class="bg-gray-800 rounded-lg overflow-hidden" x-show="!loading">
            <table class="w-full">
                <thead class="bg-gray-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Kategori</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Judul</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Isi</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-300 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-700">
                    <template x-for="faq in faqs" :key="faq.id">
                        <tr :id="`faq-${faq.id}`" class="hover:bg-gray-700/50 transition-all duration-300">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <template x-if="faq.kategori">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium shadow-lg"
                                          :style="`background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.5)), ${faq.kategori.warna}50; color: ${faq.kategori.warna}; border: 2px solid ${faq.kategori.warna}; text-shadow: 0 1px 2px rgba(0,0,0,0.8);`"
                                          x-text="`${faq.kategori.icon} ${faq.kategori.nama}`">
                                    </span>
                                </template>
                                <template x-if="!faq.kategori">
                                    <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">
                                        Tanpa Kategori
                                    </span>
                                </template>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium" x-text="faq.judul"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-400 text-sm">
                                    <span x-show="faq.isi.length <= 100" x-text="faq.isi"></span>
                                    <span x-show="faq.isi.length > 100" x-text="faq.isi.substring(0, 100) + '...'"></span>
                                    <button x-show="faq.isi.length > 100" @click="showDetail(faq)"
                                            class="text-blue-400 hover:text-blue-300 ml-2">
                                        Lihat Detail
                                    </button>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-right space-x-2">
                                <button @click="salinIsi(faq.isi)"
                                        class="text-green-400 hover:text-green-300">
                                    Salin
                                </button>
                                <button @click="editFaq(faq.id, faq.judul, faq.isi, faq.kategori_id)"
                                        class="text-blue-400 hover:text-blue-300">
                                    Edit
                                </button>
                                <button @click="hapusFaq(faq.id)"
                                        class="text-red-400 hover:text-red-300">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </template>
                    <template x-if="faqs.length === 0">
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                                Tidak ada FAQ. Klik "Tambah FAQ" untuk membuat yang baru.
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>

        <!-- Modal Detail FAQ -->
        <div x-show="showDetailModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-75" @click="showDetailModal = false"></div>

                <div class="relative bg-gray-800 rounded-lg max-w-3xl w-full p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-white" x-text="detailFaq.judul"></h3>
                        <button @click="showDetailModal = false" class="text-gray-400 hover:text-white">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    <div class="mb-4" x-show="detailFaq.kategori">
                        <span class="px-3 py-1 rounded-full text-xs font-medium shadow-lg"
                              :style="detailFaq.kategori ? `background: linear-gradient(135deg, rgba(0,0,0,0.7), rgba(0,0,0,0.5)), ${detailFaq.kategori.warna}50; color: ${detailFaq.kategori.warna}; border: 2px solid ${detailFaq.kategori.warna}; text-shadow: 0 1px 2px rgba(0,0,0,0.8);` : ''"
                              x-text="detailFaq.kategori ? `${detailFaq.kategori.icon} ${detailFaq.kategori.nama}` : ''">
                        </span>
                    </div>

                    <div class="bg-gray-700 rounded-lg p-4 mb-4 max-h-96 overflow-y-auto">
                        <p class="text-gray-300 whitespace-pre-wrap" x-text="detailFaq.isi"></p>
                    </div>

                    <div class="flex justify-end gap-2">
                        <button @click="salinIsi(detailFaq.isi)"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                            </svg>
                            Salin Isi
                        </button>
                        <button @click="showDetailModal = false"
                                class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Upload Excel -->
        <div x-show="showUploadModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-75" @click="showUploadModal = false"></div>

                <div class="relative bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold text-white mb-4">Upload FAQ dari Excel</h3>

                    <!-- Download Template Button -->
                    <div class="mb-4">
                        <a href="{{ route('faq.template') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-lg font-semibold shadow-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            Download Template CSV
                        </a>
                    </div>

                    <div class="mb-4 bg-blue-900/30 border border-blue-700 rounded-lg p-4">
                        <h4 class="text-blue-300 font-semibold mb-2">📋 Format File:</h4>
                        <ul class="text-sm text-blue-200 space-y-1">
                            <li>• Kolom 1: <strong>kategori_id</strong> (ID kategori, opsional)</li>
                            <li>• Kolom 2: <strong>judul</strong> (Pertanyaan FAQ)</li>
                            <li>• Kolom 3: <strong>isi</strong> (Jawaban FAQ)</li>
                        </ul>
                    </div>

                    <form @submit.prevent="uploadExcel" enctype="multipart/form-data">
                        <!-- Drag & Drop Zone -->
                        <div class="mb-4">
                            <label class="block text-gray-300 mb-2">Upload File <span class="text-red-400">*</span></label>
                            <div @drop.prevent="handleDrop"
                                 @dragover.prevent="isDragging = true"
                                 @dragleave.prevent="isDragging = false"
                                 @dragenter.prevent
                                 :class="isDragging ? 'border-green-500 bg-green-900/20' : 'border-gray-600'"
                                 class="border-2 border-dashed rounded-lg p-8 text-center transition-all cursor-pointer hover:border-green-500 hover:bg-gray-700/50"
                                 @click="$refs.fileInput.click()">
                                <input type="file"
                                       @change="handleFileSelect"
                                       accept=".csv,.xlsx,.xls"
                                       class="hidden"
                                       x-ref="fileInput">

                                <div x-show="!selectedFile">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    <p class="mt-2 text-gray-300">Klik atau drag & drop file di sini</p>
                                    <p class="text-sm text-gray-400 mt-1">CSV, XLSX, atau XLS (Max 2MB)</p>
                                </div>

                                <div x-show="selectedFile" class="text-green-400">
                                    <svg class="mx-auto h-12 w-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <p class="mt-2 font-semibold" x-text="selectedFile ? selectedFile.name : ''"></p>
                                    <p class="text-sm text-gray-400" x-text="selectedFile ? (selectedFile.size / 1024).toFixed(2) + ' KB' : ''"></p>
                                </div>
                            </div>
                        </div>

                        <div x-show="uploadProgress > 0 && uploadProgress < 100" class="mb-4">
                            <div class="bg-gray-700 rounded-full h-2">
                                <div class="bg-green-600 h-2 rounded-full transition-all duration-300" :style="`width: ${uploadProgress}%`"></div>
                            </div>
                            <p class="text-sm text-gray-400 mt-1" x-text="`Uploading: ${uploadProgress}%`"></p>
                        </div>

                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showUploadModal = false"
                                    class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg">
                                Batal
                            </button>
                            <button type="submit" :disabled="!selectedFile || uploading"
                                    class="bg-green-600 hover:bg-green-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white px-4 py-2 rounded-lg flex items-center gap-2">
                                <svg x-show="!uploading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                </svg>
                                <svg x-show="uploading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="uploading ? 'Uploading...' : 'Upload'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Tambah/Edit FAQ -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-75" @click="showModal = false"></div>

                <div class="relative bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold text-white mb-4" x-text="editMode ? 'Edit FAQ' : 'Tambah FAQ Baru'"></h3>

                    <form @submit.prevent="simpanFaq">
                        <div class="space-y-4">
                            <!-- Kategori -->
                            <div>
                                <label class="block text-gray-300 mb-2">Kategori</label>
                                <select name="kategori_id" x-model="formData.kategori_id"
                                        class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Pilih Kategori (Opsional)</option>
                                    @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}">{{ $kat->icon }} {{ $kat->nama }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Judul -->
                            <div>
                                <label class="block text-gray-300 mb-2">Judul FAQ <span class="text-red-400">*</span></label>
                                <input type="text" name="judul" x-model="formData.judul" required
                                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Contoh: Bagaimana cara melakukan pembayaran?">
                                @error('judul')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Isi -->
                            <div>
                                <label class="block text-gray-300 mb-2">Isi Jawaban <span class="text-red-400">*</span></label>
                                <textarea name="isi" x-model="formData.isi" required rows="6"
                                          class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Tulis jawaban lengkap untuk FAQ ini..."></textarea>
                                @error('isi')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        <!-- Buttons -->
                        <div class="flex justify-end gap-3 mt-6">
                            <button type="button" @click="showModal = false"
                                    class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg">
                                Batal
                            </button>
                            <button type="submit" :disabled="loading"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-600 disabled:cursor-not-allowed text-white rounded-lg flex items-center gap-2">
                                <svg x-show="loading" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span x-text="loading ? 'Menyimpan...' : (editMode ? 'Update' : 'Simpan')"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function faqManager() {
    return {
        showModal: false,
        showDetailModal: false,
        showUploadModal: false,
        editMode: false,
        editId: null,
        loading: false,
        uploading: false,
        uploadProgress: 0,
        selectedFile: null,
        isDragging: false,
        faqs: [],
        detailFaq: {},
        filters: {
            search: '',
            kategori_id: ''
        },
        formData: {
            kategori_id: '',
            judul: '',
            isi: ''
        },

        init() {
            this.loadFaqs();
            this.handleHighlight();
        },

        handleHighlight() {
            // Ambil parameter dari URL
            const urlParams = new URLSearchParams(window.location.search);
            const highlight = urlParams.get('highlight');
            const faqId = urlParams.get('id');

            if (highlight) {
                this.filters.search = highlight;

                // Jika ada ID, scroll ke FAQ tersebut setelah load
                if (faqId) {
                    setTimeout(() => {
                        const element = document.getElementById(`faq-${faqId}`);
                        if (element) {
                            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                            element.classList.add('ring-2', 'ring-yellow-400');
                            setTimeout(() => {
                                element.classList.remove('ring-2', 'ring-yellow-400');
                            }, 3000);
                        }
                    }, 500);
                }
            }
        },

        async loadFaqs() {
            this.loading = true;
            try {
                const params = new URLSearchParams();
                if (this.filters.search) params.append('search', this.filters.search);
                if (this.filters.kategori_id) params.append('kategori_id', this.filters.kategori_id);
                params.append('ajax', '1');

                const response = await fetch(`{{ route('faq.index') }}?${params}`);
                const data = await response.json();

                if (data.sukses) {
                    this.faqs = data.data;
                }
            } catch (error) {
                console.error('Error loading FAQs:', error);
            } finally {
                this.loading = false;
            }
        },

        resetFilters() {
            this.filters = {
                search: '',
                kategori_id: ''
            };
            this.loadFaqs();
        },

        showDetail(faq) {
            this.detailFaq = faq;
            this.showDetailModal = true;
        },

        async salinIsi(isi) {
            try {
                await navigator.clipboard.writeText(isi);
                showNotification('success', 'Berhasil!', 'Isi FAQ berhasil disalin');
            } catch (error) {
                console.error('Error copying:', error);
                showNotification('error', 'Gagal!', 'Gagal menyalin isi FAQ');
            }
        },

        resetForm() {
            this.formData = {
                kategori_id: '',
                judul: '',
                isi: ''
            };
        },

        editFaq(id, judul, isi, kategori_id) {
            this.editMode = true;
            this.editId = id;
            this.formData = {
                kategori_id: kategori_id || '',
                judul: judul,
                isi: isi
            };
            this.showModal = true;
        },

        async simpanFaq() {
            this.loading = true;

            try {
                const url = this.editMode ? `/faq/${this.editId}` : '{{ route("faq.store") }}';
                const method = this.editMode ? 'PUT' : 'POST';

                const response = await fetch(url, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(this.formData)
                });

                const data = await response.json();

                if (data.sukses) {
                    showNotification('success', 'Berhasil!', data.pesan || 'FAQ berhasil disimpan');
                    this.showModal = false;
                    this.resetForm();
                    this.loadFaqs();
                } else {
                    showNotification('error', 'Gagal!', data.pesan || 'Gagal menyimpan FAQ');
                }
            } catch (error) {
                console.error('Error saving FAQ:', error);
                showNotification('error', 'Error!', 'Terjadi kesalahan saat menyimpan FAQ');
            } finally {
                this.loading = false;
            }
        },

        async hapusFaq(id) {
            const result = await showConfirm('Hapus FAQ?', 'FAQ yang dihapus tidak dapat dikembalikan');
            if (!result.isConfirmed) return;

            try {
                const response = await fetch(`/faq/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });

                const data = await response.json();

                if (data.sukses) {
                    showNotification('success', 'Berhasil!', 'FAQ berhasil dihapus');
                    this.loadFaqs();
                } else {
                    showNotification('error', 'Gagal!', data.pesan || 'Gagal menghapus FAQ');
                }
            } catch (error) {
                console.error('Error deleting FAQ:', error);
                showNotification('error', 'Error!', 'Terjadi kesalahan saat menghapus FAQ');
            }
        },

        handleFileSelect(event) {
            this.selectedFile = event.target.files[0];
            this.uploadProgress = 0;
            this.isDragging = false;
        },

        handleDrop(event) {
            this.isDragging = false;
            const files = event.dataTransfer.files;
            if (files.length > 0) {
                this.selectedFile = files[0];
                this.uploadProgress = 0;
            }
        },

        async uploadExcel() {
            if (!this.selectedFile) {
                showNotification('warning', 'Perhatian!', 'Pilih file terlebih dahulu');
                return;
            }

            this.uploading = true;
            this.uploadProgress = 0;

            const formData = new FormData();
            formData.append('file', this.selectedFile);

            try {
                const xhr = new XMLHttpRequest();

                xhr.upload.addEventListener('progress', (e) => {
                    if (e.lengthComputable) {
                        this.uploadProgress = Math.round((e.loaded / e.total) * 100);
                    }
                });

                xhr.addEventListener('load', () => {
                    if (xhr.status === 200) {
                        const data = JSON.parse(xhr.responseText);
                        if (data.sukses) {
                            showNotification('success', 'Berhasil!', `Berhasil import ${data.imported} FAQ`);
                            this.showUploadModal = false;
                            this.selectedFile = null;
                            this.uploadProgress = 0;
                            this.loadFaqs();
                        } else {
                            showNotification('error', 'Gagal!', data.pesan || 'Gagal upload file');
                        }
                    } else {
                        showNotification('error', 'Error!', 'Gagal upload file');
                    }
                    this.uploading = false;
                });

                xhr.addEventListener('error', () => {
                    showNotification('error', 'Error!', 'Terjadi kesalahan saat upload file');
                    this.uploading = false;
                });

                xhr.open('POST', '{{ route("faq.import") }}');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                xhr.send(formData);
            } catch (error) {
                console.error('Error uploading:', error);
                alert('Gagal upload file');
                this.uploading = false;
            }
        }
    }
}
</script>

<style>
[x-cloak] { display: none !important; }
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>
@endsection


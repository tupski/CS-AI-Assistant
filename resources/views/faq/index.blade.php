@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Kelola FAQ</h1>
            <p class="text-gray-400 mt-1">Manage frequently asked questions</p>
        </div>
        <button @click="showModal = true; editMode = false; resetForm()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah FAQ
        </button>
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
                        <tr class="hover:bg-gray-700/50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-3 py-1 rounded-full text-xs font-medium"
                                      :class="faq.kategori ? `bg-${faq.kategori.warna || 'gray'}-500/20 text-${faq.kategori.warna || 'gray'}-400` : 'bg-gray-500/20 text-gray-400'"
                                      x-text="faq.kategori ? `${faq.kategori.icon} ${faq.kategori.nama}` : 'Tanpa Kategori'">
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-white font-medium" x-text="faq.judul"></div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-gray-400 text-sm">
                                    <span x-show="faq.isi.length <= 100" x-text="faq.isi"></span>
                                    <span x-show="faq.isi.length > 100" x-text="faq.isi.substring(0, 100) + '...'"></span>
                                    <button x-show="faq.isi.length > 100" @click="showDetailModal(faq)"
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
                        <span class="px-3 py-1 rounded-full text-xs font-medium"
                              :class="detailFaq.kategori ? `bg-${detailFaq.kategori.warna || 'gray'}-500/20 text-${detailFaq.kategori.warna || 'gray'}-400` : 'bg-gray-500/20 text-gray-400'"
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

        <!-- Modal Tambah/Edit FAQ -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-75" @click="showModal = false"></div>

                <div class="relative bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold text-white mb-4" x-text="editMode ? 'Edit FAQ' : 'Tambah FAQ Baru'"></h3>

                    <form :action="editMode ? `/faq/${editId}` : '{{ route('faq.store') }}'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="editMode ? 'PUT' : 'POST'">

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
                            <button type="submit"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg">
                                <span x-text="editMode ? 'Update' : 'Simpan'"></span>
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
        editMode: false,
        editId: null,
        loading: false,
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

        showDetailModal(faq) {
            this.detailFaq = faq;
            this.showDetailModal = true;
        },

        async salinIsi(isi) {
            try {
                await navigator.clipboard.writeText(isi);
                alert('Isi FAQ berhasil disalin!');
            } catch (error) {
                console.error('Error copying:', error);
                alert('Gagal menyalin isi FAQ');
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

        async hapusFaq(id) {
            if (!confirm('Yakin hapus FAQ ini?')) return;

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
                    alert('FAQ berhasil dihapus!');
                    this.loadFaqs();
                } else {
                    alert(data.pesan || 'Gagal menghapus FAQ');
                }
            } catch (error) {
                console.error('Error deleting FAQ:', error);
                alert('Gagal menghapus FAQ');
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


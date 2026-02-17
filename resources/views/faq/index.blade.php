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
            <form method="GET" action="{{ route('faq.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari FAQ..."
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-64">
                    <select name="kategori_id" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori_id') == $kat->id ? 'selected' : '' }}>
                            {{ $kat->nama }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Filter
                </button>
                @if(request('search') || request('kategori_id'))
                <a href="{{ route('faq.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- FAQ Table -->
        <div class="bg-gray-800 rounded-lg overflow-hidden">
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
                    @forelse($faqs as $faq)
                    <tr class="hover:bg-gray-700/50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($faq->kategori)
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-{{ $faq->kategori->warna ?? 'gray' }}-500/20 text-{{ $faq->kategori->warna ?? 'gray' }}-400">
                                {{ $faq->kategori->icon }} {{ $faq->kategori->nama }}
                            </span>
                            @else
                            <span class="px-3 py-1 rounded-full text-xs font-medium bg-gray-500/20 text-gray-400">
                                Tanpa Kategori
                            </span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-white font-medium">{{ $faq->judul }}</div>
                        </td>
                        <td class="px-6 py-4">
                            <div class="text-gray-400 text-sm line-clamp-2">{{ Str::limit($faq->isi, 100) }}</div>
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button @click="editFaq({{ $faq->id }}, '{{ $faq->judul }}', `{{ addslashes($faq->isi) }}`, {{ $faq->kategori_id ?? 'null' }})"
                                    class="text-blue-400 hover:text-blue-300">
                                Edit
                            </button>
                            <form action="{{ route('faq.destroy', $faq) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin hapus FAQ ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-gray-400">
                            Tidak ada FAQ. Klik "Tambah FAQ" untuk membuat yang baru.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $faqs->links() }}
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
        editMode: false,
        editId: null,
        formData: {
            kategori_id: '',
            judul: '',
            isi: ''
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


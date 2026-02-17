@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-bold text-white">Kelola Kategori</h1>
            <p class="text-gray-400 mt-1">Manage answer categories</p>
        </div>
        <button @click="showModal = true; editMode = false; resetForm()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Kategori
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

    <div x-data="kategoriManager()">
        <!-- Kategori Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($kategoris as $kategori)
            <div class="bg-gray-800 rounded-lg p-6 border-l-4 border-{{ $kategori->warna }}-500">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex items-center gap-3">
                        @if($kategori->icon)
                        <span class="text-3xl">{{ $kategori->icon }}</span>
                        @endif
                        <div>
                            <h3 class="text-xl font-bold text-white">{{ $kategori->nama }}</h3>
                            <p class="text-sm text-gray-400">{{ $kategori->faq_count }} FAQ</p>
                        </div>
                    </div>
                    <span class="px-2 py-1 rounded text-xs {{ $kategori->aktif ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                        {{ $kategori->aktif ? 'Aktif' : 'Nonaktif' }}
                    </span>
                </div>

                @if($kategori->deskripsi)
                <p class="text-gray-400 text-sm mb-4">{{ $kategori->deskripsi }}</p>
                @endif

                <div class="flex items-center justify-between pt-4 border-t border-gray-700">
                    <div class="flex items-center gap-2">
                        <span class="w-4 h-4 rounded-full bg-{{ $kategori->warna }}-500"></span>
                        <span class="text-sm text-gray-400">{{ $kategori->warna }}</span>
                        <span class="text-sm text-gray-500">• Urutan: {{ $kategori->urutan }}</span>
                    </div>
                    <div class="flex gap-2">
                        <button @click="editKategori({{ $kategori->id }}, '{{ $kategori->nama }}', '{{ $kategori->warna }}', '{{ $kategori->icon }}', `{{ addslashes($kategori->deskripsi ?? '') }}`, {{ $kategori->aktif ? 'true' : 'false' }}, {{ $kategori->urutan }})"
                                class="text-blue-400 hover:text-blue-300 text-sm">
                            Edit
                        </button>
                        <form action="{{ route('kategori.destroy', $kategori) }}" method="POST" class="inline"
                              onsubmit="return confirm('Yakin hapus kategori ini? FAQ yang menggunakan kategori ini akan menjadi tanpa kategori.')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Hapus</button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-span-full bg-gray-800 rounded-lg p-8 text-center">
                <p class="text-gray-400">Tidak ada kategori. Klik "Tambah Kategori" untuk membuat yang baru.</p>
            </div>
            @endforelse
        </div>

        <!-- Modal Tambah/Edit Kategori -->
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4">
                <div class="fixed inset-0 bg-black opacity-75" @click="showModal = false"></div>

                <div class="relative bg-gray-800 rounded-lg max-w-2xl w-full p-6">
                    <h3 class="text-xl font-bold text-white mb-4" x-text="editMode ? 'Edit Kategori' : 'Tambah Kategori Baru'"></h3>

                    <form :action="editMode ? `/kategori/${editId}` : '{{ route('kategori.store') }}'" method="POST">
                        @csrf
                        <input type="hidden" name="_method" x-bind:value="editMode ? 'PUT' : 'POST'">

                        <div class="space-y-4">
                            <!-- Nama -->
                            <div>
                                <label class="block text-gray-300 mb-2">Nama Kategori <span class="text-red-400">*</span></label>
                                <input type="text" name="nama" x-model="formData.nama" required
                                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Contoh: Pembayaran">
                                @error('nama')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Warna -->
                            <div>
                                <label class="block text-gray-300 mb-2">Warna Badge <span class="text-red-400">*</span></label>
                                <div class="grid grid-cols-5 gap-3">
                                    <label class="cursor-pointer">
                                        <input type="radio" name="warna" value="blue" x-model="formData.warna" class="sr-only">
                                        <div :class="formData.warna === 'blue' ? 'ring-2 ring-blue-400' : ''"
                                             class="w-full h-12 bg-blue-500 rounded-lg flex items-center justify-center text-white font-medium">
                                            Blue
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="warna" value="green" x-model="formData.warna" class="sr-only">
                                        <div :class="formData.warna === 'green' ? 'ring-2 ring-green-400' : ''"
                                             class="w-full h-12 bg-green-500 rounded-lg flex items-center justify-center text-white font-medium">
                                            Green
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="warna" value="red" x-model="formData.warna" class="sr-only">
                                        <div :class="formData.warna === 'red' ? 'ring-2 ring-red-400' : ''"
                                             class="w-full h-12 bg-red-500 rounded-lg flex items-center justify-center text-white font-medium">
                                            Red
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="warna" value="yellow" x-model="formData.warna" class="sr-only">
                                        <div :class="formData.warna === 'yellow' ? 'ring-2 ring-yellow-400' : ''"
                                             class="w-full h-12 bg-yellow-500 rounded-lg flex items-center justify-center text-white font-medium">
                                            Yellow
                                        </div>
                                    </label>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="warna" value="purple" x-model="formData.warna" class="sr-only">
                                        <div :class="formData.warna === 'purple' ? 'ring-2 ring-purple-400' : ''"
                                             class="w-full h-12 bg-purple-500 rounded-lg flex items-center justify-center text-white font-medium">
                                            Purple
                                        </div>
                                    </label>
                                </div>
                                @error('warna')
                                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Icon -->
                            <div>
                                <label class="block text-gray-300 mb-2">Icon Emoji (Opsional)</label>
                                <input type="text" name="icon" x-model="formData.icon"
                                       class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                       placeholder="Contoh: 💰 atau 📦">
                                <p class="text-gray-500 text-xs mt-1">Gunakan emoji untuk icon kategori</p>
                            </div>

                            <!-- Deskripsi -->
                            <div>
                                <label class="block text-gray-300 mb-2">Deskripsi (Opsional)</label>
                                <textarea name="deskripsi" x-model="formData.deskripsi" rows="3"
                                          class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                                          placeholder="Deskripsi singkat tentang kategori ini..."></textarea>
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
                                        <span class="text-white">Kategori Aktif</span>
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
    </div>
</div>

<script>
function kategoriManager() {
    return {
        showModal: false,
        editMode: false,
        editId: null,
        formData: {
            nama: '',
            warna: 'blue',
            icon: '',
            deskripsi: '',
            aktif: true,
            urutan: 0
        },

        resetForm() {
            this.formData = {
                nama: '',
                warna: 'blue',
                icon: '',
                deskripsi: '',
                aktif: true,
                urutan: 0
            };
        },

        editKategori(id, nama, warna, icon, deskripsi, aktif, urutan) {
            this.editMode = true;
            this.editId = id;
            this.formData = {
                nama: nama,
                warna: warna,
                icon: icon || '',
                deskripsi: deskripsi || '',
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


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
            <form method="GET" action="{{ route('peraturan.index') }}" class="flex gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="{{ request('search') }}"
                           placeholder="Cari peraturan..."
                           class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div class="w-48">
                    <select name="tipe" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Tipe</option>
                        <option value="umum" {{ request('tipe') == 'umum' ? 'selected' : '' }}>Umum</option>
                        <option value="wajib" {{ request('tipe') == 'wajib' ? 'selected' : '' }}>Wajib</option>
                        <option value="larangan" {{ request('tipe') == 'larangan' ? 'selected' : '' }}>Larangan</option>
                        <option value="tips" {{ request('tipe') == 'tips' ? 'selected' : '' }}>Tips</option>
                    </select>
                </div>
                <div class="w-48">
                    <select name="prioritas" class="w-full bg-gray-700 text-white rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="">Semua Prioritas</option>
                        <option value="tinggi" {{ request('prioritas') == 'tinggi' ? 'selected' : '' }}>Tinggi</option>
                        <option value="normal" {{ request('prioritas') == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="rendah" {{ request('prioritas') == 'rendah' ? 'selected' : '' }}>Rendah</option>
                    </select>
                </div>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                    Filter
                </button>
                @if(request('search') || request('tipe') || request('prioritas'))
                <a href="{{ route('peraturan.index') }}" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg">
                    Reset
                </a>
                @endif
            </form>
        </div>

        <!-- Peraturan Grouped by Tipe -->
        @php
        $tipeConfig = [
            'wajib' => ['icon' => '✅', 'color' => 'green', 'label' => 'Wajib Dilakukan'],
            'larangan' => ['icon' => '🚫', 'color' => 'red', 'label' => 'Larangan'],
            'tips' => ['icon' => '💡', 'color' => 'yellow', 'label' => 'Tips & Trik'],
            'umum' => ['icon' => '📋', 'color' => 'blue', 'label' => 'Peraturan Umum'],
        ];
        @endphp

        @forelse($peraturansGrouped as $tipe => $items)
        @php $config = $tipeConfig[$tipe] ?? $tipeConfig['umum']; @endphp

        <div class="mb-8">
            <div class="flex items-center gap-3 mb-4">
                <span class="text-3xl">{{ $config['icon'] }}</span>
                <h2 class="text-2xl font-bold text-white">{{ $config['label'] }}</h2>
                <span class="px-3 py-1 bg-{{ $config['color'] }}-500/20 text-{{ $config['color'] }}-400 rounded-full text-sm">
                    {{ $items->count() }} item
                </span>
            </div>

            <div class="space-y-4">
                @foreach($items as $peraturan)
                <div class="bg-gray-800 rounded-lg p-6 border-l-4 border-{{ $config['color'] }}-500">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center gap-3 mb-2">
                                <h3 class="text-lg font-bold text-white">{{ $peraturan->judul }}</h3>

                                <!-- Prioritas Badge -->
                                @if($peraturan->prioritas === 'tinggi')
                                <span class="px-2 py-1 bg-red-500/20 text-red-400 rounded text-xs font-medium">
                                    🔥 Prioritas Tinggi
                                </span>
                                @elseif($peraturan->prioritas === 'rendah')
                                <span class="px-2 py-1 bg-gray-500/20 text-gray-400 rounded text-xs font-medium">
                                    Prioritas Rendah
                                </span>
                                @endif

                                <!-- Status Badge -->
                                <span class="px-2 py-1 rounded text-xs {{ $peraturan->aktif ? 'bg-green-500/20 text-green-400' : 'bg-gray-500/20 text-gray-400' }}">
                                    {{ $peraturan->aktif ? 'Aktif' : 'Nonaktif' }}
                                </span>
                            </div>

                            <p class="text-gray-300 whitespace-pre-line">{{ $peraturan->isi }}</p>
                        </div>

                        @if(auth()->user()->isAdmin())
                        <div class="flex gap-2 ml-4">
                            <button @click="editPeraturan({{ $peraturan->id }}, '{{ $peraturan->judul }}', `{{ addslashes($peraturan->isi) }}`, '{{ $peraturan->tipe }}', '{{ $peraturan->prioritas }}', {{ $peraturan->aktif ? 'true' : 'false' }}, {{ $peraturan->urutan }})"
                                    class="text-blue-400 hover:text-blue-300 text-sm">
                                Edit
                            </button>
                            <form action="{{ route('peraturan.destroy', $peraturan) }}" method="POST" class="inline"
                                  onsubmit="return confirm('Yakin hapus peraturan ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-300 text-sm">Hapus</button>
                            </form>
                        </div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @empty
        <div class="bg-gray-800 rounded-lg p-8 text-center">
            <p class="text-gray-400">Tidak ada peraturan. Klik "Tambah Peraturan" untuk membuat yang baru.</p>
        </div>
        @endforelse

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
        formData: {
            judul: '',
            isi: '',
            tipe: 'umum',
            prioritas: 'normal',
            aktif: true,
            urutan: 0
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


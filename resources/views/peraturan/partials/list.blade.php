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
    <p class="text-gray-400">Tidak ada peraturan yang sesuai dengan filter.</p>
</div>
@endforelse


@props(['title', 'color' => 'blue', 'icon'])

<div class="bg-gray-800 rounded-lg border border-gray-700 overflow-hidden">
    <!-- Header -->
    <div class="bg-{{ $color }}-900/30 border-b border-{{ $color }}-700/50 px-4 py-3 flex items-center justify-between">
        <div class="flex items-center space-x-2">
            @if(isset($icon))
            <div class="text-{{ $color }}-400">
                {!! $icon !!}
            </div>
            @endif
            <h3 class="font-semibold text-{{ $color }}-300">{{ $title }}</h3>
        </div>
        <button 
            onclick="salinTeks(this)" 
            class="bg-{{ $color }}-600 hover:bg-{{ $color }}-700 text-white px-3 py-1 rounded text-sm transition duration-200 flex items-center space-x-1"
        >
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
            </svg>
            <span>Salin</span>
        </button>
    </div>

    <!-- Content -->
    <div class="p-4">
        <textarea 
            rows="6" 
            class="w-full bg-gray-700 border border-gray-600 rounded-lg px-4 py-3 text-white focus:outline-none focus:border-{{ $color }}-500 focus:ring-1 focus:ring-{{ $color }}-500 resize-none"
            placeholder="Jawaban akan muncul di sini..."
        >{{ $slot }}</textarea>
    </div>
</div>


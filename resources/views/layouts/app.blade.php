<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - CS AI Assistant</title>

    <!-- TailwindCSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        dark: {
                            900: '#0f172a',
                            800: '#1e293b',
                            700: '#334155',
                        }
                    }
                }
            }
        }
    </script>

    <!-- AlpineJS -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-gray-900 text-white antialiased">
    <div class="min-h-screen">
        <!-- Navbar -->
        <nav class="bg-gray-800 border-b border-gray-700 sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <!-- Logo & Title -->
                    <div class="flex items-center space-x-8">
                        <div class="flex-shrink-0 flex items-center">
                            <svg class="h-8 w-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path>
                            </svg>
                            <span class="ml-3 text-xl font-bold">CS AI Assistant</span>
                        </div>

                        <!-- Navigation Menu -->
                        <nav class="hidden md:flex space-x-4">
                            <a href="{{ route('dashboard') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('dashboard*') ? 'bg-gray-700 text-white' : '' }}">
                                Dashboard
                            </a>
                            @if(Auth::user()->isAdmin())
                            <a href="{{ route('pengaturan.index') }}" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium {{ request()->routeIs('pengaturan*') ? 'bg-gray-700 text-white' : '' }}">
                                Pengaturan
                            </a>
                            @endif
                        </nav>
                    </div>

                    <!-- User Menu -->
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-3">
                            <div class="text-right">
                                <p class="text-sm font-medium text-white">{{ Auth::user()->name }}</p>
                                <div class="flex items-center gap-1 justify-end">
                                    @foreach(Auth::user()->roles as $role)
                                    <span class="text-xs px-2 py-0.5 rounded-full
                                        {{ $role->nama === 'admin' ? 'bg-red-900/50 text-red-300' : '' }}
                                        {{ $role->nama === 'supervisor' ? 'bg-yellow-900/50 text-yellow-300' : '' }}
                                        {{ $role->nama === 'cs' ? 'bg-blue-900/50 text-blue-300' : '' }}">
                                        {{ $role->label }}
                                    </span>
                                    @endforeach
                                </div>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-blue-600 flex items-center justify-center">
                                <span class="text-white font-semibold">{{ strtoupper(substr(Auth::user()->name, 0, 1)) }}</span>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="bg-red-600 hover:bg-red-700 px-4 py-2 rounded-lg transition duration-200 text-sm font-medium">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Alert Messages -->
        @if(session('sukses'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>{{ session('sukses') }}</span>
                <button onclick="this.parentElement.remove()" class="text-green-300 hover:text-green-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        @if(session('error'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4">
            <div class="bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded-lg flex items-center justify-between">
                <span>{{ session('error') }}</span>
                <button onclick="this.parentElement.remove()" class="text-red-300 hover:text-red-100">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
        @endif

        <!-- Main Content -->
        <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="bg-gray-800 border-t border-gray-700 mt-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                <div class="text-center text-gray-400 text-sm">
                    &copy; {{ date('Y') }} CS AI Assistant. Dibuat dengan ❤️ untuk tim CS.
                </div>
            </div>
        </footer>
    </div>

    @stack('scripts')
</body>
</html>


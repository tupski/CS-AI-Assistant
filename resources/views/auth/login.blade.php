<!DOCTYPE html>
<html lang="id" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CS AI Assistant</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
        }
    </script>
</head>
<body class="bg-gray-900 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-md">
        <!-- Card Login -->
        <div class="bg-gray-800 rounded-lg shadow-xl p-8">
            <!-- Logo/Title -->
            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-white mb-2">CS AI Assistant</h1>
                <p class="text-gray-400">Login untuk melanjutkan</p>
            </div>

            <!-- Alert Sukses -->
            @if(session('sukses'))
            <div class="mb-6 bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded">
                {{ session('sukses') }}
            </div>
            @endif

            <!-- Alert Error -->
            @if($errors->any())
            <div class="mb-6 bg-red-900/50 border border-red-700 text-red-300 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Form Login -->
            <form method="POST" action="{{ route('login.proses') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-gray-300 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        value="{{ old('email') }}"
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="nama@email.com"
                        required
                        autofocus
                    >
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-gray-300 mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-2 bg-gray-700 border border-gray-600 rounded-lg text-white focus:outline-none focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                        placeholder="••••••••"
                        required
                    >
                </div>

                <!-- Remember Me -->
                <div class="mb-6">
                    <label class="flex items-center">
                        <input 
                            type="checkbox" 
                            name="remember" 
                            class="w-4 h-4 bg-gray-700 border-gray-600 rounded text-blue-600 focus:ring-blue-500"
                        >
                        <span class="ml-2 text-gray-300">Ingat saya</span>
                    </label>
                </div>

                <!-- Button Login -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded-lg transition duration-200"
                >
                    Login
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="text-center mt-6 text-gray-500 text-sm">
            &copy; {{ date('Y') }} CS AI Assistant
        </div>
    </div>
</body>
</html>


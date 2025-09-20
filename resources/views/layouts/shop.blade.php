<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'E-Commerce Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @livewireStyles
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
        {{-- This is the shared header for all customer-facing pages --}}
        <header class="flex justify-between items-center mb-8 pb-4 border-b">
            <a href="{{ route('shop.index') }}" class="text-4xl font-bold text-gray-800">E-Commerce Store</a>
            <div class="flex items-center space-x-4">
                @auth
                    <span class="text-sm font-medium text-gray-800">Hi, {{ Auth::user()->name }}</span>
                    <a href="{{ route('order.status') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">My Orders</a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <a href="{{ route('logout') }}"
                           onclick="event.preventDefault(); this.closest('form').submit();"
                           class="text-sm font-medium text-gray-600 hover:text-gray-900">
                            Logout
                        </a>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Login</a>
                    <a href="{{ route('register') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">Register</a>
                @endguest
            </div>
        </header>

        {{-- This is where the specific page content will be injected --}}
        <main>
            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>
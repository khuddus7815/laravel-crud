<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>My Orders</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
        {{-- This is the standalone header for the order status page --}}
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

        <main>
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Your Orders</h1>
                <a href="{{ route('shop.index') }}" class="text-sm font-medium text-gray-600 hover:text-gray-900">&larr; Back to Shop</a>
            </div>

            @if($orders->isEmpty())
                <div class="text-center bg-white p-12 rounded-lg shadow-md">
                    <h2 class="text-2xl font-semibold text-gray-700">You have no orders yet.</h2>
                    <p class="text-gray-500 mt-2">Once you place an order, its status will appear here.</p>
                    <a href="{{ route('shop.index') }}" class="mt-6 inline-block bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700">
                        Go Shopping
                    </a>
                </div>
            @else
                <div class="space-y-8">
                    @foreach ($orders as $order)
                        <div class="bg-white p-6 rounded-lg shadow-md">
                           {{-- Your order details loop will go here, it should work as before --}}
                           <p>Order #{{ $order->id }} - Status: {{ $order->status }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>

</body>
</html>

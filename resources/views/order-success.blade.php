<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order #{{ $order->order_number }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-12">
    <div class="bg-white rounded-lg shadow-xl p-8 max-w-4xl mx-auto">
        
        <div class="text-center mb-8">
            <svg class="w-16 h-16 text-green-500 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <h1 class="text-3xl font-bold text-gray-800">Thank You For Your Order!</h1>
            <p class="text-gray-600 mt-2">Your order has been placed successfully and is now being processed.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-b py-6">
            <div>
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Shipping Details</h2>
                <p class="text-gray-600">{{ $order->customer_name }}</p>
                <p class="text-gray-600">{{ $order->customer_email }}</p>
            </div>
            <div class="text-left md:text-right">
                <h2 class="text-lg font-semibold text-gray-700 mb-2">Order Summary</h2>
                <p class="text-gray-600">Order #: <span class="font-medium text-gray-800">{{ $order->order_number }}</span></p>
                <p class="text-gray-600">Order Date: <span class="font-medium text-gray-800">{{ $order->created_at->format('d M, Y') }}</span></p>
            </div>
        </div>

        <div class="mt-8">
            <h3 class="text-xl font-semibold text-gray-800 mb-4">Order Items</h3>
            <div class="space-y-4">
                @foreach($order->items as $orderItem)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                    <div class="flex items-center">
                        <img src="{{ $orderItem->item->image_path ? Storage::url($orderItem->item->image_path) : 'https://via.placeholder.com/150' }}" alt="{{ $orderItem->item->name }}" class="w-16 h-16 rounded-md object-cover mr-4">
                        <div>
                            <p class="font-semibold text-gray-800">{{ $orderItem->item->name }}</p>
                            <p class="text-sm text-gray-600">Qty: {{ $orderItem->quantity }}</p>
                        </div>
                    </div>
                    <p class="font-semibold text-gray-800">₹{{ number_format($orderItem->price * $orderItem->quantity, 2) }}</p>
                </div>
                @endforeach
            </div>
        </div>

        <div class="mt-8 border-t pt-6">
            <div class="flex justify-end items-center mb-6">
                <div class="text-right">
                    <p class="text-gray-600">Subtotal: <span class="font-medium">₹{{ number_format($order->total, 2) }}</span></p>
                    {{-- You can add discount logic here if needed --}}
                    <p class="text-2xl font-bold text-gray-800 mt-2">Total: <span class="text-indigo-600">₹{{ number_format($order->total, 2) }}</span></p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-center">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <h4 class="font-semibold">Order Status</h4>
                    <p class="text-blue-800 capitalize">{{ $order->status }}</p>
                </div>
                <div class="bg-gray-100 p-4 rounded-lg">
                    <h4 class="font-semibold">Customer Support</h4>
                    <p class="text-gray-700">support@example.com</p>
                </div>
                <div class="p-4">
                    @if($order->status === 'pending')
                        <button class="w-full bg-red-500 text-white py-2 rounded-lg hover:bg-red-600">
                            Cancel Order
                        </button>
                    @else
                        <button class="w-full bg-gray-300 text-gray-500 py-2 rounded-lg cursor-not-allowed" disabled>
                            Cancellation unavailable
                        </button>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-10 text-center">
            <a href="{{ route('shop.index') }}" class="inline-block bg-indigo-600 text-white py-3 px-8 rounded-lg hover:bg-indigo-700 font-semibold">
                &larr; Continue Shopping
            </a>
        </div>

    </div>
</div>

</body>
</html>
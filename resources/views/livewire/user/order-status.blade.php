<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Order Status</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .timeline-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: relative;
            width: 100%;
            margin: 20px 0;
        }
        .timeline-line {
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 4px;
            background-color: #e0e0e0;
            transform: translateY(-50%);
            z-index: 1;
        }
        .timeline-line-progress {
            position: absolute;
            top: 50%;
            left: 0;
            height: 4px;
            background-color: #34d399;
            transform: translateY(-50%);
            z-index: 2;
            transition: width 0.5s ease;
        }
        .timeline-milestone {
            position: relative;
            z-index: 3;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }
        .milestone-circle {
            width: 24px;
            height: 24px;
            border-radius: 50%;
            background-color: #e0e0e0;
            border: 4px solid #fff;
            transition: background-color 0.5s ease;
        }
        .milestone-circle.active {
            background-color: #34d399;
        }
        .milestone-label {
            margin-top: 8px;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
        }
        .milestone-label.active {
            color: #1f2937;
            font-weight: 600;
        }
    </style>
</head>
<body class="bg-gray-100">

<div class="container mx-auto px-4 py-8">
    <div class="flex justify-between items-center mb-8 pb-4 border-b">
        <h1 class="text-4xl font-bold text-gray-800">Your Orders</h1>
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
                    <div class="flex justify-between items-start">
                        <div>
                            <h2 class="text-xl font-bold">Order #{{ $order->id }}</h2>
                            <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('d M Y') }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-lg font-semibold">Total: ₹{{ number_format($order->total, 2) }}</p>
                            <span class="px-3 py-1 text-sm font-semibold rounded-full
                                @switch($order->status)
                                    @case('pending') bg-yellow-200 text-yellow-800 @break
                                    @case('processing') bg-blue-200 text-blue-800 @break
                                    @case('delivered') bg-green-200 text-green-800 @break
                                    @case('cancelled') bg-red-200 text-red-800 @break
                                    @case('returned') bg-gray-200 text-gray-800 @break
                                @endswitch">
                                {{ ucfirst($order->status) }}
                            </span>
                        </div>
                    </div>

                    <div class="mt-6">
                        @php
                            $statuses = ['pending', 'processing', 'delivered'];
                            if ($order->status === 'cancelled') {
                                $statuses = ['pending', 'cancelled'];
                            } elseif ($order->status === 'returned') {
                                $statuses = ['delivered', 'returned'];
                            }
                            $currentStatusIndex = array_search($order->status, $statuses);
                            $progressWidth = $currentStatusIndex > 0 ? ($currentStatusIndex / (count($statuses) - 1)) * 100 : 0;
                        @endphp

                        <div class="timeline-container">
                            <div class="timeline-line"></div>
                            <div class="timeline-line-progress" style="width: {{ $progressWidth }}%;"></div>
                            @foreach ($statuses as $index => $status)
                                <div class="timeline-milestone">
                                    <div class="milestone-circle {{ $currentStatusIndex >= $index ? 'active' : '' }}"></div>
                                    <div class="milestone-label {{ $currentStatusIndex >= $index ? 'active' : '' }}">{{ ucfirst($status) }}</div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6 border-t pt-4">
                        <h3 class="font-semibold text-lg mb-2">Items</h3>
                        <ul class="space-y-2">
                            @foreach($order->items as $item)
                                <li class="flex justify-between">
                                    <span>{{ $item->name }} (x{{ $item->pivot->quantity }})</span>
                                    <span>₹{{ number_format($item->pivot->price * $item->pivot->quantity, 2) }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>

                    <div class="mt-6 flex justify-end space-x-4">
                        @if($order->status == 'pending')
                            <button wire:click="cancelOrder({{ $order->id }})" wire:confirm="Are you sure you want to cancel this order?" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                                Cancel Order
                            </button>
                        @elseif($order->status == 'delivered')
                            <button wire:click="returnOrder({{ $order->id }})" wire:confirm="Are you sure you want to return this order?" class="px-4 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800">
                                Return Order
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

</body>
</html>

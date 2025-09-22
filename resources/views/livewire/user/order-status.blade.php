<div>
    @php
        $statuses = ['pending', 'processing', 'shipped', 'delivered'];
        $currentStatusIndex = array_search($order->status, $statuses);
        $isCancelled = ($order->status === 'cancelled');

        if ($isCancelled) {
            $currentStatusIndex = -1; // Or some other indicator that it's a terminal, non-progressing state
        } elseif ($currentStatusIndex === false) {
            $currentStatusIndex = -1;
        }
    @endphp

    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-between items-center">
            <h2 class="text-2xl font-semibold">Order Tracking</h2>
            @if($order->status === 'pending' || $order->status === 'processing')
                <button wire:click="cancelOrder" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded">
                    Cancel Order
                </button>
            @endif
        </div>

        @if(session()->has('message'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mt-4" role="alert">
                <span class="block sm:inline">{{ session('message') }}</span>
            </div>
        @endif

        @if(session()->has('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mt-4" role="alert">
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        @if ($isCancelled)
            <div class="text-center mt-8">
                <p class="text-red-600 text-2xl font-bold">Order Cancelled</p>
                <div class="relative pt-10">
                    <div class="flex items-center justify-center">
                        <div class="w-10 h-10 mx-auto rounded-full text-lg flex items-center justify-center bg-red-500 text-white">
                            &#10006; {{-- X mark --}}
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="relative pt-10">
                <div class="flex items-center justify-between">
                    <div class="absolute top-1/2 left-0 w-full h-1 bg-gray-300" style="transform: translateY(-50%);"></div>
                    <div class="absolute top-1/2 left-0 h-1 bg-green-500" style="width: {{ ($currentStatusIndex / (count($statuses) - 1)) * 100 }}%; transform: translateY(-50%);"></div>
                    @foreach($statuses as $index => $status)
                        <div class="w-1/4 relative">
                            <div class="w-10 h-10 mx-auto rounded-full text-lg flex items-center justify-center {{ $currentStatusIndex >= $index ? 'bg-green-500 text-white' : 'bg-gray-300 text-gray-600' }}">
                                @if($currentStatusIndex > $index)
                                    &#10003;
                                @else
                                    {{ $index + 1 }}
                                @endif
                            </div>
                            <div class="text-xs text-center md:text-base mt-2">{{ ucfirst($status) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

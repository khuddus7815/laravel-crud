<div>
    {{-- This is the new root div --}}
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-6">Checkout</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Order Summary --}}
            <div>
                <h2 class="text-xl font-semibold mb-4">Your Order</h2>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    @forelse($this->cartItems as $item)
                        <div class="flex justify-between items-center {{ !$loop->last ? 'border-b pb-4 mb-4' : '' }}">
                            <div>
                                <p class="font-semibold">{{ $item->item->name }}</p>
                                <p class="text-sm text-gray-500">Quantity: {{ $item->quantity }}</p>
                            </div>
                            <p class="font-medium">₹{{ number_format($item->item->price * $item->quantity, 2) }}</p>
                        </div>
                    @empty
                        <p>Your cart is empty.</p>
                    @endforelse

                    <div class="mt-6 border-t pt-4">
                        <div class="flex justify-between">
                            <p>Subtotal</p>
                            <p>₹{{ number_format($this->subtotal, 2) }}</p>
                        </div>
                        @if($couponApplied)
                            <div class="flex justify-between text-green-600">
                                <p>Discount ({{ $couponCode }})</p>
                                <p>- ₹{{ number_format($this->discount, 2) }}</p>
                            </div>
                        @endif
                        <div class="flex justify-between font-bold text-lg mt-2">
                            <p>Total</p>
                            <p>₹{{ number_format($this->total, 2) }}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Checkout Form --}}
            <div>
                <h2 class="text-xl font-semibold mb-4">Shipping & Payment</h2>
                <div class="bg-white p-6 rounded-lg shadow-md">
                    <form wire:submit.prevent="placeOrder">
                        <div class="mb-4">
                            <label for="name" class="block text-sm font-medium text-gray-700">Full Name</label>
                            <input type="text" id="name" wire:model.defer="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                            <input type="email" id="email" wire:model.defer="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                            @error('email') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        {{-- Coupon Code --}}
                        <div class="mb-4">
                             <label for="coupon_code" class="block text-sm font-medium text-gray-700">Coupon Code</label>
                            @if($couponApplied)
                                <div class="flex items-center justify-between mt-1">
                                    <p class="text-green-600 bg-green-50 px-3 py-2 rounded-l-md border border-r-0 border-green-200">Applied: {{ $couponCode }}</p>
                                    <button type="button" wire:click="removeCoupon" class="bg-red-500 text-white px-4 py-2 rounded-r-md hover:bg-red-600">&times;</button>
                                </div>
                            @else
                                <div class="flex items-center mt-1">
                                    <input type="text" id="coupon_code" wire:model.defer="couponCode" class="block w-full rounded-l-md border-gray-300 shadow-sm">
                                    <button type="button" wire:click="applyCoupon" class="bg-gray-800 text-white px-4 py-2 rounded-r-md hover:bg-gray-700">Apply</button>
                                </div>
                                 @if($couponMessage) <span class="text-red-500 text-xs">{{ $couponMessage }}</span> @endif
                            @endif
                        </div>

                        {{-- Payment Method --}}
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Payment Method</label>
                            <div class="mt-2 space-y-2">
                                @foreach($paymentMethods as $key => $value)
                                    <label class="flex items-center p-3 rounded-lg border @if($paymentMethod === $key) border-indigo-500 bg-indigo-50 @else border-gray-300 @endif">
                                        <input type="radio" wire:model="paymentMethod" value="{{ $key }}" class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500">
                                        <span class="ml-3 block text-sm font-medium text-gray-700">{{ $value }}</span>
                                        @if($key !== 'cod')
                                            <span class="ml-auto text-xs text-gray-500">(Currently unavailable)</span>
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                            @error('paymentMethod') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>

                        <button type="submit" 
                                class="w-full bg-indigo-600 text-white py-3 rounded-lg hover:bg-indigo-700 font-semibold text-lg @if($paymentMethod !== 'cod') bg-gray-400 hover:bg-gray-400 cursor-not-allowed @endif"
                                @if($paymentMethod !== 'cod') disabled @endif>
                            Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> {{-- This is the closing new root div --}}
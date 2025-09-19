<div class="container mx-auto p-4">
    <h1 class="text-3xl font-bold mb-6 text-center">Checkout</h1>

    @if (session()->has('error'))
        <div class="bg-red-500 text-white p-3 rounded mb-4 text-center">
            {{ session('error') }}
        </div>
    @endif
    @if (session()->has('success'))
        <div class="bg-green-500 text-white p-3 rounded mb-4 text-center">
            {{ session('success') }}
        </div>
    @endif


    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div>
            <h2 class="text-xl font-semibold mb-4">Your Information</h2>
            <form wire:submit.prevent="placeOrder" class="space-y-4">
                <div>
                    <label for="name" class="block font-medium">Full Name</label>
                    <input type="text" id="name" wire:model.defer="name" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="email" class="block font-medium">Email Address</label>
                    <input type="email" id="email" wire:model.defer="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <h3 class="text-lg font-medium mb-2">Payment Method</h3>
                    <div class="space-y-2">
                        <label class="flex items-center p-3 w-full bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <input type="radio" wire:model="paymentMethod" value="cod" class="form-radio h-5 w-5 text-indigo-600">
                            <span class="ml-3">Cash on Delivery (COD)</span>
                        </label>
                        <label class="flex items-center p-3 w-full bg-gray-100 dark:bg-gray-700 rounded-lg">
                            <input type="radio" wire:model="paymentMethod" value="online" class="form-radio h-5 w-5 text-indigo-600">
                            <span class="ml-3">Online Payment (Card/UPI)</span>
                        </label>
                    </div>
                     @error('paymentMethod') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <button type="submit" class="w-full bg-indigo-600 text-white py-3 px-4 rounded-md hover:bg-indigo-700 font-semibold text-lg">
                    Place Order
                </button>
            </form>
        </div>

        <div class="bg-gray-50 dark:bg-gray-700 p-6 rounded-lg">
            <h2 class="text-xl font-semibold mb-4">Order Summary</h2>
            <div class="space-y-4">
                @forelse($cart as $item)
                    <div class="flex justify-between items-center">
                        <div>
                            <p class="font-semibold">{{ $item['name'] }}</p>
                            <p class="text-sm text-gray-500">Quantity: {{ $item['quantity'] }}</p>
                        </div>
                        <p class="font-medium">₹{{ number_format($item['price'] * $item['quantity'], 2) }}</p>
                    </div>
                @empty
                    <p>Your cart is empty.</p>
                @endforelse
            </div>

            <div class="border-t border-gray-200 mt-6 pt-6">
                <div class="mb-4">
                    <label for="couponCode" class="block font-medium mb-1">Have a coupon?</label>
                    <div class="flex space-x-2">
                        <input type="text" id="couponCode" wire:model.live="couponCode" placeholder="Enter coupon code" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" {{ $couponApplied ? 'disabled' : '' }}>
                        @if($couponApplied)
                            <button wire:click="removeCoupon" class="px-4 py-2 bg-red-500 text-white rounded-md hover:bg-red-600">Remove</button>
                        @else
                            <button wire:click="applyCoupon" class="px-4 py-2 bg-gray-800 text-white rounded-md hover:bg-gray-700">Apply</button>
                        @endif
                    </div>
                    @if($couponMessage)
                        <p class="mt-2 text-sm {{ $couponApplied ? 'text-green-600' : 'text-red-600' }}">{{ $couponMessage }}</p>
                    @endif
                </div>


                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span>Subtotal</span>
                        <span>₹{{ number_format($this->subtotal, 2) }}</span>
                    </div>
                    @if ($discount > 0)
                    <div class="flex justify-between text-green-600">
                        <span>Discount</span>
                        <span>- ₹{{ number_format($this->discount, 2) }}</span>
                    </div>
                    @endif
                    <div class="flex justify-between font-bold text-lg border-t pt-2 mt-2">
                        <span>Total</span>
                        <span>₹{{ number_format($this->total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
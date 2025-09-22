<div>
    {{-- Cart Button --}}
    <div class="relative">
        <button wire:click="toggleCart" class="relative focus:outline-none">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600 hover:text-indigo-600 transition" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            @if($this->cartCount > 0)
                <span class="absolute -top-2 -right-2 bg-indigo-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center">{{ $this->cartCount }}</span>
            @endif
        </button>
    </div>

    {{-- Shopping Cart Sidebar --}}
    @if($cartOpen)
        <div class="fixed inset-0 bg-black bg-opacity-50 z-40" wire:click="toggleCart"></div>

        <div class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-xl z-50 flex flex-col">

            <div class="flex justify-between items-center p-4 border-b">
                <h2 class="text-xl font-semibold">Your Cart</h2>
                <button wire:click="toggleCart" class="text-2xl">&times;</button>
            </div>

            <div class="flex-grow flex flex-col">
                @if($this->cartItems->isEmpty())
                    {{-- THIS IS THE NEW EMPTY CART STATE --}}
                    <div class="flex-grow flex flex-col items-center justify-center p-4 text-center">
                        <svg class="w-24 h-24 text-gray-300 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 0 0-3 3h15.75m-12.75-3h11.218c.51 0 .962-.344 1.087-.835l1.838-5.514A1.875 1.875 0 0 0 18.224 6H6.136a1.875 1.875 0 0 0-1.82 2.404l1.838 5.514Z" />
                        </svg>
                        <h3 class="text-xl font-semibold text-gray-700">Your cart is empty</h3>
                        <p class="text-gray-500 mt-2">Looks like you haven't added anything to your cart yet.</p>
                        <button wire:click="toggleCart" class="mt-6 bg-indigo-600 text-white py-2 px-6 rounded-lg hover:bg-indigo-700 focus:outline-none">
                            Continue Shopping
                        </button>
                    </div>
                @else
                    <div class="overflow-y-auto p-4">
                        @foreach($this->cartItems as $cartItem)
                            @if($cartItem->item)
                            <div class="flex items-center space-x-4 mb-4" wire:key="{{ $cartItem->id }}">
                                <img src="{{ $cartItem->item->image_path ? Storage::url($cartItem->item->image_path) : 'https://via.placeholder.com/150' }}" class="w-16 h-16 rounded object-cover">
                                <div class="flex-grow">
                                    <h3 class="font-semibold">{{ $cartItem->item->name }}</h3>
                                    <p class="text-sm text-gray-600">₹{{ number_format($cartItem->item->price, 2) }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <button wire:click="updateQuantity({{ $cartItem->id }}, {{ $cartItem->quantity - 1 }})" class="px-2 py-1 bg-gray-200 rounded text-lg">-</button>
                                    <span>{{ $cartItem->quantity }}</span>
                                    <button wire:click="updateQuantity({{ $cartItem->id }}, {{ $cartItem->quantity + 1 }})" class="px-2 py-1 bg-gray-200 rounded text-lg">+</button>
                                </div>
                                <button wire:click="removeFromCart({{ $cartItem->id }})" class="text-red-500 hover:text-red-700 text-2xl">&times;</button>
                            </div>
                            @endif
                        @endforeach
                    </div>
                @endif
            </div>

            @if(!$this->cartItems->isEmpty())
                <div class="p-4 border-t">
                    <div class="flex justify-between items-center font-semibold text-lg">
                        <span>Total</span>
                        <span>₹{{ number_format($this->cartTotal, 2) }}</span>
                    </div>
                    <a href="{{ route('checkout') }}"
                       class="block text-center w-full mt-4 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700" wire:navigate>
                        Checkout
                    </a>
                </div>
            @endif
        </div>
    @endif
</div>


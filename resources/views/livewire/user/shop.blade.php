<div x-data="shop()" x-init="init()" x-cloak>
    {{-- Cart Button - Moved here from header to work with AlpineJS scope --}}
    <div class="fixed top-8 right-8 z-50">
         <button @click="cartOpen = !cartOpen" class="relative">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            <span class="absolute -top-2 -right-2 bg-indigo-600 text-white text-xs font-bold rounded-full w-5 h-5 flex items-center justify-center" x-text="cartCount">0</span>
        </button>
    </div>

    {{-- Shopping Cart Sidebar --}}
    <div x-show="cartOpen" @click.away="cartOpen = false" class="fixed inset-0 bg-black bg-opacity-50 z-40"></div>
    <div x-show="cartOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="transform translate-x-full"
         x-transition:enter-end="transform translate-x-0"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="transform translate-x-0"
         x-transition:leave-end="transform translate-x-full"
         class="fixed top-0 right-0 h-full w-full max-w-sm bg-white shadow-xl z-50 flex flex-col">
        <div class="flex justify-between items-center p-4 border-b">
            <h2 class="text-xl font-semibold">Your Cart</h2>
            <button @click="cartOpen = false" class="text-2xl">&times;</button>
        </div>
        <div class="flex-grow overflow-y-auto p-4">
            <template x-if="Object.values(cart).length === 0"><p class="text-gray-500">Your cart is empty.</p></template>
            <template x-for="item in Object.values(cart)" :key="item.id">
                <div class="flex items-center space-x-4 mb-4">
                    <img :src="item.image_url" class="w-16 h-16 rounded object-cover">
                    <div class="flex-grow">
                        <h3 class="font-semibold" x-text="item.name"></h3>
                        <p class="text-sm text-gray-600" x-text="`₹${item.price}`"></p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <button @click="updateQuantity(item.id, item.quantity - 1)" class="px-2 py-1 bg-gray-200 rounded text-lg">-</button>
                        <span x-text="item.quantity"></span>
                        <button @click="updateQuantity(item.id, item.quantity + 1)" class="px-2 py-1 bg-gray-200 rounded text-lg">+</button>
                    </div>
                    <button @click="removeFromCart(item.id)" class="text-red-500 hover:text-red-700 text-2xl">&times;</button>
                </div>
            </template>
        </div>
        <div class="p-4 border-t">
            <div class="flex justify-between items-center font-semibold text-lg">
                <span>Total</span>
                <span x-text="`₹${cartTotal}`"></span>
            </div>
            <button
                @click="@auth window.location.href='{{ route('checkout') }}' @else window.location.href='{{ route('login') }}' @endauth"
                :disabled="Object.values(cart).length === 0"
                class="w-full mt-4 bg-indigo-600 text-white py-2 rounded-lg hover:bg-indigo-700 disabled:bg-gray-400">
                Checkout
            </button>
        </div>
    </div>

    {{-- Product Grid --}}
    <main>
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
            @foreach ($items as $item)
                <div class="bg-white rounded-lg shadow-md overflow-hidden transform hover:-translate-y-1 transition-transform duration-300 flex flex-col">
                    @php
                        $imageUrl = $item->image_path ? Storage::url($item->image_path) : 'https://via.placeholder.com/300';
                    @endphp
                    <img src="{{ $imageUrl }}" alt="{{ $item->name }}" class="w-full h-48 object-cover">
                    <div class="p-4 flex flex-col flex-grow">
                        <div class="flex-grow">
                            <h2 class="text-lg font-semibold text-gray-800">{{ $item->name }}</h2>
                            <p class="text-gray-600 mt-1 font-bold">₹{{ number_format($item->price, 2) }}</p>
                            <p class="text-sm text-gray-500 mt-2">{{ Str::limit($item->description, 50) }}</p>
                        </div>
                        <div class="mt-4">
                            @guest
                                <button @click="window.location.href='{{ route('login') }}'" class="w-full bg-indigo-500 text-white py-2 rounded-lg hover:bg-indigo-600 transition-all duration-200">
                                    Add to Cart
                                </button>
                            @else
                                <template x-if="!cart[{{ $item->id }}]">
                                    <button
                                        @click="addToCart({ id: {{ $item->id }}, name: '{{ addslashes($item->name) }}', price: {{ $item->price }}, image_url: '{{ $imageUrl }}' })"
                                        class="w-full bg-indigo-500 text-white py-2 rounded-lg hover:bg-indigo-600 transition-all duration-200">
                                        Add to Cart
                                    </button>
                                </template>
                                <template x-if="cart[{{ $item->id }}]">
                                    <div class="flex items-center justify-between w-full border border-gray-300 rounded-lg">
                                        <button @click="updateQuantity({{ $item->id }}, cart[{{ $item->id }}].quantity - 1)"
                                                class="px-4 py-1 text-xl font-bold text-gray-700 hover:bg-gray-100 rounded-l-lg focus:outline-none">-</button>
                                        <span x-text="cart[{{ $item->id }}].quantity" class="font-bold text-lg text-gray-800"></span>
                                        <button @click="updateQuantity({{ $item->id }}, cart[{{ $item->id }}].quantity + 1)"
                                                class="px-4 py-1 text-xl font-bold text-gray-700 hover:bg-gray-100 rounded-r-lg focus:outline-none">+</button>
                                    </div>
                                </template>
                            @endguest
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
    
    <script>
        function shop() {
            return {
                items: @json($items),
                cart: {},
                cartOpen: false,

                init() {
                    this.loadCartFromLocalStorage();
                    this.$watch('cart', () => this.saveCartToLocalStorage());
                },

                addToCart(itemToAdd) {
                    let cartItem = this.cart[itemToAdd.id];
                    if (cartItem) {
                        cartItem.quantity++;
                    } else {
                        this.cart[itemToAdd.id] = {
                            id: itemToAdd.id,
                            name: itemToAdd.name,
                            price: itemToAdd.price,
                            image_url: itemToAdd.image_url,
                            quantity: 1,
                        };
                    }
                },

                removeFromCart(itemId) {
                    delete this.cart[itemId];
                },

                updateQuantity(itemId, newQuantity) {
                    if (newQuantity <= 0) {
                        this.removeFromCart(itemId);
                    } else {
                        if (this.cart[itemId]) {
                           this.cart[itemId].quantity = newQuantity;
                        }
                    }
                },

                get cartCount() {
                    return Object.values(this.cart).reduce((total, item) => total + item.quantity, 0);
                },

                get cartTotal() {
                    return Object.values(this.cart).reduce((total, item) => total + (item.price * item.quantity), 0).toFixed(2);
                },

                saveCartToLocalStorage() {
                    localStorage.setItem('cart', JSON.stringify(this.cart));
                },

                loadCartFromLocalStorage() {
                    const savedCart = localStorage.getItem('cart');
                    if (savedCart) {
                        this.cart = JSON.parse(savedCart);
                    }
                },
            }
        }
    </script>
</div>

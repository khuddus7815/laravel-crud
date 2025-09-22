

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'E-Commerce Store' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    {{-- Using defer is crucial to prevent conflicts with Livewire --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    @livewireStyles
    <style>
        /* Add a subtle transition for the placeholder text color */
        input::placeholder {
            transition: opacity 0.5s ease-in-out;
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 flex flex-col min-h-screen">
    <div class="flex-grow">
        <header class="bg-white shadow-md sticky top-0 z-40">
            <div class="container mx-auto px-4">
                <div class="flex items-center justify-between py-3">
                    
                    {{-- Left: Logo --}}
                    <div class="flex-shrink-0">
                        <a href="{{ route('shop.index') }}" class="text-2xl font-bold text-indigo-600">
                            E-Commerce Store
                        </a>
                    </div>

                    {{-- Center: Dynamic Search Bar --}}
                    <div 
                        x-data="{
                            placeholders: [
                                'Search for Mobiles & Tablets...',
                                'Search for Laptops & Computers...',
                                'Search for Fashion & Apparel...',
                                'Search for Groceries...',
                                'Search for Home Appliances...'
                            ],
                            currentPlaceholder: 'Search for products, brands and more',
                            init() {
                                let i = 0;
                                this.currentPlaceholder = this.placeholders[i];
                                setInterval(() => {
                                    i = (i + 1) % this.placeholders.length;
                                    const input = this.$refs.searchInput;
                                    if (input) {
                                        input.style.opacity = 0;
                                        setTimeout(() => {
                                            this.currentPlaceholder = this.placeholders[i];
                                            input.style.opacity = 1;
                                        }, 500);
                                    }
                                }, 3000);
                            }
                        }"
                        class="flex-grow max-w-xl mx-auto hidden md:flex"
                    >
                        <div class="relative w-full">
                            <input 
                                type="search"
                                x-ref="searchInput"
                                :placeholder="currentPlaceholder"
                                class="w-full px-4 py-2 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"
                            >
                            <button class="absolute top-0 right-0 h-full px-4 text-gray-500 hover:text-indigo-600">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Right: Auth Links & Cart --}}
                    <div class="flex-shrink-0 flex items-center space-x-6">
                        @auth
                           <div x-data="{ open: false }" class="relative">
                               <button @click="open = !open" class="flex items-center space-x-2 focus:outline-none">
                                   <span class="text-sm font-medium">Hi, {{ Auth::user()->name }}</span>
                                   <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                               </button>
                               <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50" style="display: none;">
                                   <a href="{{ route('order.status') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My Orders</a>
                                   <form method="POST" action="{{ route('logout') }}">
                                       @csrf
                                       <a href="{{ route('logout') }}"
                                          onclick="event.preventDefault(); this.closest('form').submit();"
                                          class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                           Logout
                                       </a>
                                   </form>
                               </div>
                           </div>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-medium hover:text-indigo-600">Login</a>
                            <a href="{{ route('register') }}" class="text-sm font-medium bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Register</a>
                        @endguest
                        
                        <livewire:cart-counter />
                    </div>
                </div>
            </div>
        </header>
        <body class="bg-gray-100">

    <div class="container mx-auto px-4 py-8">
       
        <main>
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Your Orders</h1>
                <a href="{{ route('shop.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">&larr; Back to Shop</a>
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
                            <div class="flex justify-between items-start mb-4">
                                <div>
                                    <h2 class="text-xl font-semibold text-gray-800">Order #{{ $order->order_number }}</h2>
                                    <p class="text-sm text-gray-500">Placed on {{ $order->created_at->format('M d, Y') }}</p>
                                </div>
                                <div class="text-right">
                                     <p class="text-lg font-bold text-gray-900 mt-1">Total: ₹{{ number_format($order->total, 2) }}</p>
                                </div>
                            </div>

                            <livewire:user.order-status :order="$order" :key="$order->id" />
                            
                            <div class="border-t border-gray-200 pt-4 mt-4">
                                <h3 class="font-semibold mb-2">Items:</h3>
                                @foreach($order->orderItems as $item)
                                    <div class="flex items-center justify-between py-2 {{ !$loop->last ? 'border-b border-gray-100' : '' }}">
                                        <div class="flex items-center space-x-4">
                                            <img src="{{ Storage::url($item->item->image_path) }}" alt="{{ $item->item->name }}" class="w-12 h-12 rounded-md object-cover">
                                            <div>
                                                <p class="font-medium">{{ $item->item->name }}</p>
                                                <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                            </div>
                                        </div>
                                        <p class="text-gray-700">₹{{ number_format($item->price * $item->quantity, 2) }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </main>
    </div>

 {{-- START: New Flipkart-style Footer --}}
    <footer class="bg-gray-800 text-gray-400 text-sm">
        <div class="container mx-auto px-4 py-10">
            <div class="grid grid-cols-2 md:grid-cols-6 gap-8">
                {{-- About Column --}}
                <div class="space-y-4">
                    <h3 class="text-gray-500 font-semibold uppercase">About</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white">Contact Us</a></li>
                        <li><a href="#" class="hover:text-white">About Us</a></li>
                        <li><a href="#" class="hover:text-white">Careers</a></li>
                        <li><a href="#" class="hover:text-white">Store Stories</a></li>
                        <li><a href="#" class="hover:text-white">Corporate Information</a></li>
                    </ul>
                </div>

                {{-- Help Column --}}
                <div class="space-y-4">
                    <h3 class="text-gray-500 font-semibold uppercase">Help</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white">Payments</a></li>
                        <li><a href="#" class="hover:text-white">Shipping</a></li>
                        <li><a href="#" class="hover:text-white">Cancellation & Returns</a></li>
                        <li><a href="#" class="hover:text-white">FAQ</a></li>
                        <li><a href="#" class="hover:text-white">Report Infringement</a></li>
                    </ul>
                </div>

                {{-- Policy Column --}}
                <div class="space-y-4">
                    <h3 class="text-gray-500 font-semibold uppercase">Consumer Policy</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white">Return Policy</a></li>
                        <li><a href="#" class="hover:text-white">Terms Of Use</a></li>
                        <li><a href="#" class="hover:text-white">Security</a></li>
                        <li><a href="#" class="hover:text-white">Privacy</a></li>
                        <li><a href="#" class="hover:text-white">Sitemap</a></li>
                    </ul>
                </div>

                {{-- Social Column --}}
                <div class="space-y-4">
                    <h3 class="text-gray-500 font-semibold uppercase">Social</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="hover:text-white">Facebook</a></li>
                        <li><a href="#" class="hover:text-white">Twitter</a></li>
                        <li><a href="#" class="hover:text-white">YouTube</a></li>
                    </ul>
                </div>

                {{-- Mail Us Column --}}
                <div class="col-span-2 md:col-span-1 md:border-l border-gray-700 md:pl-8 space-y-4">
                    <h3 class="text-gray-500 font-semibold uppercase">Mail Us</h3>
                    <p>E-Commerce Store Internet Private Limited, Buildings Alyssa, Begonia & Clove Embassy Tech Village, Outer Ring Road, Devarabeesanahalli Village, Bengaluru, 560103, Karnataka, India</p>
                </div>

                {{-- Registered Office --}}
                <div class="col-span-2 md:col-span-1 space-y-4">
                    <h3 class="text-gray-500 font-semibold uppercase">Registered Office Address</h3>
                    <p>E-Commerce Store Internet Private Limited, Buildings Alyssa, Begonia & Clove Embassy Tech Village, Outer Ring Road, Devarabeesanahalli Village, Bengaluru, 560103, Karnataka, India CIN : U51109KA2012PTC066107 Telephone: 044-45614700</p>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-700">
            <div class="container mx-auto px-4 py-6 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-white"><span>&#128736;</span> Become a Seller</a>
                    <a href="#" class="hover:text-white"><span>&#11088;</span> Advertise</a>
                    <a href="#" class="hover:text-white"><span>&#127873;</span> Gift Cards</a>
                    <a href="#" class="hover:text-white"><span>&#10067;</span> Help Center</a>
                </div>
                <p>&copy; {{ date('Y') }} E-Commerce Store. Built by Khuddus Shaik.</p>
                <div class="flex items-center space-x-2">
                    <p>We Accept:</p>
                    <img src="https://static-assets-web.flixcart.com/fk-p-linchpin-web/fk-cp-zion/img/payment-method_69e7ec.svg" alt="Payment Methods" class="h-6">
                </div>
            </div>
        </div>
    </footer>
    {{-- END: New Footer --}}

    @livewireScripts
</body>
</html>


<div>
    <main>
        {{-- START: Auto-scrolling Banner Slider --}}
        <div 
            x-data="{
                slides: [
                    'https://rukminim2.flixcart.com/fk-p-flap/3240/540/image/8c2598b5c820a42b.jpg?q=60',
                    'https://rukminim2.flixcart.com/fk-p-flap/3240/540/image/6cbfac6f1b2702a4.jpg?q=60',
                    'https://rukminim2.flixcart.com/fk-p-flap/3240/540/image/274ab939b76cd3b2.jpeg?q=60',
                    'https://rukminim1.flixcart.com/fk-p-flap/3240/540/image/24797830feb41221.jpeg?q=60'
                ],
                activeSlide: 1,
                interval: null,
                init() {
                    this.startAutoplay();
                },
                startAutoplay() {
                    this.interval = setInterval(() => {
                        this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1;
                    }, 3000); // Change slide every 3 seconds
                },
                stopAutoplay() {
                    clearInterval(this.interval);
                },
                next() {
                    this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1;
                    this.stopAutoplay();
                    this.startAutoplay();
                },
                prev() {
                    this.activeSlide = this.activeSlide === 1 ? this.slides.length : this.activeSlide - 1;
                    this.stopAutoplay();
                    this.startAutoplay();
                }
            }"
            class="relative w-full mx-auto mb-8 rounded-lg overflow-hidden shadow-lg"
        >
            <!-- Slides -->
            <div class="relative w-full h-56 md:h-96" @mouseenter="stopAutoplay()" @mouseleave="startAutoplay()">
                <template x-for="(slide, index) in slides" :key="index">
                    <div 
                        x-show="activeSlide === index + 1"
                        x-transition:enter="transition ease-in-out duration-1000"
                        x-transition:enter-start="opacity-0"
                        x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in-out duration-1000"
                        x-transition:leave-start="opacity-100"
                        x-transition:leave-end="opacity-0"
                        class="absolute inset-0"
                    >
                        <img :src="slide" class="w-full h-full object-cover" />
                    </div>
                </template>
            </div>

            <!-- Prev/Next Buttons -->
            <button @click="prev()" class="absolute top-1/2 left-4 transform -translate-y-1/2 bg-white/60 hover:bg-white rounded-full p-2 focus:outline-none transition">
                <svg class="w-6 h-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" /></svg>
            </button>
            <button @click="next()" class="absolute top-1/2 right-4 transform -translate-y-1/2 bg-white/60 hover:bg-white rounded-full p-2 focus:outline-none transition">
                <svg class="w-6 h-6 text-gray-800" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
            </button>

            <!-- Navigation Dots -->
            <div class="absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                <template x-for="(slide, index) in slides" :key="index">
                    <button 
                        @click="activeSlide = index + 1; stopAutoplay(); startAutoplay();"
                        class="w-3 h-3 rounded-full"
                        :class="{'bg-white': activeSlide === index + 1, 'bg-white/50': activeSlide !== index + 1}"
                    ></button>
                </template>
            </div>
        </div>
        {{-- END: Auto-scrolling Banner Slider --}}

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
                                <a href="{{ route('login') }}" class="w-full block text-center bg-indigo-500 text-white py-2 rounded-lg hover:bg-indigo-600 transition-all duration-200">
                                    Add to Cart
                                </a>
                            @else
                                @if(isset($cartItems[$item->id]))
                                    <div class="flex items-center justify-between w-full border border-gray-300 rounded-lg">
                                        <button wire:click="decrement({{ $item->id }})" class="px-4 py-1 text-xl font-bold text-gray-700 hover:bg-gray-100 rounded-l-lg focus:outline-none">-</button>
                                        <span class="font-bold text-lg text-gray-800">{{ $cartItems[$item->id]['quantity'] }}</span>
                                        <button wire:click="increment({{ $item->id }})" class="px-4 py-1 text-xl font-bold text-gray-700 hover:bg-gray-100 rounded-r-lg focus:outline-none">+</button>
                                    </div>
                                @else
                                    <button wire:click="addToCart({{ $item->id }})" class="w-full bg-indigo-500 text-white py-2 rounded-lg hover:bg-indigo-600 transition-all duration-200">
                                        Add to Cart
                                    </button>
                                @endif
                            @endguest
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </main>
</div>

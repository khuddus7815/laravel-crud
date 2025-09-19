<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        {{-- ... (head content is unchanged) ... --}}
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display-swap" rel="stylesheet" />
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body 
        x-data="{ sidebarOpen: (localStorage.getItem('sidebarOpen') === 'true' || localStorage.getItem('sidebarOpen') === null) }" 
        x-init="$watch('sidebarOpen', value => localStorage.setItem('sidebarOpen', value))"
        class="font-sans antialiased" 
        x-cloak
    >
        <div class="min-h-screen bg-gray-100">
            @include('layouts.navigation')

            <div class="flex h-[calc(100vh-65px)]">

                <div 
                    :class="sidebarOpen ? 'w-64' : 'w-16'"
                    class="relative bg-white shadow-lg transition-all duration-300 ease-in-out">
                <button 
                        @click="sidebarOpen = !sidebarOpen" 
                        class="absolute right-2 top-8 z-10 w-7 h-7 rounded-full bg-gray-800 text-white flex items-center justify-center focus:outline-none hover:bg-gray-700 transition-transform hover:scale-110"
                    >
                        {{-- hamburger menu --}}
                        <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                           <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>

                    <div class="w-64 h-full overflow-hidden">
                        {{-- The x-show directive is now on the parent div to hide the border --}}
                        <div x-show="sidebarOpen" x-transition class="p-4 flex items-center border-b h-16">
                            <h2 class="text-lg font-semibold text-gray-800">Menu</h2>
                        </div>
                        
                        <div 
                            x-show="sidebarOpen"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                        >
                            @auth('web')
                                @livewire('categories')
                            @endauth
                        </div>
                    </div>
                </div>

                <div class="flex-1 flex flex-col overflow-hidden">
                    @if (isset($header))
                        <header class="bg-white shadow-sm flex-shrink-0">
                            <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                                <h1 class="text-xl font-semibold text-gray-800">
                                    {{ $header }}
                                </h1>
                            </div>
                        </header>
                    @endif
                    
                    <main class="flex-1 overflow-y-auto">
                        {{ $slot }}
                    </main>
                </div>
            </div>
        </div>
        @livewireScripts
    </body>
</html>
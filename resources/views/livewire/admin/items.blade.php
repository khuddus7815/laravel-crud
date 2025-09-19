<div x-data="{ lightboxOpen: false, lightboxImage: '', hideTimeout: null }" class="py-12 px-4 sm:px-6 lg:px-8">
    {{-- START: Lightbox Overlay --}}
    <div x-show="lightboxOpen"
         @mouseenter="clearTimeout(hideTimeout)"
         @mouseleave="hideTimeout = setTimeout(() => { lightboxOpen = false }, 300)"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 md:p-8"
         style="display: none;">

        <div @click="lightboxOpen = false" class="absolute inset-0 bg-black bg-opacity-75 cursor-pointer"></div>

        <div class="relative max-w-4xl max-h-full mx-auto">
            <img :src="lightboxImage" 
                 alt="Image Preview" 
                 class="object-contain max-w-full max-h-[90vh] rounded-md shadow-lg" 
                 @click.stop>
            <button @click="lightboxOpen = false" 
                    class="absolute -top-3 -right-3 sm:-top-4 sm:-right-4 bg-white rounded-full p-2 text-gray-700 hover:text-black focus:outline-none focus:ring-2 focus:ring-white">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </div>
    {{-- END: Lightbox Overlay --}}

    @if (!$selectedCategoryId)
        <div class="text-center bg-white shadow-sm sm:rounded-lg p-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No category selected</h3>
            <p class="mt-1 text-sm text-gray-500">Please select a category from the sidebar to view or add items.</p>
        </div>
    @else
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6 bg-white border-b border-gray-200">

                @if (session()->has('message'))
                    @php
                        $messageType = session('message_type') ?? 'success';
                        $bgColor = $messageType === 'error' ? 'bg-red-100 border-red-400 text-red-700' : 'bg-green-100 border-green-400 text-green-700';
                    @endphp
                    <div class="{{ $bgColor }} px-4 py-3 rounded relative mb-4" role="alert">
                        <span class="block sm:inline">{{ session('message') }}</span>
                    </div>
                @endif

                
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-4">
                    <div x-data="{ open: false }" @click.away="open = false" class="relative">
                        <button @click="open = !open" class="flex items-center space-x-2 text-left">
                            <h2 class="text-2xl font-semibold">{{ $selectedCategoryName ?? 'Select a Category' }}</h2>
                            <svg class="w-5 h-5 text-gray-500 transform transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                            </svg>
                        </button>

                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute z-10 mt-2 w-64 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5"
                             style="display: none;">

                            <div class="py-1" role="menu" aria-orientation="vertical">
                                @forelse ($this->categories as $category)
                                    <a href="#" wire:click.prevent="filterByCategory({{ $category->id }})" @click="open = false" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        {{ $category->name }}
                                    </a>
                                @empty
                                    <span class="block px-4 py-2 text-sm text-gray-500">No categories found.</span>
                                @endforelse

                                <div class="border-t border-gray-100 my-1"></div>

                                <a href="#"
                                   wire:click.prevent="create"
                                   @click="open = false"
                                   class="block px-4 py-2 text-sm text-indigo-700 hover:bg-gray-100 font-medium"
                                   role="menuitem">
                                   + Add New Item
                                </a>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                        <div class="w-full sm:w-64">
                            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search items..." class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                        </div>
                        <x-primary-button wire:click="create">+ Add New Item</x-primary-button>
                    </div>
                </div>

                <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                    <div wire:loading.flex class="absolute inset-0 z-10 bg-white bg-opacity-75 items-center justify-center">
                        <svg class="animate-spin h-8 w-8 text-gray-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </div>

                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button wire:click="sortBy('name')" class="flex items-center space-x-1">
                                        <span>Name</span>
                                        @if($sortField === 'name')
                                            @if($sortDirection === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                {{-- ADD THIS SNIPPET --}}
<th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
    <button wire:click="sortBy('price')" class="flex items-center space-x-1">
        <span>Price</span>
        @if($sortField === 'price')
            @if($sortDirection === 'asc')
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
            @else
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
            @endif
        @endif
    </button>
</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Image</th>
                                <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    <button wire:click="sortBy('created_at')" class="flex items-center space-x-1">
                                        <span>Created At</span>
                                        @if($sortField === 'created_at')
                                            @if($sortDirection === 'asc')
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd" /></svg>
                                            @else
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" /></svg>
                                            @endif
                                        @endif
                                    </button>
                                </th>
                                <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($items as $item)
                            <tr wire:key="item-{{ $item->id }}" class="odd:bg-white even:bg-gray-50 hover:bg-gray-100">
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ \Illuminate\Support\Str::limit($item->description, 40) }}
                                </td>
                                {{-- ADD THIS SNIPPET --}}
<td class="px-6 py-4 whitespace-nowrap font-medium">
    ₹{{ number_format($item->price, 2) }}
</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($item->image_path)
                                        <div
                                            @mouseenter="clearTimeout(hideTimeout); lightboxImage = '{{ Storage::url($item->image_path) }}'; lightboxOpen = true"
                                            @mouseleave="hideTimeout = setTimeout(() => { lightboxOpen = false }, 300)"
                                            class="cursor-pointer">
                                            <img src="{{ Storage::url($item->image_path) }}" alt="{{ $item->name }}" class="h-10 w-10 object-cover rounded-full">
                                        </div>
                                    @else
                                        <span class="text-gray-400">No Image</span>
                                    @endif
                                </td>
                                
                                <td class="px-6 py-4 whitespace-nowrap">{{ $item->created_at->format('d-m-Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <div class="inline-flex items-center space-x-2">
                                        <x-secondary-button type="button" wire:click="view({{ $item->id }})">View</x-secondary-button>
                                        <x-secondary-button type="button" wire:click="edit({{ $item->id }})">Edit</x-secondary-button>
                                        <x-danger-button type="button" wire:click="delete({{ $item->id }})">Delete</x-danger-button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-4 whitespace-nowrap text-center text-gray-500">
                                    No items found in this category.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $items->links() }}
                </div>

                @if($showModal)
                    <div class="fixed z-10 inset-0 overflow-y-auto">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <form wire:submit.prevent="save">
                                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $itemId ? 'Edit Item' : 'Create Item' }}</h3>
                                        <div class="mt-4">
                                            <div>
                                                <label for="name" class="block text-sm font-medium text-gray-700">Name</label>
                                                <input type="text" wire:model.defer="name" id="name" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                                @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            {{-- ADD THIS SNIPPET --}}
<div class="mt-4">
    <label for="price" class="block text-sm font-medium text-gray-700">Price (₹)</label>
    <input type="number" step="0.01" min="0" wire:model.defer="price" id="price" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
    @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
</div>
                                            <div class="mt-4">
                                                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                                                <textarea wire:model.defer="description" id="description" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"></textarea>
                                                @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                            </div>
                                            <div class="mt-4">
                                                <label for="image" class="block text-sm font-medium text-gray-700">Image</label>
                                                <input type="file" wire:model="image" id="image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-600 hover:file:bg-indigo-100">
                                                @if ($image)
                                                    <img src="{{ $image->temporaryUrl() }}" class="mt-2 h-20 w-20 object-cover">
                                                @elseif ($existingImage)
                                                    <img src="{{ Storage::url($existingImage) }}" class="mt-2 h-20 w-20 object-cover">
                                                @endif
                                                @error('image') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                                <div wire:loading wire:target="image">Uploading...</div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                        <x-primary-button type="submit" wire:loading.attr="disabled">Save</x-primary-button>
                                        <x-secondary-button type="button" class="mr-2" wire:click="closeModal">Cancel</x-secondary-button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endif

                @if($showDeleteModal)
                    <div class="fixed z-10 inset-0 overflow-y-auto">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Item</h3>
                                    <div class="mt-2"><p class="text-sm text-gray-500">Are you sure you want to delete this item? This action cannot be undone.</p></div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <x-danger-button type="button" wire:click="confirmDelete">Delete</x-danger-button>
                                    <x-secondary-button type="button" class="mr-2" wire:click="$set('showDeleteModal', false)">Cancel</x-secondary-button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                @if($showViewModal && $viewingItem)
                    <div class="fixed z-10 inset-0 overflow-y-auto">
                        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                            <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900">{{ $viewingItem->name }}</h3>
                                    <div class="mt-4">
                                        @if($viewingItem->image_path)
                                            <img src="{{ Storage::url($viewingItem->image_path) }}" alt="{{ $viewingItem->name }}" class="w-full h-64 object-cover rounded-md mb-4">
                                        @endif
                                        {{-- ADD THIS SNIPPET --}}
<div class="mb-4">
    <p class="text-sm text-gray-700"><strong>Price:</strong></p>
    <p class="text-lg text-gray-900 font-semibold">₹{{ number_format($viewingItem->price, 2) }}</p>
</div>
                                        <p class="text-sm text-gray-700"><strong>Description:</strong></p>
                                        <p class="text-sm text-gray-500 whitespace-pre-wrap">{{ $viewingItem->description ?: 'No description provided.' }}</p>
                                        <p class="text-xs text-gray-400 mt-4">Created: {{ $viewingItem->created_at->format('M d, Y') }}</p>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <x-secondary-button type="button" wire:click="closeViewModal">Done</x-secondary-button>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
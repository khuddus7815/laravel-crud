<div class="p-4">
    @if (session()->has('category_message'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-2 rounded relative mb-3 text-sm" role="alert">
            <span>{{ session('category_message') }}</span>
        </div>
    @endif

    <div class="flex justify-between items-center mb-2">
        <h3 class="text-xs font-bold text-gray-500 uppercase">Categories</h3>
        <button wire:click="openAddCategoryModal" class="text-indigo-600 hover:text-indigo-900 font-bold text-lg" title="Add New Category">+</button>
    </div>

    <nav class="space-y-1">
        @forelse($categories as $category)
            <div class="flex items-center justify-between rounded-md group {{ $selectedCategoryId == $category->id ? 'bg-indigo-100 text-indigo-700' : 'text-gray-600 hover:bg-gray-200 hover:text-gray-900' }}">
                <a href="#"
                   wire:click.prevent="selectCategory({{ $category->id }})"
                   @click="sidebarOpen = false"  {{-- This closes the sidebar on click --}}
                   class="flex-grow px-3 py-2 text-sm font-medium">
                    {{ $category->name }}
                </a>
                <button wire:click.prevent="confirmCategoryDeletion({{ $category->id }})" class="mr-2 opacity-0 group-hover:opacity-100 transition-opacity" title="Delete Category">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-red-400 hover:text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        @empty
            <p class="text-sm text-gray-500 px-3">No categories yet.</p>
        @endforelse
    </nav>

    @if($showAddCategoryModal)
        <div class="fixed z-50 inset-0 overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
                <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                    <form wire:submit.prevent="saveCategory">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Add New Category</h3>
                            <div class="mt-4">
                                <label for="newCategoryName" class="block text-sm font-medium text-gray-700">Category Name</label>
                                <input type="text" wire:model.defer="newCategoryName" id="newCategoryName" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                @error('newCategoryName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <x-primary-button type="submit">Save</x-primary-button>
                            <x-secondary-button type="button" class="mr-2" wire:click="$set('showAddCategoryModal', false)">Cancel</x-secondary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @if($showDeleteConfirmModal)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Category</h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            Are you sure you want to delete this category? Items in this category will not be deleted but will become uncategorized. This action cannot be undone.
                        </p>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <x-danger-button type="button" wire:click="deleteCategory">
                        Yes, Delete
                    </x-danger-button>
                    <x-secondary-button type="button" class="mr-2" wire:click="$set('showDeleteConfirmModal', false)">
                        Cancel
                    </x-secondary-button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
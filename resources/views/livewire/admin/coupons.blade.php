<div class="py-12 px-4 sm:px-6 lg:px-8">
    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
        <div class="p-6 bg-white border-b border-gray-200">
            @if (session()->has('message'))
                <div class="bg-green-100 border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                    <span class="block sm:inline">{{ session('message') }}</span>
                </div>
            @endif

            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold">Manage Coupons</h2>
                <x-primary-button wire:click="create">+ Add New Coupon</x-primary-button>
            </div>

            <div class="relative overflow-x-auto shadow-md sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Code</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Value</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expires</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse ($coupons as $coupon)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap font-mono text-sm">{{ $coupon->code }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ ucfirst($coupon->type) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    {{ $coupon->type == 'percent' ? $coupon->value . '%' : '₹' . number_format($coupon->value, 2) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">{{ $coupon->expires_at ? $coupon->expires_at->format('d-m-Y') : 'Never' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <x-secondary-button wire:click="edit({{ $coupon->id }})">Edit</x-secondary-button>
                                    <x-danger-button wire:click="delete({{ $coupon->id }})" wire:confirm="Are you sure you want to delete this coupon?">Delete</x-danger-button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No coupons found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="mt-4">{{ $coupons->links() }}</div>
        </div>
    </div>
    
    @if($showModal)
    <div class="fixed z-50 inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <div class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg sm:w-full">
                <form wire:submit.prevent="save">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ $couponId ? 'Edit Coupon' : 'Create Coupon' }}</h3>
                        <div class="mt-4 grid grid-cols-1 gap-4">
                            <div>
                                <label for="code" class="block text-sm font-medium text-gray-700">Code</label>
                                <input type="text" wire:model.defer="code" id="code" class="mt-1 block w-full border-gray-300 rounded-md">
                                @error('code') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
                                <select wire:model.defer="type" id="type" class="mt-1 block w-full border-gray-300 rounded-md">
                                    <option value="percent">Percent</option>
                                    <option value="fixed">Fixed Amount</option>
                                </select>
                            </div>
                            <div>
                                <label for="value" class="block text-sm font-medium text-gray-700">Value</label>
                                <input type="number" step="0.01" wire:model.defer="value" id="value" class="mt-1 block w-full border-gray-300 rounded-md">
                                @error('value') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label for="expires_at" class="block text-sm font-medium text-gray-700">Expiry Date (Optional)</label>
                                <input type="date" wire:model.defer="expires_at" id="expires_at" class="mt-1 block w-full border-gray-300 rounded-md">
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <x-primary-button type="submit">Save</x-primary-button>
                        <x-secondary-button type="button" class="mr-2" wire:click="closeModal">Cancel</x-secondary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif
</div>
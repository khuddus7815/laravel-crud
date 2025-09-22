<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Order Management') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">

                    @if (session()->has('message'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('message') }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between items-center mb-4">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Show</span>
                            <select wire:model.live="perPage" class="border-gray-300 rounded-md shadow-sm text-sm">
                                <option>10</option>
                                <option>25</option>
                                <option>50</option>
                                <option>100</option>
                            </select>
                             <span class="text-sm text-gray-600">entries</span>
                        </div>
                        <div>
                            <input wire:model.live.debounce.300ms="search" type="text" class="border-gray-300 rounded-md shadow-sm text-sm" placeholder="Search orders...">
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <button wire:click="sortBy('order_number')" class="flex items-center">
                                            Order #
                                            @if($sortField === 'order_number')
                                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <button wire:click="sortBy('customer_name')" class="flex items-center">
                                            Customer
                                            @if($sortField === 'customer_name')
                                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <button wire:click="sortBy('total')" class="flex items-center">
                                            Total
                                             @if($sortField === 'total')
                                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <button wire:click="sortBy('status')" class="flex items-center">
                                            Status
                                            @if($sortField === 'status')
                                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        <button wire:click="sortBy('created_at')" class="flex items-center">
                                            Date
                                            @if($sortField === 'created_at')
                                                <span class="ml-1">{{ $sortDirection === 'asc' ? '▲' : '▼' }}</span>
                                            @endif
                                        </button>
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($orders as $order)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->customer_name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹{{ number_format($order->total, 2) }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                                @if($order->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                                @if($order->status == 'processing') bg-blue-100 text-blue-800 @endif
                                                @if($order->status == 'shipped') bg-indigo-100 text-indigo-800 @endif
                                                @if($order->status == 'delivered') bg-green-100 text-green-800 @endif
                                                @if($order->status == 'cancelled') bg-red-100 text-red-800 @endif
                                            ">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d M, Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex items-center space-x-2" x-data="{ newStatus: '{{ $order->status }}' }">
                                                <select x-model="newStatus" class="block w-full border-gray-300 rounded-md shadow-sm text-sm">
                                                    <option value="pending">Pending</option>
                                                    <option value="processing">Processing</option>
                                                    <option value="shipped">Shipped</option>
                                                    <option value="delivered">Delivered</option>
                                                    <option value="cancelled">Cancelled</option>
                                                </select>
                                                <button @click="$wire.updateStatus({{ $order->id }}, newStatus)" class="px-3 py-1 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 text-xs">Update</button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                            No orders found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
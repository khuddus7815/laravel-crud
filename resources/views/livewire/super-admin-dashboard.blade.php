<div>
    <div class="container mx-auto px-4 py-8">
        <h1 class="text-3xl font-bold mb-8">Super Admin Dashboard</h1>

        {{-- Action Buttons --}}
        <div class="flex space-x-4 mb-8">
            <button wire:click="showCreateForm(1)" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                Create Admin
            </button>
            <button wire:click="showCreateForm(0)" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                Create User
            </button>
        </div>

        {{-- User Creator Form --}}
        @if($showUserCreator)
            <div class="mb-8">
                <livewire:super-admin-user-creator @user-created="$refresh" :initialIsAdmin="$initialIsAdmin" />
                <button wire:click="hideCreateForm" class="mt-4 bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                    Close Form
                </button>
            </div>
        @endif

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">
            {{-- Users and Admins Column --}}
            <div class="xl:col-span-1 space-y-8">
                {{-- Users Table --}}
                <div>
                    <h2 class="text-2xl font-semibold mb-4">Users</h2>
                    <input wire:model.live.debounce.300ms="searchUsers" type="text" placeholder="Search Users..." class="w-full rounded-md border-gray-300 shadow-sm mb-4">
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse($users as $user)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $user->email }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-sm text-gray-500">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="p-4">
                            {{ $users->links() }}
                        </div>
                    </div>
                </div>
                {{-- Admins Table --}}
                <div>
                    <h2 class="text-2xl font-semibold mb-4">Admins</h2>
                    <input wire:model.live.debounce.300ms="searchAdmins" type="text" placeholder="Search Admins..." class="w-full rounded-md border-gray-300 shadow-sm mb-4">
                    <div class="bg-white shadow-md rounded-lg overflow-hidden">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                               @forelse($admins as $admin)
                                    <tr>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $admin->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">{{ $admin->email }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="2" class="text-center py-4 text-sm text-gray-500">No admins found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                        <div class="p-4">
                            {{ $admins->links() }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Orders Table --}}
            <div class="xl:col-span-2">
                <h2 class="text-2xl font-semibold mb-4">All Orders</h2>
                <input wire:model.live.debounce.300ms="searchOrders" type="text" placeholder="Search orders..." class="w-full rounded-md border-gray-300 shadow-sm mb-4">
                <div class="bg-white shadow-md rounded-lg overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Order #</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Admin</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Payment</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                @php
                                    $orderAdmins = $order->orderItems->map(fn($item) => optional($item->item->user)->name)->filter()->unique();
                                @endphp
                                <tr>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $order->order_number }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <div>{{ $order->customer_name }}</div>
                                        <div class="text-xs text-gray-400">{{ $order->customer_email }}</div>
                                    </td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $orderAdmins->implode(', ') ?: 'N/A' }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-800 font-semibold">₹{{ number_format($order->total, 2) }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->payment_method }}</td>
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
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
                                    <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('d M, Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-sm text-gray-500">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                    <div class="p-4">
                        {{ $orders->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

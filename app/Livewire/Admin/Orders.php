<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    // The $orderStatuses property and the mount() method were not being used,
    // so they can be safely removed for a cleaner component.

    public function updateStatus($orderId, $newStatus)
    {
        // Add a check to ensure a valid status is being passed.
        if (!in_array($newStatus, ['pending', 'processing', 'shipped', 'delivered', 'cancelled'])) {
            return;
        }

        $order = Order::findOrFail($orderId);

        $order->status = $newStatus;
        $order->save();

        session()->flash('message', 'Order status updated successfully.');
    }

    public function render()
    {
        $orders = Order::latest()->paginate(10);
        return view('livewire.admin.orders', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}

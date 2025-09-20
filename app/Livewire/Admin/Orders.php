<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class Orders extends Component
{
    use WithPagination;

    public function updateStatus($orderId, $newStatus)
    {
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
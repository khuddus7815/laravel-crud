<?php

namespace App\Livewire\User; // <-- UPDATED NAMESPACE

use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;

#[Layout('layouts.guest')] // You can create a specific customer layout or use the guest one
class OrderStatus extends Component
{
    public $orders;

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        $this->orders = Auth::guard('customer')->user()->orders()->with('items.item')->latest()->get();
    }

    public function cancelOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->customer_id == Auth::guard('customer')->id() && $order->status == 'pending') {
            $order->status = 'cancelled';
            $order->save();
            $this->loadOrders();
        }
    }

    public function returnOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->customer_id == Auth::guard('customer')->id() && $order->status == 'delivered') {
            $order->status = 'returned';
            $order->save();
            $this->loadOrders();
        }
    }

    public function render()
    {
        return view('livewire.user.order-status');
    }
}

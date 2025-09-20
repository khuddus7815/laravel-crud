<?php

namespace App\Livewire\User;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class OrderStatus extends Component
{
    public $orders;

    public function mount()
    {
        $this->loadOrders();
    }

    public function loadOrders()
    {
        if (Auth::check()) {
            $this->orders = Auth::user()->orders()->with('items.item')->latest()->get();
        } else {
            $this->orders = collect(); // Return an empty collection if the user is not logged in
        }
    }

    public function cancelOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->user_id == Auth::id() && $order->status == 'pending') {
            $order->status = 'cancelled';
            $order->save();
            $this->loadOrders();
        }
    }

    public function returnOrder($orderId)
    {
        $order = Order::find($orderId);
        if ($order && $order->user_id == Auth::id() && $order->status == 'delivered') {
            $order->status = 'returned';
            $order->save();
            $this->loadOrders();
        }
    }

    /**
     * Render the component.
     * This renders the view as a complete, standalone HTML page without any layout.
     */
    public function render()
    {
        return view('livewire.user.order-status');
    }
}
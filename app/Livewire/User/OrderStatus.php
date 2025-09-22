<?php

namespace App\Livewire\User;

use App\Models\Order;
use Livewire\Component;

class OrderStatus extends Component
{
    public Order $order;

    public function cancelOrder()
    {
        if ($this->order->status === 'pending' || $this->order->status === 'processing') {
            $this->order->status = 'cancelled';
            $this->order->save();
            session()->flash('message', 'Order successfully cancelled.');
        } else {
            session()->flash('error', 'Order cannot be cancelled at this stage.');
        }
    }

    public function render()
    {
        return view('livewire.user.order-status');
    }
}

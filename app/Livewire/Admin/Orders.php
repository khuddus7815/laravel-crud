<?php

namespace App\Livewire\Admin;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Orders extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }
        $this->sortField = $field;
    }

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
        // Start with a base query
        $query = Order::query();

        // ** THIS IS THE KEY FIX **
        // If the user is NOT a super admin, filter orders to their products.
        // The super admin (is_admin == 2) will bypass this filter and see all orders.
        if (Auth::user()->is_admin != 2) {
            $query->whereHas('orderItems.item', function ($subQuery) {
                $subQuery->where('user_id', Auth::id());
            });
        }

        // Add the search functionality
        if ($this->search) {
            $query->where(function ($subQuery) {
                $subQuery->where('order_number', 'like', '%' . $this->search . '%')
                         ->orWhere('customer_name', 'like', '%' . $this->search . '%')
                         ->orWhere('status', 'like', '%' . $this->search . '%');
            });
        }

        // Apply sorting and pagination
        $orders = $query->orderBy($this->sortField, $this->sortDirection)
                        ->paginate($this->perPage);

        return view('livewire.admin.orders', [
            'orders' => $orders
        ])->layout('layouts.app');
    }
}
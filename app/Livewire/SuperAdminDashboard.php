<?php

namespace App\Livewire;

use App\Models\Order;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class SuperAdminDashboard extends Component
{
    use WithPagination;

    public $searchUsers = '';
    public $searchAdmins = '';
    public $searchOrders = '';
    public $showUserCreator = false;
    public $initialIsAdmin = 0;
    public $sortField = 'created_at';
    public $sortDirection = 'desc';

    #[On('userCreated')]
    public function refreshUsersAndAdmins()
    {
        $this->resetPage('usersPage');
        $this->resetPage('adminsPage');
        $this->showUserCreator = false;
    }

    public function showCreateForm($isAdmin)
    {
        $this->showUserCreator = true;
        $this->initialIsAdmin = $isAdmin;
    }

    public function hideCreateForm()
    {
        $this->showUserCreator = false;
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

    public function updating($property)
    {
        if (in_array($property, ['searchUsers', 'searchAdmins', 'searchOrders'])) {
            $this->resetPage();
        }
    }

    public function render()
    {
        // Fetch Users
        $users = User::where('is_admin', 0)
            ->where(fn($q) => $q->where('name', 'like', "%{$this->searchUsers}%")->orWhere('email', 'like', "%{$this->searchUsers}%"))
            ->paginate(10, ['*'], 'usersPage');

        // Fetch Admins
        $admins = User::where('is_admin', 1)
            ->where(fn($q) => $q->where('name', 'like', "%{$this->searchAdmins}%")->orWhere('email', 'like', "%{$this->searchAdmins}%"))
            ->paginate(10, ['*'], 'adminsPage');

        // *** THIS IS THE CORRECTED QUERY FOR ORDERS ***
        $ordersQuery = Order::with(['orderItems.item.user']);

        // Apply search if a search term is provided
        if (!empty($this->searchOrders)) {
            $ordersQuery->where(function ($query) {
                $query->where('order_number', 'like', '%' . $this->searchOrders . '%')
                      ->orWhere('customer_name', 'like', '%' . $this->searchOrders . '%')
                      ->orWhere('status', 'like', '%' . $this->searchOrders . '%')
                      ->orWhereHas('orderItems.item.user', function ($subQuery) {
                          $subQuery->where('name', 'like', '%' . $this->searchOrders . '%');
                      });
            });
        }

        $orders = $ordersQuery->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10, ['*'], 'ordersPage');

        return view('livewire.super-admin-dashboard', [
            'users' => $users,
            'admins' => $admins,
            'orders' => $orders,
        ])->layout('layouts.super-admin');
    }
}
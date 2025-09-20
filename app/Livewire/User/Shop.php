<?php

namespace App\Livewire\User;

use App\Models\Item;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.shop')]
class Shop extends Component
{
    public $items = [];
    public $cart = [];
    public $cartOpen = false;

    public function mount()
    {
        $this->items = Item::all();
        $this->cart = session('cart', []);
    }

    public function toggleCart()
    {
        $this->cartOpen = !$this->cartOpen;
    }

    #[Computed]
    public function cartCount()
    {
        return collect($this->cart)->sum('quantity');
    }

    #[Computed]
    public function cartTotal()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    public function addToCart($itemId)
    {
        $item = Item::find($itemId);
        if (!$item) {
            return;
        }

        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity']++;
        } else {
            $this->cart[$itemId] = [
                'id' => $item->id,
                'name' => $item->name,
                'price' => $item->price,
                'image_url' => $item->image_path ? Storage::url($item->image_path) : 'https://via.placeholder.com/300',
                'quantity' => 1,
            ];
        }

        session(['cart' => $this->cart]);
    }

    public function removeFromCart($itemId)
    {
        unset($this->cart[$itemId]);
        session(['cart' => $this->cart]);
    }

    public function updateQuantity($itemId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($itemId);
            return;
        }

        if (isset($this->cart[$itemId])) {
            $this->cart[$itemId]['quantity'] = $quantity;
            session(['cart' => $this->cart]);
        }
    }
    
    public function checkout()
    {
        return redirect()->route('checkout');
    }

    public function render()
    {
        return view('livewire.user.shop');
    }
}
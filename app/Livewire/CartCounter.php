<?php

namespace App\Livewire;

use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class CartCounter extends Component
{
    public $cartOpen = false;

    /**
     * Listen for the 'cart-updated' event and force a re-render.
     * Unsetting the computed property is a robust way to make Livewire
     * re-calculate it from scratch.
     */
    #[On('cart-updated')]
    public function refresh()
    {
        unset($this->cartItems);
    }

    #[Computed]
    public function cartItems()
    {
        if (Auth::check()) {
            return Auth::user()->cartItems()->with('item')->get();
        }
        return collect();
    }

    #[Computed]
    public function cartCount()
    {
        return $this->cartItems->sum('quantity');
    }

    #[Computed]
    public function cartTotal()
    {
        return $this->cartItems->sum(function ($cartItem) {
            if ($cartItem->item && $cartItem->item->price) {
                return $cartItem->item->price * $cartItem->quantity;
            }
            return 0;
        });
    }

    public function removeFromCart($cartItemId)
    {
        Cart::find($cartItemId)?->delete();
        $this->dispatch('cart-updated');
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        if ($quantity < 1) {
            $this->removeFromCart($cartItemId);
            return;
        }

        $cartItem = Cart::find($cartItemId);
        if ($cartItem) {
            $cartItem->update(['quantity' => $quantity]);
            $this->dispatch('cart-updated');
        }
    }

    public function toggleCart()
    {
        $this->cartOpen = !$this->cartOpen;
    }

    public function render()
    {
        return view('livewire.cart-counter');
    }
}
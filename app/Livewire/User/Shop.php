<?php

namespace App\Livewire\User;

use App\Models\Item;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('layouts.shop')]
class Shop extends Component
{
    public $items = [];
    public $cartItems = [];

    public function mount()
    {
        $this->items = Item::all();
        $this->loadCart();
    }

    /**
     * Listen for the 'cart-updated' event to keep this component's
     * cart state in sync with the rest of the application.
     */
    #[On('cart-updated')]
    public function loadCart()
    {
        if (Auth::check()) {
            $this->cartItems = Auth::user()->cartItems
                ->keyBy('item_id') // Use item_id as the key for easy lookup
                ->toArray();
        }
    }

    /**
     * This method now only adds the item if it's not already in the cart.
     */
    public function addToCart($itemId)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!isset($this->cartItems[$itemId])) {
            Cart::create([
                'user_id' => Auth::id(),
                'item_id' => $itemId,
                'quantity' => 1,
            ]);

            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    /**
     * Increments the quantity of an item in the cart.
     */
    public function increment($itemId)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('item_id', $itemId)->first();
        if ($cartItem) {
            $cartItem->increment('quantity');
            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    /**
     * Decrements the quantity or removes the item if quantity is 1.
     */
    public function decrement($itemId)
    {
        $cartItem = Cart::where('user_id', Auth::id())->where('item_id', $itemId)->first();
        if ($cartItem) {
            if ($cartItem->quantity > 1) {
                $cartItem->decrement('quantity');
            } else {
                $cartItem->delete();
            }
            $this->loadCart();
            $this->dispatch('cart-updated');
        }
    }

    public function render()
    {
        return view('livewire.user.shop');
    }
}
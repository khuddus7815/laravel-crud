<?php

namespace App\Livewire\User;

use App\Models\Item;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Computed;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.shop')] // This tells Livewire to use your main shop layout
class Shop extends Component
{
    public $items = [];
    public $cart = [];
    public $cartOpen = false;

    /**
     * This runs when the component is first loaded.
     * It fetches all items from the database and loads the cart from the session.
     */
    public function mount()
    {
        $this->items = Item::all();
        $this->cart = session('cart', []);
    }

    /**
     * Toggles the visibility of the shopping cart sidebar.
     */
    public function toggleCart()
    {
        $this->cartOpen = !$this->cartOpen;
    }

    /**
     * A computed property to get the total number of items in the cart.
     * This recalculates automatically whenever the cart changes.
     */
    #[Computed]
    public function cartCount()
    {
        return collect($this->cart)->sum('quantity');
    }

    /**
     * A computed property to calculate the total price of all items in the cart.
     */
    #[Computed]
    public function cartTotal()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    /**
     * Adds an item to the cart. If the item is already there, it increases the quantity.
     */
    public function addToCart($itemId)
    {
        $item = Item::find($itemId);
        if (!$item) {
            return; // Don't add if the item doesn't exist
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

        session(['cart' => $this->cart]); // Save the updated cart to the session
    }

    /**
     * Removes an item completely from the cart.
     */
    public function removeFromCart($itemId)
    {
        unset($this->cart[$itemId]);
        session(['cart' => $this->cart]);
    }

    /**
     * Updates the quantity of an item in the cart.
     * If the quantity drops to 0 or less, it removes the item.
     */
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
    
    /**
     * Redirects the user to the checkout page.
     * The cart is already in the session, so no data needs to be passed.
     */
    public function checkout()
    {
        return redirect()->route('checkout');
    }

    /**
     * Renders the component's view file.
     * Livewire automatically looks for a Blade file in a matching directory structure.
     */
    public function render()
    {
        return view('livewire.user.shop');
    }
}


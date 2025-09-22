<?php

namespace App\Livewire\User;

use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Illuminate\Support\Str;

#[Layout('layouts.shop')]
class Checkout extends Component
{
    public $name = '';
    public $email = '';
    public $paymentMethod = 'cod';
    public $couponCode = '';
    public $discount = 0;
    public $couponMessage = '';
    public $couponApplied = false;
    public $paymentMethods = [];

    public function mount()
    {
        // Set payment methods once on component initialization
        $this->paymentMethods = [
            'cod' => 'Cash on Delivery',
            'card' => 'Credit/Debit Card',
            'paypal' => 'PayPal',
        ];
    }

    public function getCartItemsProperty()
    {
        return Auth::user()->cartItems()->with('item')->get();
    }

    public function getSubtotalProperty()
    {
        return $this->cartItems->sum(function ($cartItem) {
            return $cartItem->item->price * $cartItem->quantity;
        });
    }

    public function getTotalProperty()
    {
        $total = $this->subtotal - $this->discount;
        return $total > 0 ? $total : 0;
    }

    public function applyCoupon()
    {
        $coupon = Coupon::where('code', $this->couponCode)->first();

        if ($coupon) {
            $this->discount = $coupon->discount($this->subtotal);
            $this->couponApplied = true;
            $this->couponMessage = 'Coupon applied successfully!';
        } else {
            $this->discount = 0;
            $this->couponApplied = false;
            $this->couponMessage = 'Invalid coupon code.';
        }
    }

    public function removeCoupon()
    {
        $this->couponCode = '';
        $this->discount = 0;
        $this->couponApplied = false;
        $this->couponMessage = '';
    }

    public function placeOrder()
    {
        $this->validate([
            'name' => 'required',
            'email' => 'required|email',
            'paymentMethod' => 'required|in:cod',
        ]);

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(10)),
            'user_id' => Auth::id(),
            'customer_name' => $this->name,
            'customer_email' => $this->email,
            'total' => $this->total,
            'payment_method' => $this->paymentMethod,
            'status' => 'pending',
        ]);

        foreach ($this->cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $cartItem->item->id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->item->price,
            ]);
        }
        
        Cart::where('user_id', Auth::id())->delete();
        
        // Dispatch one last update to clear the cart icon
        $this->dispatch('cart-updated');

        return redirect()->route('order.status');
    }

    public function render()
    {
        // This is the key: Set user details every time the component renders.
        if (Auth::check()) {
            $this->name = Auth::user()->name;
            $this->email = Auth::user()->email;
        }

        return view('livewire.user.checkout');
    }
}
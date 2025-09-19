<?php

namespace App\Livewire\User;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;

#[Layout('layouts.shop')]
class Checkout extends Component
{
    public $cart = [];
    public $name = '';
    public $email = '';
    public $paymentMethod = 'cod'; // Default payment method

    public $couponCode = '';
    public $discount = 0;
    public $couponMessage = '';
    public $couponApplied = false;

    // Computed property for subtotal
    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });
    }

    // Computed property for total
    public function getTotalProperty()
    {
        $total = $this->subtotal - $this->discount;
        return $total > 0 ? $total : 0;
    }

    public function mount()
    {
        // Load cart from session and pre-fill user details if logged in
        $this->cart = session('cart', []);
        if (Auth::guard('customer')->check()) {
            $this->name = Auth::guard('customer')->user()->name;
            $this->email = Auth::guard('customer')->user()->email;
        }
    }

    public function applyCoupon()
    {
        $coupon = Coupon::where('code', $this->couponCode)
                        ->where('expires_at', '>', now())
                        ->first();

        if ($coupon) {
            if ($coupon->type == 'fixed') {
                $this->discount = $coupon->value;
            } elseif ($coupon->type == 'percent') {
                $this->discount = ($this->subtotal * $coupon->value) / 100;
            }
            $this->couponApplied = true;
            $this->couponMessage = 'Coupon applied successfully!';
            session()->flash('success', 'Coupon applied!');
        } else {
            $this->discount = 0;
            $this->couponApplied = false;
            $this->couponMessage = 'Invalid or expired coupon code.';
            session()->flash('error', 'Invalid Coupon!');
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'paymentMethod' => 'required|in:cod,online',
        ]);

        if (empty($this->cart)) {
            session()->flash('error', 'Your cart is empty!');
            return;
        }

        // Create the order
        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(uniqid()),
            'customer_id' => Auth::guard('customer')->id(),
            'customer_name' => $this->name,
            'customer_email' => $this->email,
            'total' => $this->total,
            'payment_method' => $this->paymentMethod,
            'status' => 'pending',
        ]);

        // Create order items
        foreach ($this->cart as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'item_id' => $item['id'],
                'quantity' => $item['quantity'],
                'price' => $item['price'],
            ]);
        }

        // Clear the cart
        session()->forget('cart');

        // Redirect to success page
        return redirect()->route('order.success', ['order' => $order->order_number]);
    }

    public function render()
    {
        return view('livewire.user.checkout');
    }
}
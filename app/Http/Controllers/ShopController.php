<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Item;
use Illuminate\Support\Facades\Storage;

class ShopController extends Controller
{
    public function index()
    {
        $items = Item::where('price', '>', 0)->latest()->get();
        return view('shop', ['items' => $items]);
    }
    public function checkout()
    {
        return view('checkout');
    }

    /**
     * Display the order success page for any user.
     * The Order model is automatically fetched by its order_number.
     */
    public function orderSuccess(Order $order)
    {
        // Eager load the order items and the associated item details
        $order->load('items.item');

        return view('order-success', ['order' => $order]);
    }
}

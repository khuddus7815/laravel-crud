<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display the shop index page with all available items.
     * This is no longer used by the main route but is kept for reference.
     */
    public function index()
    {
        $items = Item::where('price', '>', 0)->latest()->get();

        return view('shop', ['items' => $items]);
    }


    /**
     * Display the order success page.
     * Note: This is now handled by the OrderController.
     * This method can be safely removed if you have followed previous steps.
     * Keeping it here for now to avoid breaking other potential routes.
     */
    public function success(Order $order)
    {
        return view('order-success', ['order' => $order]);
    }
}


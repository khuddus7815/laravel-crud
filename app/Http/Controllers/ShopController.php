<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Order;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display the shop index page with all available items.
     */
    public function index()
    {
        // Fetch items that have a price greater than 0
        $items = Item::where('price', '>', 0)->latest()->get();

        // Pass the items to the 'shop' view
        return view('shop', ['items' => $items]);
    }


    /**
     * Display the order success page.
     */
    public function success(Order $order)
    {
        return view('order-success', ['order' => $order]);
    }
}
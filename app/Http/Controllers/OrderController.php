<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Show the order success page.
     *
     * @param  string  $orderNumber
     * @return \Illuminate\View\View
     */
    public function success($orderNumber)
    {
        // Find the order by its order number
        $order = Order::where('order_number', $orderNumber)->firstOrFail();

        // Return the order success view with the order data
        return view('order-success', compact('order'));
    }
}
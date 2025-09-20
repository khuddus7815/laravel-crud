<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class OrderStatusController extends Controller
{
    /**
     * Display the user's order status page.
     * This method fetches orders directly from the database for the logged-in user.
     */
    public function __invoke(Request $request): View
    {
        // Ensure the user is authenticated before fetching orders
        if (!Auth::check()) {
            // This should not happen because of the 'auth' middleware, but it's a good safeguard
            return view('my-orders', ['orders' => collect()]);
        }

        $orders = Auth::user()->orders()->with('items.item')->latest()->get();

        return view('my-orders', ['orders' => $orders]);
    }
}

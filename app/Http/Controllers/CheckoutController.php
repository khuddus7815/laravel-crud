<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CheckoutController extends Controller
{
    public function index()
    {
        $cartItems = Cart::with('item')
                        ->where('user_id', auth()->id())
                        ->get();

        return view('checkout', compact('cartItems'));
    }
} 
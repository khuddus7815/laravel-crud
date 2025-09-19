<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Update the cart data in the session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        // Get the cart data from the request body
        $cart = $request->json()->all();

        // Store the cart data in the user's session
        session(['cart' => $cart]);

        // Return a success response
        return response()->json(['success' => true]);
    }
}
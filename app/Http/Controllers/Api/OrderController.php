<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Item; // Make sure Item model is imported
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Import Log facade for better error logging
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'cart' => 'required|array|min:1',
            'cart.*.id' => 'required|exists:items,id',
            'cart.*.quantity' => 'required|integer|min:1',
            'coupon_code' => 'nullable|string|exists:coupons,code',
            'payment_method' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Validation failed', 'errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $cartItems = $request->input('cart');
            $customer = $request->user(); // auth:sanctum correctly provides the authenticated user

            if (!$customer) {
                return response()->json(['message' => 'Unauthenticated.'], 401);
            }

            // 1. Calculate total on the backend to prevent tampering
            $total = 0;
            // Fetch all item prices from the database at once for efficiency
            $itemIds = array_column($cartItems, 'id');
            $itemsInDb = Item::whereIn('id', $itemIds)->pluck('price', 'id');

            foreach ($cartItems as $cartItem) {
                if (!isset($itemsInDb[$cartItem['id']])) {
                    throw new \Exception("Item with ID {$cartItem['id']} not found in database.");
                }
                $total += $itemsInDb[$cartItem['id']] * $cartItem['quantity'];
            }

            // 2. Create the Order
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'customer_id' => $customer->id,
                // FIX: Use the correct properties from the Customer model
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'total' => $total,
                'payment_method' => $request->input('payment_method'),
                'status' => 'pending',
            ]);

            // 3. Create Order Items
            foreach ($cartItems as $cartItem) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'item_id' => $cartItem['id'],
                    'quantity' => $cartItem['quantity'],
                    // Use the price from the database, not from the frontend request
                    'price' => $itemsInDb[$cartItem['id']],
                ]);
            }

            DB::commit();

            // 4. Return a successful response
            return response()->json([
                'message' => 'Order placed successfully!',
                'order_number' => $order->order_number,
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Order placement failed: ' . $e->getMessage());
            return response()->json(['message' => 'An error occurred while placing the order.'], 500);
        }
    }
}

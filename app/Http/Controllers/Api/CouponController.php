<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;

class CouponController extends Controller
{
    /**
     * Validate and apply a coupon code.
     */
    public function apply(Request $request)
    {
        $code = strtoupper($request->input('code'));
        
        // Find the coupon by its code first.
        $coupon = Coupon::where('code', $code)->first();

        // Check if the coupon exists at all.
        if (!$coupon) {
            return response()->json(['valid' => false, 'message' => 'This coupon code does not exist.'], 404);
        }

        // Now, check if the found coupon has expired.
        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            return response()->json(['valid' => false, 'message' => 'This coupon has expired.'], 422);
        }

        // If the coupon is valid and not expired, return success.
        return response()->json([
            'valid' => true,
            'code' => $coupon->code,
            'discount' => [
                'type' => $coupon->type,
                'value' => $coupon->value,
            ],
            'message' => 'Coupon applied successfully!',
        ]);
    }
}
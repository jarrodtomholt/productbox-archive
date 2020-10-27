<?php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Gloudemans\Shoppingcart\Facades\Cart;

class CouponController extends Controller
{
    public function store(Request $request)
    {
        $coupon = Coupon::where('code', strtoupper($request->code))->firstOrFail();

        if ($coupon->expired) {
            return response()->json(['error' => 'Coupon could not be applied'], 404);
        }

        if ($coupon->minimum_order && $coupon->minimum_order > Cart::priceTotal()) {
            return response()->json([
                'error' => sprintf('Coupon can only be applied on orders greater than $%s', $coupon->minimum_order),
            ], 422);
        }

        session(['coupon' => sprintf('%s - %s', $coupon->code, $coupon->description)]);

        Cart::setGlobalDiscount($coupon->value);

        return new CartResource(null);
    }

    public function destroy(Request $request)
    {
        session()->forget('coupon');

        Cart::setGlobalDiscount(0);

        return new CartResource(null);
    }
}

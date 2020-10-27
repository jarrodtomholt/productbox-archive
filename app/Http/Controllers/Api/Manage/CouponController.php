<?php

namespace App\Http\Controllers\Api\Manage;

use App\Models\Coupon;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\CouponRequest;
use App\Http\Resources\Manage\CouponResource;

class CouponController extends Controller
{
    public function index()
    {
        return CouponResource::collection(Coupon::all());
    }

    public function store(CouponRequest $request)
    {
        $coupon = Coupon::create([
            'code' => $request->code,
            'value' => $request->value,
            'description' => $request->description,
            'minimum_order' => $request->minimum_order,
            'expires_at' => $request->expires_at,
        ]);

        return new CouponResource($coupon);
    }

    public function update(Coupon $coupon, CouponRequest $request)
    {
        $coupon->update([
            'code' => $request->code,
            'value' => $request->value,
            'description' => $request->description,
            'minimum_order' => $request->minimum_order,
            'expires_at' => $request->expires_at,
        ]);

        return new CouponResource($coupon);
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();

        return CouponResource::collection(Coupon::all());
    }
}

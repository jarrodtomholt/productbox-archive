<?php

namespace App\Http\Resources;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'content' => Cart::content(),
            'subtotal' => Cart::priceTotal(),
            'coupon' => [
                'description' => session()->get('coupon'),
                'discount' => Cart::discount(),
            ],
            'total' => Cart::total(),
        ];
    }
}

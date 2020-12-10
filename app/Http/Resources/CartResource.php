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
            'content' => Cart::count() ? Cart::content() : null,
            'subtotal' => filter_var(Cart::priceTotal(), FILTER_SANITIZE_NUMBER_INT) / 100,
            'coupon' => [
                'description' => session()->get('coupon'),
                'discount' => Cart::discount(),
            ],
            'total' => filter_var(Cart::total(), FILTER_SANITIZE_NUMBER_INT) / 100,
        ];
    }
}

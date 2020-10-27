<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Gloudemans\Shoppingcart\Facades\Cart;

class ClearCartController extends Controller
{
    public function destroy()
    {
        Cart::destroy();

        return new CartResource(null);
    }
}

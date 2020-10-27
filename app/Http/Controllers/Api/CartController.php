<?php

namespace App\Http\Controllers\Api;

use App\Models\Item;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use Gloudemans\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        return new CartResource(null);
    }

    public function store(Request $request)
    {
        $item = Item::where([
            'available' => true,
            'slug' => $request->item,
        ])->firstOrFail();

        Cart::add($item, 1, $request->selections);

        return new CartResource(null);
    }

    public function update(Request $request)
    {
        Cart::update($request->rowId, $request->quantity);

        return new CartResource(null);
    }

    public function destroy(Request $request)
    {
        Cart::remove($request->rowId);

        return new CartResource(null);
    }
}

<?php

namespace App\Http\Controllers\Api;

use Stripe\Charge;
use App\Models\Order;
use App\Facades\Settings;
use Illuminate\Support\Facades\Log;
use Stripe\Exception\CardException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CheckoutRequest;
use Stripe\Exception\ApiErrorException;
use Stripe\Exception\RateLimitException;
use Gloudemans\Shoppingcart\Facades\Cart;
use Stripe\Exception\ApiConnectionException;
use App\Http\Resources\CheckoutOrderResource;
use Stripe\Exception\InvalidRequestException;

class CheckoutController extends Controller
{
    public function store(CheckoutRequest $request)
    {
        if (!tenant()->stripe_connect_id) {
            return response()->json(['error' => 'There was an error communicating with the store'], 422);
        }

        try {
            $transaction = Charge::create([
                'amount' => intval(Cart::total() * 100) + $this->getDeliveryCharge($request->deliveryType),
                'currency' => 'AUD',
                'source' => $request->token,
            ], [
                'on_behalf_of' => tenant()->stripe_connect_id,
            ]);
        } catch (CardException | InvalidRequestException | Exception $e) {
            return response()->json(['error' => 'There was an error processing your payment'], 422);
        } catch (ApiConnectionException | ApiErrorException | RateLimitException $e) {
            Log::critical("stripe authentication or api failure : {$e->getMessage()}");

            return response()->json(['error' => 'There was an error communicating with the payment gateway'], 422);
        }

        $order = Order::create([
            'user_id' => auth()->user()->id ?? null,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'delivery_type' => $request->deliveryType,
            'address' => $request->address,
            'address2' => $request->address2,
            'city' => $request->city,
            'state' => $request->state,
            'postcode' => $request->postcode,
            'items' => Cart::content(),
            'coupon' => session()->get('coupon'),
            'subtotal' => Cart::priceTotal(),
            'discount' => Cart::discount(),
            'total' => Cart::total(),
            'delivery_fee' => $this->getDeliveryCharge($request->deliveryType),
            'paid' => $transaction->paid,
            'transaction_id' => $transaction->id,
            'transaction_source' => $transaction->source,
            'complete' => false,
        ]);

        return new CheckoutOrderResource($order);
    }

    private function getDeliveryCharge($deliveryType): ?int
    {
        return $deliveryType === 'delivery' ? intval(Settings::get('deliveryCharge') * 100) : null;
    }
}

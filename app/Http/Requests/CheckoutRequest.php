<?php

namespace App\Http\Requests;

use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Cart::count() >= 1;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email:rfc,filter'],
            'phone' => ['required', 'min:6', 'max:10'],
            'address' => ['required_if:deliveryType,delivery'],
            'city' => ['required_if:deliveryType,delivery'],
            'state' => ['required_if:deliveryType,delivery'],
            'postcode' => ['required_if:deliveryType,delivery'],
            'token' => ['required'],
        ];
    }
}

<?php

namespace App\Http\Controllers\Api\Auth;

use Illuminate\Http\Request;
use Vinkla\Hashids\Facades\Hashids;
use App\Http\Controllers\Controller;
use App\Http\Resources\Auth\UserResource;
use App\Http\Requests\Auth\AddressRequest;

class UserAddressController extends Controller
{
    public function store(AddressRequest $request)
    {
        auth()->user()->addresses()->create([
            'address' => $request->input('address'),
            'address2' => $request->input('address2'),
            'city' => $request->input('city'),
            'state' => $request->input('state'),
            'postcode' => $request->input('postcode'),
        ]);

        return new UserResource(auth()->user());
    }

    public function destroy(Request $request)
    {
        $request->validate(['address' => 'required']);

        auth()->user()->addresses()->where([
            'id' => Hashids::decode($request->input('address')),
        ])->delete();

        return new UserResource(auth()->user());
    }
}

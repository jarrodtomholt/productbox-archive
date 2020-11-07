<?php

namespace App\Http\Resources\Auth;

use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            // 'name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'token' => $this->currentAccessToken() ?? $this->createToken('token')->plainTextToken,
            // 'addresses' => AddressResource::collection($this->addresses),
        ];
    }
}

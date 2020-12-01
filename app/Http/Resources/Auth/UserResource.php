<?php

namespace App\Http\Resources\Auth;

use Jenssegers\Agent\Facades\Agent;
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
            'name' => $this->fullName,
            'email' => $this->email,
            'phone' => $this->phone,
            'token' => $this->currentAccessToken() ? $request->bearerToken() : $this->createToken(Agent::device())->plainTextToken,
            'addresses' => AddressResource::collection($this->addresses),
        ];
    }
}

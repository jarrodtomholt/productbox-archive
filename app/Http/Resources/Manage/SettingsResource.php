<?php

namespace App\Http\Resources\Manage;

use App\Facades\Settings;
use Illuminate\Http\Resources\Json\JsonResource;

class SettingsResource extends JsonResource
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
            'name' => Settings::get('name'),
            'phone' => Settings::get('phone'),
            'email' => Settings::get('email'),
            'messageOfTheDay' => Settings::get('messageOfTheDay'),
            'primaryColor' => Settings::get('primaryColor'),
            'secondaryColor' => Settings::get('secondaryColor'),
            'deliveryEnabled' => Settings::get('deliveryEnabled', false),
            'deliveryFee' => Settings::get('deliveryFee', null),
            'openingHours' => Settings::get('openingHours', []),
        ];
    }
}

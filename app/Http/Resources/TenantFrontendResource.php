<?php

namespace App\Http\Resources;

use App\Facades\Settings;
use Spatie\OpeningHours\OpeningHours;
use Illuminate\Http\Resources\Json\JsonResource;

class TenantFrontendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $openingHours = Settings::get('openingHours', null);

        if ($openingHours) {
            $openingHours = OpeningHours::createAndMergeOverlappingRanges($openingHours, tenant()->timezone);
        }

        return [
            'settings' => [
                'name' => Settings::get('name'),
                'phone' => Settings::get('phone'),
                'email' => Settings::get('email'),
                'logo' => null, // tenant()->logo,
                'messageOfTheDay' => Settings::get('messageOfTheDay'),
                'openingHours' => [
                    'isOpen' => $openingHours ? $openingHours->isOpen() : null,
                    'nextOpen' => $openingHours ? $openingHours->nextOpen(now())->format('Y-m-d H:i') : null,
                    'nextClose' => $openingHours ? $openingHours->nextClose(now())->format('Y-m-d H:i') : null,
                ],
                'theme' => [
                    'primaryColor' => Settings::get('primaryColor'),
                    'secondaryColor' => Settings::get('secondaryColor'),
                    'deliveryEnabled' => Settings::get('deliveryEnabled', false),
                    'deliveryFee' => Settings::get('deliveryFee', null),
                ],
            ],
            'categories' => CategoryResource::collection($this),
        ];
    }
}

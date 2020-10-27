<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => number_format($this->price / 100, 2),
            'image' => $this->image,
            'variants' => VariantResource::collection($this->variants),
            'options' => OptionResource::collection($this->options),
            'available' => $this->available,
        ];
    }
}

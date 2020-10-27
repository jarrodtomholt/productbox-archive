<?php

namespace App\Http\Controllers\Api\Manage;

use App\Models\Item;
use App\Models\Variant;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Requests\Manage\VariantRequest;

class VariantsController extends Controller
{
    public function store(Item $item, VariantRequest $request)
    {
        $item->variants()->create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return new ItemResource($item->fresh());
    }

    public function update(Item $item, Variant $variant, VariantRequest $request)
    {
        $variant->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return new ItemResource($item->fresh());
    }

    public function destroy(Item $item, Variant $variant)
    {
        $variant->delete();

        return new ItemResource($item->fresh());
    }
}

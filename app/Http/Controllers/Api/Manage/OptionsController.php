<?php

namespace App\Http\Controllers\Api\Manage;

use App\Models\Item;
use App\Models\Option;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Requests\Manage\OptionRequest;

class OptionsController extends Controller
{
    public function store(Item $item, OptionRequest $request)
    {
        $item->options()->create([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return new ItemResource($item->fresh());
    }

    public function update(Item $item, Option $option, OptionRequest $request)
    {
        $option->update([
            'name' => $request->name,
            'price' => $request->price,
        ]);

        return new ItemResource($item->fresh());
    }

    public function destroy(Item $item, Option $option)
    {
        $option->delete();

        return new ItemResource($item->fresh());
    }
}

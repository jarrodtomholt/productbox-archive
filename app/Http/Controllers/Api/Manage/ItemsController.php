<?php

namespace App\Http\Controllers\Api\Manage;

use App\Models\Item;
use App\Http\Controllers\Controller;
use App\Http\Resources\ItemResource;
use App\Http\Requests\Manage\ItemRequest;

class ItemsController extends Controller
{
    public function index()
    {
        return ItemResource::collection(Item::all());
    }

    public function store(ItemRequest $request)
    {
        $item = Item::create([
            'category_id' => $request->category_id,
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'available' => $request->available,
        ]);

        if ($request->hasFile('image')) {
            $item->setImage($request->file('image'));
        }

        return new ItemResource($item);
    }

    public function update(Item $item, ItemRequest $request)
    {
        $item->update([
            'name' => $request->name,
            'description' => $request->description,
            'price' => $request->price,
            'available' => $request->available,
        ]);

        if ($request->hasFile('image')) {
            $item->setImage($request->file('image'));
        }

        return new ItemResource($item);
    }

    public function destroy(Item $item)
    {
        $item->delete();

        return ItemResource::collection(Item::all());
    }
}

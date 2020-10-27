<?php

namespace App\Http\Controllers\Api\Manage;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Requests\Manage\CategoryRequest;

class CategoriesController extends Controller
{
    public function index()
    {
        return CategoryResource::collection(Category::all());
    }

    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return new CategoryResource($category);
    }

    public function update(Category $category, CategoryRequest $request)
    {
        $category->update([
            'name' => $request->name,
            'description' => $request->description,
        ]);

        return new CategoryResource($category);
    }

    public function destroy(Category $category)
    {
        if ($category->items->isNotEmpty()) {
            return response()->json(['error' => 'Category has items attached'], 400);
        }

        $category->delete();

        return CategoryResource::collection(Category::all());
    }
}

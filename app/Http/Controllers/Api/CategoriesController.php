<?php

namespace App\Http\Controllers\Api;

use App\Models\Category;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;

class CategoriesController extends Controller
{
    public function index()
    {
        $categories = cache()->remember('categories', now()->addHours(24), function () {
            return Category::all();
        });

        return CategoryResource::collection($categories);
    }
}

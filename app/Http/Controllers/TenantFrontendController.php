<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Http\Resources\TenantFrontendResource;

class TenantFrontendController extends Controller
{
    public function index()
    {
        $categories = cache()->remember('categories', now()->addHours(24), function () {
            return Category::all();
        });

        return new TenantFrontendResource($categories);
    }
}

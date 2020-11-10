<?php

namespace App\Http\Controllers\Api\Manage;

use App\Facades\Settings;
use App\Http\Controllers\Controller;
use App\Http\Requests\Manage\SettingsRequest;
use App\Http\Resources\Manage\SettingsResource;

class SettingsController extends Controller
{
    public function index()
    {
        return new SettingsResource(null);
    }

    public function store(SettingsRequest $request)
    {
        Settings::set($request->validated());

        return new SettingsResource(null);
    }
}

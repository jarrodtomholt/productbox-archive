<?php

namespace App\Http\Controllers\Api\Manage;

use App\Models\Admin;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Resources\Manage\AdminResource;
use App\Http\Requests\Manage\Auth\LoginRequest;

class AuthController extends Controller
{
    public function store(LoginRequest $request)
    {
        $user = Admin::where('email', $request->input('email'))->first();

        if (!$user || !Hash::check($request->input('password'), $user->password)) {
            return response('The provided credentials are incorrect.', 422);
        }

        return new AdminResource($user);
    }

    public function delete()
    {
        auth()->user()->tokens()->where('id', auth()->user()->currentAccessToken()->id)->delete();

        return response()->noContent();
    }
}

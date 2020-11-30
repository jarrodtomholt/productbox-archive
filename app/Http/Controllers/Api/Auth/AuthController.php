<?php

namespace App\Http\Controllers\Api\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\UserResource;

class AuthController extends Controller
{
    public function index()
    {
        return new UserResource(auth()->user());
    }

    public function store(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response('The provided credentials are incorrect.', 422);
        }

        return new UserResource($user);
    }

    public function delete()
    {
        auth()->user()->tokens()->where('id', auth()->user()->currentAccessToken()->id)->delete();

        return response()->noContent();
    }
}

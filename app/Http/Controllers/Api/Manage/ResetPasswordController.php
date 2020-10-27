<?php

namespace App\Http\Controllers\Api\Manage;

use Illuminate\Http\Request;
use App\Rules\PasswordStrength;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:App\Models\Admin,email'],
            'password' => ['required', 'confirmed', new PasswordStrength],
            'token' => 'required',
        ]);

        $response = Password::broker('admins')->reset($request->only(['email', 'password', 'token']), function ($user, $password) {
            $user->update(['password' => $password]);
        });

        return $response == Password::PASSWORD_RESET
            ? response()->json(['message' => 'Password reset.', 'status' => true], 201)
            : response()->json(['message' => 'Could not reset password', 'status' => false], 401);
    }
}

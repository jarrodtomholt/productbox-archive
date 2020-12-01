<?php

namespace App\Http\Controllers\Api\Manage\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required' , 'email', 'exists:App\Models\Admin,email']
        ]);

        $response = Password::broker('admins')->sendResetLink($request->only('email'));

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['message' => 'Reset link sent to your email.', 'status' => true], 201)
            : response()->json(['message' => 'Unable to send reset link', 'status' => false], 401);
    }
}

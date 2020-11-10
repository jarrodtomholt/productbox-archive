<?php

namespace App\Http\Requests\Auth;

use App\Rules\PasswordStrength;
use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->guest();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'firstname' => ['required', 'alpha'],
            'lastname' => ['required', 'alpha'],
            'email' => ['required', 'email:rfc,filter', 'unique:App\Models\User,email'],
            'phone' => ['required', 'min:6', 'max:10'],
            'password' => ['required', new PasswordStrength, 'confirmed'],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }
}

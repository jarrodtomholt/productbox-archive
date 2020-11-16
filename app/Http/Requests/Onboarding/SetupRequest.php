<?php

namespace App\Http\Requests\Onboarding;

use App\Rules\ValidAbn;
use App\Rules\PasswordStrength;
use App\Rules\ValidOpeningHours;
use Illuminate\Foundation\Http\FormRequest;

class SetupRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->route('tenant') && session()->has("{$this->route('tenant')->slug}.setup.verified");
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'logo' => ['sometimes', 'mimes:jpeg,png'],
            'abn' => ['required', new ValidAbn, 'unique:App\Models\Tenant,abn'],
            'state' => ['required', 'in:VIC,NSW,QLD,SA,WA,NT,ACT,TAS'],
            'name' => 'required',
            'email' => ['required', 'email:rfc,filter'],
            'password' => ['required', new PasswordStrength, 'confirmed'],
            'password_confirmation' => ['required', 'same:password'],
            'openingHours' => ['required', 'array', new ValidOpeningHours],
        ];
    }
}

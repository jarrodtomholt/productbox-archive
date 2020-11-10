<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class SettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'sometimes',
            'email' => ['sometimes', 'email:rfc,filter'],
            'phone' => ['sometimes', 'min:6', 'max:10'],
            'primaryColor' => 'sometimes',
            'secondaryColor' => 'sometimes',
            'deliveryEnabled' => ['required', 'boolean'],
            'deliveryFee' => ['required_if:deliveryEnabled,true', 'numeric'],
            'openingHours' => ['sometimes', 'array'],
        ];
    }
}

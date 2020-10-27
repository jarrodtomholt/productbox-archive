<?php

namespace App\Http\Requests\Manage;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
            'code' => 'required',
            'value' => ['required', 'numeric'],
            'description' => 'nullable',
            'minimum_order' => ['nullable', 'numeric'],
            'expires_at' => ['nullable', 'date', 'after_or_equal:today'],
        ];
    }
}

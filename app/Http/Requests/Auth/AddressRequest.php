<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

class AddressRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'address' => 'required',
            'address2' => 'sometimes',
            'city' => 'required',
            'state' => 'required|in:VIC,NSW,QLD,SA,WA,TAS,ACT,NT',
            'postcode' => 'required|digits:4',
        ];
    }
}

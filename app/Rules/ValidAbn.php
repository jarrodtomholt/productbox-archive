<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class ValidAbn implements Rule
{
    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_numeric($value)) {
            return false;
        }

        $weights = [10, 1, 3, 5, 7, 9, 11, 13, 15, 17, 19];
        $abn = str_split($value);

        if (count($abn) !== 11) {
            return false;
        }

        $abn[0] -= 1;

        $value = 0;

        foreach ($abn as $index => $digit) {
            $value += $digit * $weights[$index];
        }

        return $value % 89 === 0;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The provided ABN is invalid.';
    }
}

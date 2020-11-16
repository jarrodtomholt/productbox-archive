<?php

namespace App\Rules;

use Illuminate\Support\Facades\Log;
use Spatie\OpeningHours\OpeningHours;
use Illuminate\Contracts\Validation\Rule;

class ValidOpeningHours implements Rule
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
        try {
            OpeningHours::create(collect($value)->map(function ($item) {
                return [$item];
            })->toArray());

            return true;
        } catch (\Exception $e) {
            Log::debug($e->getMessage() . '|' . json_encode($value));
        }

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The opeing hours are invalid.';
    }
}

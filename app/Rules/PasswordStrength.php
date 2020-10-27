<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class PasswordStrength implements Rule
{
    private $message = '';

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (strlen($value) < 10) {
            $this->message = 'Password is too short. Must be at least 10 characters long';

            return false;
        }

        if (stristr($value, 'password') !== false) {
            $this->message = 'Password cannot contain the word password';

            return false;
        }

        if (!preg_match('@[A-Z]@', $value) && strlen($value) < 16) {
            $this->message = 'Password must contain at least 1 uppercase character';

            return false;
        }

        if (!preg_match('@[0-9]@', $value) && strpbrk($value, '~@#!`$%^&*()_+-=[]\\;\',./{}|:"<>?') === false && strlen($value) < 16) {
            $this->message = 'Password must contain at least 1 numerical digit or 1 special character';

            return false;
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}

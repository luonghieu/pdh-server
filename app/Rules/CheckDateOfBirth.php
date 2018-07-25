<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class CheckDateOfBirth implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */

    public function __construct()
    {
        //
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (0 == $value) {
            return true;
        }

        if (\DateTime::createFromFormat('Y-m-d', $value)) {
            $dateOfBirth = \DateTime::createFromFormat('Y-m-d', $value);
            $today = \Carbon\Carbon::today();
            if ($dateOfBirth <= $today) {
                return true;
            }
        }
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return ':attributeは、有効ではありません';
    }
}

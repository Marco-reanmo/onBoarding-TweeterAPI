<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Name implements Rule
{

    /**
     * Determine if the name passes the validation rule.
     * Examples:
     *  pass:
     *    - O'Connor
     *    - Müller
     *    - Schmitz-Meier
     *    - Voß
     *  fail
     *    - Bergmann123
     *    - Mustermann!$()
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value): bool
    {
        return preg_match('/^[A-Za-zßÄÖÜäöü.\' -]+$/', $value);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message(): string
    {
        return 'The :attribute must only contain letters, dots, hyphens or apostrophes.';
    }
}

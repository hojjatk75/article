<?php
/**
 * @author Hojjat koochak zadeh
 */

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class PersianChars implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! empty($value) && ! preg_match('/^[\x{0600}-\x{06FF}\s]+$/u', $value)) {
            $fail(':attribute must consist of Persian letters!');
        }
    }
}

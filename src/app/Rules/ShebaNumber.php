<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ShebaNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $length = strlen($value);
        if (($length < 24 || $length > 26) && !preg_match("/^(?:IR)?\d{24}$/", $value)) {
            $fail(__('validation.invalid_sheba'));
        }
    }
}

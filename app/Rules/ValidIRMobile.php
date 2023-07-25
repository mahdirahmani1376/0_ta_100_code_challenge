<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ValidIRMobile implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!clean_ir_mobile($value)) {
            $fail(__('validation.ir_mobile'));
        }
    }
}

<?php

namespace App\Http\Requests\BankGateway\DirectPayment;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBazaarPayDirectPaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required', Rule::exists('profiles', 'id'),],
        ];
    }
}

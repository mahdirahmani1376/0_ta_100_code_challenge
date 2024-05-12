<?php

namespace App\Http\Requests\Wallet\CreditTransaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCreditTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['nullable', 'string',],
            'invoice_id' => ['nullable', 'integer', Rule::exists('invoices', 'id'),]
        ];
    }
}

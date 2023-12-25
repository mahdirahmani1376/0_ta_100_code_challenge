<?php

namespace App\Http\Requests\Wallet\CreditTransaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCreditTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'gte:1',],
            'description' => ['nullable', 'string',],
            'date' => ['nullable', 'date_format:Y-m-d',],
            'invoice_id' => ['nullable', 'integer', Rule::exists('invoices', 'id'),]
        ];
    }
}

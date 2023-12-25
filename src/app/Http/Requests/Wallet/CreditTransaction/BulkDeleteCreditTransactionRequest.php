<?php

namespace App\Http\Requests\Wallet\CreditTransaction;

use Illuminate\Foundation\Http\FormRequest;

class BulkDeleteCreditTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'credit_transaction_ids' => ['required', 'array',],
            'credit_transaction_ids.*' => ['integer',],
        ];
    }
}

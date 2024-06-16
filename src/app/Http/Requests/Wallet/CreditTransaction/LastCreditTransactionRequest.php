<?php

namespace App\Http\Requests\Wallet\CreditTransaction;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class LastCreditTransactionRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'profile_id' => ['required','numeric',Rule::exists('profiles','id')],
            'credit_transaction_id' => ['required','numeric',Rule::exists('credit_transactions','id')]
        ];
    }
}

<?php

namespace App\Http\Requests\Profile\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class AddBalanceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => [
                'required',
                'integer',
                'min:' . config('payment.transactions.limit.min'),
                'max:' . config('payment.transactions.limit.max'),
            ],
            'client_id' => ['required', 'numeric',]
        ];
    }
}

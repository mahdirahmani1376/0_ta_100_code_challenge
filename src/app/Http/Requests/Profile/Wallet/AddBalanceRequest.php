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
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
        ];
    }
}

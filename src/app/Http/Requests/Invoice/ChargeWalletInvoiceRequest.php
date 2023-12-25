<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class ChargeWalletInvoiceRequest extends FormRequest
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
                'numeric',
                'min:' . config('payment.transactions.limit.min'),
                'max:' . config('payment.transactions.limit.max'),
            ],
            'profile_id' => ['required', 'integer', 'exists:profiles,id',],
            'admin_id' => ['nullable', 'integer',],
        ];
    }
}

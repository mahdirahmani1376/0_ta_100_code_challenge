<?php

namespace App\Http\Requests\Profile\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class ApplyBalanceToInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required', 'numeric', 'min:1'],
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
        ];
    }
}

<?php

namespace App\Http\Requests\Invoice;

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
            'amount' => ['nullable', 'integer', 'min:1'],
            'admin_id' => ['nullable', 'integer',],
            'profile_id' => ['nullable', 'integer', 'exists:profiles,id',],
        ];
    }
}

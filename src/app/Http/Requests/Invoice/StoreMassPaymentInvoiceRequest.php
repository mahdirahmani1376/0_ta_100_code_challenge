<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class StoreMassPaymentInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
            'invoice_ids' => ['required', 'array',],
            'invoice_ids.*' => ['numeric',],
        ];
    }
}

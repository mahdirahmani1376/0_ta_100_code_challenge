<?php

namespace App\Http\Requests\Profile\Invoice;

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
            'client_id' => ['required', 'numeric',],
            'invoice_ids' => ['required', 'array',],
            'invoice_ids.*' => ['numeric',],
        ];
    }
}

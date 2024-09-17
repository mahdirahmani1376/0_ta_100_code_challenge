<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class MergeInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_ids' => ['required', 'array', 'min:2'],
            'invoice_ids.*' => ['integer', 'exists:invoices,id'],
            'admin_id' => ['nullable', 'integer',],
        ];
    }
}

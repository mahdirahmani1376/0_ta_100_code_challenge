<?php

namespace App\Http\Requests\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_date' => ['nullable', 'date',],
            'due_date' => ['nullable', 'date',],
            'paid_at' => ['nullable', 'date',],
            'tax_rate' => ['nullable', 'integer', 'max:99', 'min:0',],
            'invoice_number' => ['nullable', 'int',],
            'fiscal_year' => ['nullable', 'string',],
            'note' => ['nullable',],
        ];
    }
}

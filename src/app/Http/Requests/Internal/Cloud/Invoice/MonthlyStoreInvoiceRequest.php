<?php

namespace App\Http\Requests\Internal\Cloud\Invoice;

use Illuminate\Foundation\Http\FormRequest;

class MonthlyStoreInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required', 'integer', 'exists:profiles,id',],
            'invoice_date' => ['required', 'date', 'date_format:Y-m-d'],
            'due_date' => ['required', 'date', 'date_format:Y-m-d'],
            'paid_at' => ['nullable', 'date', 'date_format:Y-m-d'],
            'tax_rate' => ['nullable', 'numeric',],
            'items' => ['required', 'array'],
            'items.*.description' => ['required', 'string'],
            'items.*.amount' => ['required', 'numeric'],
            'items.*.invoiceable_type' => ['nullable', 'string'],
            'items.*.invoiceable_id' => ['nullable', 'numeric'],
            'credit_transaction_ids' => ['required', 'array',],
            'credit_transaction_description' => ['required',]
        ];
    }
}

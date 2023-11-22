<?php

namespace App\Http\Requests\Internal\Cloud\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreInvoiceRequest extends FormRequest
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
            'status' => [Rule::in([
                Invoice::STATUS_UNPAID,
                Invoice::STATUS_PAID,
                Invoice::STATUS_COLLECTIONS,
            ])],
            'items' => ['required', 'array'],
            'items.*.description' => ['required', 'string'],
            'items.*.amount' => ['required', 'numeric'],
            'items.*.invoiceable_type' => ['nullable', 'string'],
            'items.*.invoiceable_id' => ['nullable', 'numeric'],
            'credit_transaction_id' => ['nullable', 'numeric',],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin\Invoice;

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
            'client_id' => ['required', 'integer'],
            'invoice_date' => ['required', 'date', 'date_format:Y-m-d'],
            'due_date' => ['required', 'date', 'date_format:Y-m-d'],
            'paid_at' => ['nullable', 'date', 'date_format:Y-m-d'],
            'status' => [Rule::in([
                Invoice::STATUS_UNPAID,
                Invoice::STATUS_DRAFT,
                Invoice::STATUS_REFUNDED,
            ])],
            'items' => ['required', 'array'],
            'items.*.description' => ['required', 'string'],
            'items.*.amount' => ['required', 'numeric'],
            'items.*.invoiceable_type' => ['nullable', 'string'],
            'items.*.invoiceable_id' => ['nullable', 'numeric'],
            'items.*.from_date' => ['nullable', 'date', 'date_format:Y-m-d'],
            'items.*.to_date' => ['nullable', 'date', 'date_format:Y-m-d'],
        ];
    }
}

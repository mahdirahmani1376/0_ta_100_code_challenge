<?php

namespace App\Http\Requests\Internal\Domain\Invoice;

use App\Models\Invoice;
use App\Models\Item;
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
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
            'due_date' => ['nullable',],
            'paid_at' => ['nullable',],
            'invoice_date' => ['nullable',],
            'status' => ['nullable', Rule::in([Invoice::STATUS_UNPAID, Invoice::STATUS_REFUNDED,])],
            'items' => ['required', 'array',],
            'items.*.description' => ['required', 'string',],
            'items.*.amount' => ['required', 'numeric',],
            'items.*.invoiceable_type' => ['required', 'string',],
            'items.*.invoiceable_id' => ['required', 'numeric',],
        ];
    }
}

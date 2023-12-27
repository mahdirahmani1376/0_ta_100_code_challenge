<?php

namespace App\Http\Requests\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BulkIndexInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', Rule::in(Invoice::STATUSES),],
            'items' => ['required', 'array',],
            'items.*.invoiceable_id' => ['required', 'integer',],
            'items.*.invoiceable_type' => ['nullable', 'max:255',],
        ];
    }
}

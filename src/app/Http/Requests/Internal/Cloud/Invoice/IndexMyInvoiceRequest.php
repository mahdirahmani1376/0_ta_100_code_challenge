<?php

namespace App\Http\Requests\Internal\Cloud\Invoice;

use App\Models\Invoice;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexMyInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'integer'],
            'invoiceable_ids' => ['required', 'array',],
            'status' => ['nullable', Rule::in([Invoice::STATUS_PAID, Invoice::STATUS_UNPAID, Invoice::STATUS_CANCELED,])],
            'search' => ['nullable',],
        ];
    }
}

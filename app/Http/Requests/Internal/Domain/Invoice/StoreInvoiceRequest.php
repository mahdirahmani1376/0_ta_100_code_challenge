<?php

namespace App\Http\Requests\Internal\Domain\Invoice;

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
            'client_id' => ['required', 'nullable',],
            'due_date' => ['nullable',],
            'items' => ['required', 'array',],
            'items.*.description' => ['required', 'string',],
            'items.*.amount' => ['required', 'numeric',],
            'items.*.invoiceable_type' => ['required', 'string', Rule::in([Item::TYPE_DOMAIN_SERVICE,])],
            'items.*.invoiceable_id' => ['required', 'numeric',],
        ];
    }
}

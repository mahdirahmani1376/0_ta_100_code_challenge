<?php

namespace App\Http\Requests\Internal\Cloud\Invoice;

use App\Models\Item;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoiceable_id' => ['required', 'numeric'],
            'invoiceable_type' => ['nullable', 'string', Rule::in([Item::TYPE_CLOUD, Item::TYPE_ADD_CLOUD_CREDIT])]
        ];
    }
}

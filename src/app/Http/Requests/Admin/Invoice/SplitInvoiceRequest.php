<?php

namespace App\Http\Requests\Admin\Invoice;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SplitInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'item_ids' => ['required', 'array'],
            'item_ids.*' => [
                'numeric',
                Rule::exists('items', 'id')->where('invoice_id', request()->invoice->getKey())
            ],
            'admin_id' => ['required', 'numeric',],
        ];
    }
}

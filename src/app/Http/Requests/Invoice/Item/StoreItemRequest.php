<?php

namespace App\Http\Requests\Invoice\Item;

use Illuminate\Foundation\Http\FormRequest;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description'      => ['required', 'string',],
            'amount'           => ['required', 'numeric',],
            'invoiceable_type' => ['nullable', 'string',],
            'invoiceable_id'   => ['nullable', 'integer',],
            'from_date'        => ['nullable', 'date',],
            'to_date'          => ['nullable', 'date',],
        ];
    }
}

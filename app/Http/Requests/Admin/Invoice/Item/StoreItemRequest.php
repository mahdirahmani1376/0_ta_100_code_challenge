<?php

namespace App\Http\Requests\Admin\Invoice\Item;

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
            'description' => ['required',],
            'amount' => ['required', 'numeric',],
        ];
    }
}

<?php

namespace App\Http\Requests\Invoice\Item;

use Illuminate\Foundation\Http\FormRequest;

class UpdateItemRequest extends FormRequest
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
            'from_date' => ['nullable', 'date',],
            'to_date' => ['nullable', 'date',],
        ];
    }
}

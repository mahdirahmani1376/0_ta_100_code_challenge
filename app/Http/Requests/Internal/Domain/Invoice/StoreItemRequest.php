<?php

namespace App\Http\Requests\Internal\Domain\Invoice;

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
            'description' => ['required', 'string',],
            'amount' => ['required', 'numeric',],
        ];
    }
}

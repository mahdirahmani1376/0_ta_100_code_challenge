<?php

namespace App\Http\Requests\Admin\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class StoreCreditTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['required',],
            'description' => [
                'nullable',
                'string'
            ],
            'date' => [
                'nullable',
                'date_format:Y-m-d'
            ],
        ];
    }
}

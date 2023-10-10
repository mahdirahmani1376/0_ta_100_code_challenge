<?php

namespace App\Http\Requests\Internal\Cloud\Wallet;

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
            'client_id' => ['required', 'numeric',],
            'amount' => ['required', 'numeric',],
            'description' => ['numeric',],
        ];
    }
}

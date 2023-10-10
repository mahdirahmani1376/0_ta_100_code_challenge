<?php

namespace App\Http\Requests\Internal\Cloud\Wallet;

use Illuminate\Foundation\Http\FormRequest;

class DeleteBulkCreditTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'ids' => ['required', 'array',],
            'ids.*' => ['numeric',],
        ];
    }
}

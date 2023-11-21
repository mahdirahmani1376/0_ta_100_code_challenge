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
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
            'amount' => ['required', 'numeric',],
            'description' => ['numeric',],
        ];
    }
}

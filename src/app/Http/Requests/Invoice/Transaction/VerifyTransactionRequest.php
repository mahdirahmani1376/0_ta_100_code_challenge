<?php

namespace App\Http\Requests\Invoice\Transaction;

use Illuminate\Foundation\Http\FormRequest;

class VerifyTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_id' => ['required', 'integer',],
        ];
    }
}

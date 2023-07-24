<?php

namespace App\Http\Requests\Admin\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

class IndexBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'max:255',]
        ];
    }
}

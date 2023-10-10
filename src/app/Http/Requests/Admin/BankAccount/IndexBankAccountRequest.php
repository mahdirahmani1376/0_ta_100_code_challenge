<?php

namespace App\Http\Requests\Admin\BankAccount;

use App\Models\BankAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'max:255',],
            'status' => ['nullable', Rule::in(BankAccount::STATUSES)]
        ];
    }
}

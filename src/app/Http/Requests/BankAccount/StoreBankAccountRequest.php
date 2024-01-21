<?php

namespace App\Http\Requests\BankAccount;

use App\Models\BankAccount;
use App\Rules\ShebaNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sheba_number' => ['required', new ShebaNumber],
            'account_number' => ['required', 'string'],
            'card_number' => ['required', 'string'],
            'title' => ['required', 'string'],
            'order' => ['required', 'integer'],
            'rahkaran_id' => ['nullable'],
            'status' => ['nullable', Rule::in(BankAccount::STATUSES)],
        ];
    }
}

<?php

namespace App\Http\Requests\ClientBankAccount;

use App\Models\ClientBankAccount;
use App\Rules\ShebaNumber;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['nullable', 'string', Rule::in(ClientBankAccount::STATUSES),],
            'bank_name' => ['nullable', 'string',],
            'card_number' => ['required_without:sheba_number',],
            'sheba_number' => ['required_without:card_number', new ShebaNumber,],
            'account_number' => ['nullable', 'max:255'],
            'zarinpal_bank_account_id' => ['nullable', 'integer',],
            'owner_name' => ['required', 'max:255',],
            'admin_id' => ['nullable', 'integer',],
        ];
    }
}

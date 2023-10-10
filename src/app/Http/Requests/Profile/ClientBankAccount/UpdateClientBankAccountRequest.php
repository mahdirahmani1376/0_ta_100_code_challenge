<?php

namespace App\Http\Requests\Profile\ClientBankAccount;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bank_name' => ['nullable', 'string',],
            'card_number' => ['required_without:sheba_number',],
            'sheba_number' => ['required_without:card_number', 'regex:/^(?:IR)?(?=.{24}$)[0-9]*$/',],
            'account_number' => ['nullable', 'max:255'],
            'zarinpal_bank_account_id' => ['nullable', 'numeric',],
            'owner_name' => ['nullable', 'max:255',],
        ];
    }
}

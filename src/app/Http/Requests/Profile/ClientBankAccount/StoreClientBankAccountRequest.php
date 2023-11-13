<?php

namespace App\Http\Requests\Profile\ClientBankAccount;

use Illuminate\Foundation\Http\FormRequest;

class StoreClientBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'client_id' => ['required', 'numeric',],
            'bank_name' => ['nullable', 'string',],
            'card_number' => ['required',],
            'sheba_number' => ['required', 'regex:/^(?:IR)?(?=.{24}$)[0-9]*$/',],
            'account_number' => ['nullable', 'max:255'],
            'owner_name' => ['required', 'max:255',],
        ];
    }
}

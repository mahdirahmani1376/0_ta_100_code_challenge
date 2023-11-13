<?php

namespace App\Http\Requests\Admin\ClientBankAccount;

use App\Models\ClientBankAccount;
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
            'card_number' => ['required',],
            'sheba_number' => ['required', 'regex:/^(?:IR)?(?=.{24}$)[0-9]*$/',],
            'account_number' => ['nullable', 'max:255'],
            'zarinpal_bank_account_id' => ['nullable', 'numeric',],
            'owner_name' => ['required', 'max:255',],
        ];
    }
}

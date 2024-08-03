<?php

namespace App\Http\Requests\ClientCashout;

use App\Models\ClientBankAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientCashoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount'                 => ['nullable', 'integer',],
            'profile_id'             => ['nullable', 'integer', 'exists:profiles,id',],
            'client_bank_account_id' => ['nullable', 'exists:client_bank_accounts,id',Rule::in([
                ClientBankAccount::STATUS_ACTIVE
            ])]
        ];
    }
}

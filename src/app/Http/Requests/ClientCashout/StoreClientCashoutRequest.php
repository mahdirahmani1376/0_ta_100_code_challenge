<?php

namespace App\Http\Requests\ClientCashout;

use App\Models\ClientBankAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientCashoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required', 'integer', 'exists:profiles,id',],
            'client_bank_account_id' => [
                'required',
                'integer',
                Rule::exists('client_bank_accounts', 'id')
                    ->where('profile_id', request('profile_id')),
                Rule::in([ClientBankAccount::STATUS_ACTIVE])
            ],
            'admin_id' => ['nullable', 'integer',],
            'amount' => ['nullable', 'integer',],
            'admin_note' => ['nullable', 'max:255',],
        ];
    }
}

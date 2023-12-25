<?php

namespace App\Http\Requests\ClientBankAccount;

use App\Models\ClientBankAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreClientBankAccountRequest extends FormRequest
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
            'sheba_number' => ['required', 'regex:/^(?:IR)?\d{24}$/',],
            'account_number' => ['nullable', 'max:255',],
            'owner_name' => ['required', 'max:255',],
            'profile_id' => ['required', 'integer', 'exists:profiles,id',],
            'admin_id' => ['nullable', 'integer',],
        ];
    }
}

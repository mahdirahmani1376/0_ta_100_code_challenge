<?php

namespace App\Http\Requests\Profile\ClientBankAccount;

use App\Models\ClientBankAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexClientBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'max:255',],
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
            'status' => ['nullable', Rule::in(ClientBankAccount::STATUSES),],
        ];
    }
}

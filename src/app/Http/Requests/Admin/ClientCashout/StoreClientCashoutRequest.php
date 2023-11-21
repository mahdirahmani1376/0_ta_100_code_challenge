<?php

namespace App\Http\Requests\Admin\ClientCashout;

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
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
            'client_bank_account_id' => [
                'required',
                'numeric',
                Rule::exists('client_bank_accounts', 'id')
                    ->where('profile_id', request('profile_id')),
            ],
            'admin_id' => [
                'required',
                'numeric',
            ],
            'amount' => [
                'nullable',
                'numeric',
            ],
            'admin_note' => [
                'nullable',
                'max:255',
            ],
        ];
    }
}

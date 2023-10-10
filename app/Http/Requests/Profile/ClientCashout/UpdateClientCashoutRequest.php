<?php

namespace App\Http\Requests\Profile\ClientCashout;

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
            'client_id' => ['required', 'numeric',],
            'client_bank_account_id' => [
                'required',
                'numeric',
                Rule::exists('client_bank_accounts', 'id')
                    ->where('client_id', request('client_id')),
            ],
        ];
    }
}

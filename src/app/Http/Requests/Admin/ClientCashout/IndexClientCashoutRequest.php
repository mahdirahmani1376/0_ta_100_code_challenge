<?php

namespace App\Http\Requests\Admin\ClientCashout;

use App\Models\ClientCashout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexClientCashoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['nullable', 'numeric',],
            'status' => ['nullable', Rule::in(ClientCashout::STATUSES),],
            'client_bank_account_id' => ['nullable', 'exists:client_bank_accounts,id'],
        ];
    }
}

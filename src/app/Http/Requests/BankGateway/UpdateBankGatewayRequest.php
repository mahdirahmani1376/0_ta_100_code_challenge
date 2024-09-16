<?php

namespace App\Http\Requests\BankGateway;

use App\Models\BankGateway;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBankGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'                       => ['required', 'max:255',],
            'name_fa'                    => ['required', 'max:255'], // old 'label' field
            'is_direct_payment_provider' => ['nullable', 'boolean',],
            'status'                     => ['required', 'string', Rule::in(BankGateway::STATUSES),],
            'order'                      => ['nullable', 'integer',],
            'config.merchant_id'         => ['nullable', 'max:255',],
            'config.request_url'         => ['nullable', 'max:255',],
            'config.verify_url'          => ['nullable', 'max:255',],
            'config.start_url'           => ['nullable', 'max:255',],
            'config.username'            => ['nullable', 'max:50',],
            'config.password'            => ['nullable', 'max:50',],
            'config.terminal_id'         => ['nullable', 'max:255',],
            'config.api_key'             => ['nullable', 'max:255',],
        ];
    }
}

<?php

namespace App\Http\Requests\Admin\BankGateway;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBankGatewayRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required',
            'name_fa' => 'required|max:255', // old 'label' field
            'provider' => 'required',
            'status' => 'required|string',
            'merchant_id' => 'required|max:255',
            'request_url' => 'nullable|max:255',
            'verify_url' => 'nullable|max:255',
            'start_url' => 'nullable|max:255',
            'username' => 'nullable|max:50',
            'password' => 'nullable|max:50',
            'terminal_id' => 'nullable|max:255',
            'api_key' => 'nullable|max:255',
        ];
    }
}

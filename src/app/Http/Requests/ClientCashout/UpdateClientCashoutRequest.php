<?php

namespace App\Http\Requests\ClientCashout;

use Illuminate\Foundation\Http\FormRequest;

class UpdateClientCashoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'amount' => ['nullable', 'integer',],
            'profile_id' => ['nullable', 'integer', 'exists:profiles,id',],
        ];
    }
}

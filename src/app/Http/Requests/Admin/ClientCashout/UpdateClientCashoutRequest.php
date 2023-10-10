<?php

namespace App\Http\Requests\Admin\ClientCashout;

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
            'amount' => [
                'nullable',
                'numeric',
            ],
        ];
    }
}

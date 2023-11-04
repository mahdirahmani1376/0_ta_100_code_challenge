<?php

namespace App\Http\Requests\Admin\ClientCashout;

use Illuminate\Foundation\Http\FormRequest;

class ActionOnClientCashoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_id' => ['required', 'numeric',],
            'admin_note' => ['nullable', 'max:255',],
        ];
    }
}

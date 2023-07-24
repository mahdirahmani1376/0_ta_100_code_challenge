<?php

namespace App\Http\Requests\Admin\BankAccount;

use Illuminate\Foundation\Http\FormRequest;

class StoreBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sheba_number' => 'required|string',
            'account_number' => 'required|string',
            'card_number' => 'required|string',
            'title' => 'required|string',
            'display_order' => 'required|integer',
            'rahkaran_id' => 'nullable',
        ];
    }
}

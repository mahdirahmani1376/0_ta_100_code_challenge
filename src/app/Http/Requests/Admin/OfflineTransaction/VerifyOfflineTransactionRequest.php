<?php

namespace App\Http\Requests\Admin\OfflineTransaction;

use App\Models\OfflineTransaction;
use Illuminate\Foundation\Http\FormRequest;

class VerifyOfflineTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'admin_id' => ['required', 'numeric',],
        ];
    }
}

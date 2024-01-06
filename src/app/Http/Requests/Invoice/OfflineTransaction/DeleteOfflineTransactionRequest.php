<?php

namespace App\Http\Requests\Invoice\OfflineTransaction;

use App\Models\OfflineTransaction;
use App\Rules\ValidIRMobile;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DeleteOfflineTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'profile_id' => ['required_without:admin_id', 'integer', 'exists:profiles,id',],
            'admin_id' => ['required_without:profile_id', 'integer',],
        ];
    }
}

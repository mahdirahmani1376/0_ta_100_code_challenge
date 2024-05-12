<?php

namespace App\Http\Requests\BankAccount;

use App\Models\BankAccount;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexBankAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort'           => ['string', 'nullable', Rule::in(get_sortable_items((new BankAccount())->getFillable())),],
            'sort_direction' => ['string', 'nullable', Rule::in('desc', 'asc'),],
            'search'         => ['nullable', 'max:255',],
            'status'         => ['nullable', Rule::in(BankAccount::STATUSES)],
            'admin_id'       => ['filled', 'integer',],
            'export'         => ['nullable', 'bool',],
        ];
    }

    public function prepareForValidation()
    {
        $this->mergeIfMissing([
            'sort' => 'order'
        ]);
    }
}

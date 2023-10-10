<?php

namespace App\Http\Requests\Admin\Wallet;

use App\Models\CreditTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexCreditTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'sort'          => ['string', 'nullable', Rule::in(get_sortable_items((new CreditTransaction())->getFillable()))],
            'sortDirection' => ['string', 'nullable', Rule::in('desc', 'asc')],
            'client_id'     => 'nullable|string',
            'search'        => 'nullable|string|max:255',
            'date'          => ['date_format:Y-m-d', 'nullable'],
            'from_date'     => ['nullable', 'date', 'date_format:Y-m-d', 'before_or_equal:to_date'],
            'to_date'       => ['nullable', 'date', 'date_format:Y-m-d', 'after_or_equal:from_date'],
        ];
    }
}

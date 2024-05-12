<?php

namespace App\Http\Requests\Invoice\OfflineTransaction;

use App\Models\OfflineTransaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexOfflineTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'string'],
            'sort' => ['string', 'nullable', Rule::in(get_sortable_items((new OfflineTransaction())->getFillable()))],
            'sort_direction' => ['string', 'nullable', Rule::in('desc', 'asc')],
            'from_date' => ['nullable', 'date', 'before_or_equal:to_date'],
            'to_date' => ['nullable', 'date', 'after_or_equal:from_date'],
            'status' => ['nullable', Rule::in(OfflineTransaction::STATUSES)],
            'profile_id' => ['integer', 'nullable',],
        ];
    }
}

<?php

namespace App\Http\Requests\Profile\Transaction;

use App\Models\Transaction;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexTransactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['nullable',],
            'status' => ['string', 'nullable', Rule::in(Transaction::STATUSES)],
            'sort' => ['string', 'nullable', Rule::in(get_sortable_items((new Transaction())->getFillable()))],
            'sortDirection' => ['string', 'nullable', Rule::in('desc', 'asc')],
            'search' => ['nullable', 'string'],
            'profile_id' => ['required', 'numeric', 'exists:profiles,id',],
        ];
    }
}

<?php

namespace App\Http\Requests\Invoice\MoadianLog;

use App\Models\MoadianLog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class IndexMoadianLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'search' => ['nullable', 'max:255',],
            'status' => ['nullable', Rule::in(MoadianLog::STATUSES)],
            'invoice_id' => ['nullable', 'integer',],
            'reference_code' => ['nullable', 'max:255',],
            'tax_id' => ['nullable', 'max:255',],
            'sort' => ['nullable', 'max:255', Rule::in(get_sortable_items((new MoadianLog())->getFillable())),],
            'sort_direction' => ['nullable', 'max:255', Rule::in('desc', 'asc'),],
        ];
    }
}

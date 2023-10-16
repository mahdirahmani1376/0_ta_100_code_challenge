<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReportRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'view' => ['nullable', 'numeric'],
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ];
    }
}

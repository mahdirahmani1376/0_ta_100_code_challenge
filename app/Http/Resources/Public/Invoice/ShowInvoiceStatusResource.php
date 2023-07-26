<?php

namespace App\Http\Resources\Public\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowInvoiceStatusResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Invoice $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'due_date' => $this->due_date?->toDateTimeString(),
            'balance' => $this->balance,
            'status' => $this->status,
        ];
    }
}

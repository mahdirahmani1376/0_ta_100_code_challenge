<?php

namespace App\Http\Resources\Internal\Cloud\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MonthlyInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Invoice $this */
        return [
            'id' => $this->id,
            'status' => $this->status,
            'credit_transaction_id' => $this->credit_transaction_id,
        ];
    }
}

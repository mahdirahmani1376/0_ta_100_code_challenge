<?php

namespace App\Http\Resources\Admin\Invoice;

use App\Models\InvoiceNumber;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceNumberResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var InvoiceNumber $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'invoice_id' => $this->invoice_id,
            'invoice_number' => $this->invoice_number,
            'fiscal_year' => $this->fiscal_year,
            'status' => $this->status,
            'type' => $this->type,
        ];
    }
}

<?php

namespace App\Http\Resources\Admin\Invoice;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceNumberResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        /** @var \App\Models\InvoiceNumber $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'deleted_at' => $this->deleted_at?->toDateTimeString(),
            'invoice_id' => $this->invoice_id,
            'invoice_number' => $this->invoice_number,
            'fiscal_year' => $this->fiscal_year,
            'status' => $this->status,
            'type' => $this->type,
        ];
    }
}

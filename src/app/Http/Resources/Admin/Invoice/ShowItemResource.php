<?php

namespace App\Http\Resources\Admin\Invoice;

use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Item $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'invoice_id' => $this->invoice_id,
            'invoiceable_id' => $this->invoiceable_id,
            'invoiceable_type' => $this->invoiceable_type,
            'amount' => $this->amount,
            'discount' => $this->discount,
            'from_date' => $this->from_date?->toDateTimeString(),
            'to_date' => $this->to_date?->toDateTimeString(),
            'description' => $this->description,
            'calculation_type' => $this->calculationType(),
        ];
    }
}

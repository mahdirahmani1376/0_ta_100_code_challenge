<?php

namespace App\Http\Resources\Invoice\MoadianLog;

use App\Models\MoadianLog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MoadianLogResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var MoadianLog $this */
        return [
            'id' => $this->id,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'invoice_id' => $this->invoice_id,
            'status' => $this->status,
            'reference_code' => $this->reference_code,
            'error' => $this->error,
            'tax_id' => $this->tax_id,
        ];
    }
}

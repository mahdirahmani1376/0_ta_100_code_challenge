<?php

namespace App\Http\Resources\Internal\Cloud\Invoice;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Invoice $this */
        return [
            'id' => $this->id,
            'status' => $this->status,
        ];
    }
}

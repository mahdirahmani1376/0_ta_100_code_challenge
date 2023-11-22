<?php

namespace App\Http\Resources\Admin\Invoice\InvoiceNumber;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowInvoiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var Invoice $this */
        return [
            'status' => $this->status,
            'profile_id' => $this->profile_id,
        ];
    }
}

<?php

namespace App\Http\Resources\Internal\Cloud\Wallet;

use App\Http\Resources\Internal\Cloud\Invoice\ShowInvoiceResource;
use App\Models\CreditTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowCreditTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var CreditTransaction $this */
        return [
            'id' => $this->id,
            'invoice_id' => $this->invoice_id,
            'invoice' => $this->when($this->invoice, function () {
                return ShowInvoiceResource::make($this->invoice);
            })
        ];
    }
}

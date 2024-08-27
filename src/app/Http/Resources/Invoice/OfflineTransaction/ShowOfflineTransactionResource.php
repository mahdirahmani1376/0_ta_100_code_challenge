<?php

namespace App\Http\Resources\Invoice\OfflineTransaction;

use App\Http\Resources\BankAccount\ShowBankAccountResource;
use App\Http\Resources\Invoice\InvoiceOfTransactionResource;
use App\Http\Resources\Invoice\Transaction\TransactionWithoutInvoiceResource;
use App\Models\OfflineTransaction;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShowOfflineTransactionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        /** @var OfflineTransaction $this */
        return [
            'id'             => $this->id,
            'created_at'     => $this->created_at?->toDateTimeString(),
            'updated_at'     => $this->updated_at?->toDateTimeString(),
            'paid_at'        => $this->paid_at?->toDateTimeString(),
            'profile_id'     => $this->profile_id,
            'invoice_id'     => $this->invoice_id,
            'transaction'    => TransactionWithoutInvoiceResource::make($this->transaction),
            'bank_account'   => ShowBankAccountResource::make($this->bankAccount),
            'admin_id'       => $this->admin_id,
            'amount'         => $this->amount,
            'status'         => $this->status,
            'payment_method' => $this->payment_method,
            'tracking_code'  => $this->tracking_code,
            'mobile'         => $this->mobile,
            'description'    => $this->description,
            'invoice'        => InvoiceOfTransactionResource::make($this->invoice)
        ];
    }
}

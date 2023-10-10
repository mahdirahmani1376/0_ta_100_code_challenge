<?php

namespace App\Http\Resources\Admin\FinanceReport;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FinanceReportResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'offline_transaction_today_count' => $this['offline_transaction_today_count'],
            'offline_transaction_rejected_count' => $this['offline_transaction_rejected_count'],
            'offline_transaction_latest' => OfflineTransactionResource::collection($this['offline_transaction_latest']),
            'transaction_count' => $this['transaction_count'],
            'transaction_today_approved_count' => $this['transaction_today_approved_count'],
            'transaction_today_rejected_count' => $this['transaction_today_rejected_count'],
            'transactions_latest' => TransactionResource::collection($this['transactions_latest']),
            'invoice_count' => $this['invoice_count'],
            'invoice_today_count' => $this['invoice_today_count'],
            'invoice_paid_count' => $this['invoice_paid_count'],
            'invoice_income_today' => $this['invoice_income_today'],
            'invoice_latest' => InvoiceResource::collection($this['invoice_latest']),
        ];
    }
}

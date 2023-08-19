<?php

namespace App\Actions\Admin\Invoice;

use App\Exceptions\Http\BadRequestException;
use App\Models\Invoice;
use App\Models\InvoiceNumber;
use App\Services\Admin\Invoice\AssignInvoiceNumberService;

class DownloadInvoiceBillAction
{
    private AssignInvoiceNumberService $assignInvoiceNumberService;

    public function __construct(AssignInvoiceNumberService $assignInvoiceNumberService)
    {
        $this->assignInvoiceNumberService = $assignInvoiceNumberService;
    }

    public function __invoke(Invoice $invoice, array $data)
    {
        if (!in_array($invoice->status, [
            Invoice::STATUS_PAID,
            Invoice::STATUS_REFUNDED,
            Invoice::STATUS_UNPAID,
            Invoice::STATUS_COLLECTIONS,
            Invoice::STATUS_PAYMENT_PENDING
        ])) {
            throw new BadRequestException(trans('finance.invoice.OfficialInvoiceStatusError'));
        }

        $invoiceDate = $invoice->status === Invoice::STATUS_PAID ? $invoice->paid_at : $invoice->created_at;

        if (empty($invoiceDate)) {
            throw new BadRequestException(trans('finance.invoice.NotCorrectStatus'));
        }

        if (($invoiceDate->lessThan('2021-03-21') && $invoice->invoiceNumber()->doesntExist() && !$invoice->is_credit && $invoice->status !== Invoice::STATUS_UNPAID)) {
            throw new BadRequestException(trans('finance.invoice.LessThan1400'));
        }

        if (!$invoice->is_credit &&
            in_array($invoice->status, [
                Invoice::STATUS_REFUNDED,
                Invoice::STATUS_PAID,
                Invoice::STATUS_COLLECTIONS
            ]) &&
            $invoice->invoiceNumber()->doesntExist() &&
            $invoiceDate->greaterThan('2021-03-21')
        ) {
            if ($invoice->status === Invoice::STATUS_PAID || $invoice->status === Invoice::STATUS_COLLECTIONS) {
                ($this->assignInvoiceNumberService)($invoice, InvoiceNumber::TYPE_PAID);
            }
            if ($invoice->status === Invoice::STATUS_REFUNDED) {
                ($this->assignInvoiceNumberService)($invoice, InvoiceNumber::TYPE_REFUND);
            }
        }

        return $invoice;
    }
}

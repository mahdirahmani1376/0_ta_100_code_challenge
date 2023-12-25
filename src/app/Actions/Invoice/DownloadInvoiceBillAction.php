<?php

namespace App\Actions\Invoice;

use App\Exceptions\Http\BadRequestException;
use App\Models\AdminLog;
use App\Models\Invoice;
use App\Services\Invoice\AssignInvoiceNumberService;

class DownloadInvoiceBillAction
{
    public function __construct(private readonly AssignInvoiceNumberService $assignInvoiceNumberService)
    {
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
            throw new BadRequestException(__('finance.invoice.OfficialInvoiceStatusError'));
        }
        if ($invoice->is_credit) {
            throw new BadRequestException(__('finance.invoice.CreditInvoiceCannotHaveInvoiceNumber'));
        }
        $invoiceDate = $invoice->status === Invoice::STATUS_PAID ? $invoice->paid_at : $invoice->created_at;

        if (empty($invoiceDate)) {
            throw new BadRequestException(__('finance.invoice.NotCorrectStatus'));
        }

        if (($invoiceDate->lessThan('2021-03-21') && $invoice->invoiceNumber()->doesntExist() && !$invoice->is_credit && $invoice->status !== Invoice::STATUS_UNPAID)) {
            throw new BadRequestException(__('finance.invoice.LessThan1400'));
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
            $invoiceNumber = ($this->assignInvoiceNumberService)($invoice);
            admin_log(AdminLog::DOWNLOAD_INVOICE_OFFICIAL_BILL, $invoiceNumber);
        }

        admin_log(AdminLog::DOWNLOAD_INVOICE_OFFICIAL_BILL, $invoice, $invoice->getChanges(), validatedData: $invoice->getChanges());

        return $invoice;
    }
}

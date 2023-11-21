<?php

namespace App\Services\Admin\Invoice;

use App\Exceptions\SystemException\MergeInvoiceException;
use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Support\Collection;

class ValidateInvoicesBeforeMergeService
{
    public function __construct(private readonly InvoiceRepositoryInterface $invoiceRepository)
    {
    }

    public function __invoke(array $invoiceIds): Collection
    {
        $invoices = $this->invoiceRepository->indexByIds($invoiceIds);

        if ($invoices->count() < 2) {
            throw MergeInvoiceException::make(trans('finance.mergeInvoice.needMoreInvoice'));
        }

        // Check if all invoices share the same profile_id
        if ($invoices->count() !== $invoices->where('profile_id', $invoices->first()->profile_id)->count()) {
            throw MergeInvoiceException::make(trans('finance.mergeInvoice.withDifferentClient'));
        }
        // Check if all invoices share the same tax_rate
        if ($invoices->count() !== $invoices->where('tax_rate', $invoices->first()->tax_rate)->count()) {
            throw MergeInvoiceException::make(trans('finance.mergeInvoice.withDifferentTaxRate'));
        }

        $invoices->each(function (Invoice $invoice) {
            if ($invoice->is_credit) {
                throw MergeInvoiceException::make(trans('finance.mergeInvoice.notCredit', [
                    'invoice_id' => $invoice->getKey()
                ]));
            }
            if ($invoice->is_mass_payment) {
                throw MergeInvoiceException::make(trans('finance.mergeInvoice.notMassPayment', [
                    'invoice_id' => $invoice->getKey()
                ]));
            }
            if (!empty($invoice->rahkaran_id)) {
                throw MergeInvoiceException::make(trans('finance.mergeInvoice.notRahkaranId', [
                    'invoice_id' => $invoice->getKey()
                ]));
            }
            if (!in_array($invoice->status, [
                Invoice::STATUS_UNPAID,
                Invoice::STATUS_CANCELED,
                Invoice::STATUS_DRAFT
            ]))
                throw MergeInvoiceException::make(trans('finance.mergeInvoice.notAllowedStatus', [
                    'invoice_id' => $invoice->getKey(),
                    'status' => $invoice->status
                ]));
        });

        return $invoices;
    }
}

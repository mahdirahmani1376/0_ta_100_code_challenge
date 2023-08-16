<?php

namespace App\Services\Admin\Invoice;

use App\Exceptions\SystemException\MergeInvoiceException;
use App\Models\Invoice;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class ValidateInvoicesBeforeMergeService
{
    private InvoiceRepositoryInterface $invoiceRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }

    public function __invoke(array $invoiceIds): Collection
    {
        $invoices = $this->invoiceRepository->indexByIds($invoiceIds);

        if ($invoices->count() < 2) {
            throw MergeInvoiceException::make(trans('finance.mergeInvoice.needMoreInvoice'));
        }

        // Check if all invoices share the same client_id
        if ($invoices->count() !== $invoices->where('client_id', $invoices->first()->client_id)->count()) {
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

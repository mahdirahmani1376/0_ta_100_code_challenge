<?php

namespace App\Services\Invoice;

use App\Exceptions\Http\BadRequestException;
use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class StoreMassPaymentInvoiceService
{
    private InvoiceRepositoryInterface $invoiceRepository;
    private ItemRepositoryInterface $itemRepository;

    public function __construct(InvoiceRepositoryInterface $invoiceRepository, ItemRepositoryInterface $itemRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
        $this->itemRepository = $itemRepository;
    }

    public function __invoke(array $data)
    {
        // prepare valid invoices to be merged into one mass_payment_invoice
        // a valid invoice has to have UNPAID status and its "is_credit" and "is_mass_payment" fields to be FALSE
        $invoicesForMassPayment = $this->invoiceRepository->prepareInvoicesForMassPayment($data);

        if ($invoicesForMassPayment->count() <= 1) {
            throw new BadRequestException(__('finance.error.CannotMakeMassPaymentInvoice'));
        }

        /** @var Invoice $massPaymentInvoice */
        $massPaymentInvoice = $this->invoiceRepository->create([
            'payment_method' => Invoice::PAYMENT_METHOD_CREDIT,
            'due_date' => null,
            'is_mass_payment' => true,
            'tax_rate' => Invoice::defaultTaxRate(),
            'status' => Invoice::STATUS_UNPAID,
            'profile_id' => $data['profile_id'],
        ], [
            'payment_method',
            'due_date',
            'is_mass_payment',
            'tax_rate',
            'status',
            'profile_id',
        ]);

        // Create an Item for each valid invoices ( $invoicesForMassPayment ) with the amount of those invoice's "balance"
        // which is the "remaining" amount of money that needs to be paid until an Invoice is considered "paid"
        /** @var Invoice $invoiceForMassPayment */
        foreach ($invoicesForMassPayment as $invoiceForMassPayment) {
            $this->itemRepository->create([
                'invoice_id' => $massPaymentInvoice->getKey(),
                'description' => __('finance.invoice.MassPaymentInvoice', ['id' => $invoiceForMassPayment->getKey()]),
                'amount' => $invoiceForMassPayment->balance,
                'invoiceable_type' => Item::TYPE_MASS_PAYMENT_INVOICE,
                'invoiceable_id' => $invoiceForMassPayment->getKey(),
            ]);
        }

        return $massPaymentInvoice;
    }
}

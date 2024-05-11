<?php

namespace App\Services\Invoice;

use App\Exceptions\Http\BadRequestException;
use App\Integrations\MainApp\MainAppConfig;
use App\Models\Invoice;
use App\Models\Item;
use App\Repositories\Invoice\Interface\InvoiceRepositoryInterface;
use App\Repositories\Invoice\Interface\ItemRepositoryInterface;

class StoreMassPaymentInvoiceService
{

    public function __construct(
        private readonly InvoiceRepositoryInterface    $invoiceRepository,
        private readonly ItemRepositoryInterface       $itemRepository,
        private readonly CalcInvoicePriceFieldsService $calcInvoicePriceFieldsService
    )
    {
    }

    public function __invoke(array $data): Invoice
    {
        // prepare valid invoices to be merged into one mass_payment_invoice
        // a valid invoice has to have UNPAID status and its "is_credit" and "is_mass_payment" fields to be FALSE
        $invoicesForMassPayment = $this->invoiceRepository->prepareInvoicesForMassPayment($data);

        if ($invoicesForMassPayment->count() <= 1) {
            throw new BadRequestException(__('finance.error.CannotMakeMassPaymentInvoice'));
        }

        /** @var Invoice $massPaymentInvoice */
        $massPaymentInvoice = $this->invoiceRepository->create([
            'payment_method'  => Invoice::PAYMENT_METHOD_CREDIT,
            'due_date'        => null,
            'is_mass_payment' => true,
            'tax_rate'        => 0,
            'status'          => Invoice::STATUS_UNPAID,
            'profile_id'      => $data['profile_id'],
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
            $invoiceForMassPayment = ($this->calcInvoicePriceFieldsService)($invoiceForMassPayment);
            if ($invoiceForMassPayment > 0) {
                $this->itemRepository->create([
                    'invoice_id'       => $massPaymentInvoice->getKey(),
                    'description'      => __('finance.invoice.MassPaymentInvoice', ['id' => $invoiceForMassPayment->getKey()]),
                    'amount'           => round_amount($massPaymentInvoice->balance),
                    'invoiceable_type' => Item::TYPE_MASS_PAYMENT_INVOICE,
                    'invoiceable_id'   => $invoiceForMassPayment->getKey(),
                ]);
            }
        }

        return $massPaymentInvoice;
    }
}

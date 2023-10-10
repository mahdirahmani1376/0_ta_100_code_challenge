<?php

namespace App\Http\Controllers\Internal\Domain\Invoice;

use App\Actions\Internal\Domain\Invoice\ShowUnpaidInvoiceByDomainAction;
use App\Http\Resources\Internal\Domain\Invoice\InvoiceResource;
use Illuminate\Http\Response;

class ShowUnpaidInvoiceByDomainController
{
    public function __construct(private readonly ShowUnpaidInvoiceByDomainAction $findInvoiceByDomainIdAction)
    {
    }

    public function __invoke($domainId)
    {
        $invoice = ($this->findInvoiceByDomainIdAction)($domainId);

        if (is_null($invoice)) {
            return response()->json([], Response::HTTP_NOT_FOUND);
        }

        return InvoiceResource::make($invoice);
    }
}
